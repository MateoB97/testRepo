<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenCuadreCaja;
use Illuminate\Support\Facades\Auth;
use App\EgreEgreso;
use App\User;
use App\GenImpresora;
use App\EgreTipoEgreso;
use App\TerceroSucursal;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Carbon\Carbon;

class EgreEgresosController extends Controller
{
    public function index()
    {
        $index= EgreEgreso::todosConSucursalUsuarioTercero();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new EgreEgreso($request->all());

        if ( count(EgreEgreso::all()) > 0 ){
            $consecutivo = EgreEgreso::all()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1;
        }else{
            $nuevoItem->consecutivo = 1;
        }

        $nuevoItem->gen_cuadre_caja_id = GenCuadreCaja::where('estado', 1)->where('usuario_id', Auth::user()->id)->get()->first()->id;
        $nuevoItem->save();

        return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];
    }

    public function show($id)
    {
        $model = EgreEgreso::find($id);
        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = EgreEgreso::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = EgreEgreso::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function generatePDF($id)
    {

        $caractPorlinea = caracteres_linea_pos();

        $egreso = EgreEgreso::porId($id);

        $egreso->created_at = formato_fecha($egreso->created_at);

        $cuadre = GenCuadreCaja::find($egreso->gen_cuadre_caja_id);

        $user = User::find(Auth::user()->id);

        $tipoEgreso = EgreTipoEgreso::find($egreso->egre_tipo_egreso_id);

        $sucursal = TerceroSucursal::find($egreso->proveedor_id);
        $tercero = $sucursal->tercero;

        $empresa = GenEmpresa::find(1);
        $municipio = GenMunicipio::find($empresa->gen_municipios_id);
        $departamento = GenDepartamento::find($municipio->departamento_id);

        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);
        // $printer->setJustification(Printer::JUSTIFY_CENTER);

        // ENCABEZADO
        $etiqueta  = str_pad(strtoupper(eliminar_acentos($empresa->razon_social)), $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper(eliminar_acentos($empresa->nombre)), $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("NIT: ".$empresa->nit, $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper(eliminar_acentos($empresa->direccion)), $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper(eliminar_acentos($municipio->nombre))." - ".strtoupper(eliminar_acentos($departamento->nombre)), $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("TEL: ".$empresa->telefono, $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(eliminar_acentos($tipoEgreso->nombre), $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad('Consecutivo: '.$egreso->consecutivo, $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);
        $etiqueta .= "Cliente: ";
        $etiqueta .= str_pad(eliminar_acentos($tercero->nombre), $caractPorlinea - 9, " ", STR_PAD_BOTH);
        $etiqueta .= "Sucursal: ";
        $etiqueta .= str_pad(eliminar_acentos($sucursal->nombre), $caractPorlinea - 10, " ", STR_PAD_BOTH);
        $etiqueta .= "Fecha: ";
        $etiqueta .= str_pad($egreso->created_at, $caractPorlinea - 7, " ", STR_PAD_BOTH);
        $etiqueta .= "Valor: ";
        $etiqueta .= str_pad('$ '.number_format($egreso->valor, 0, ',', '.'), $caractPorlinea - 7, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("Descripcion: ", $caractPorlinea, " ", STR_PAD_RIGHT);
        $etiqueta .= str_pad('- '.eliminar_acentos($egreso->descripcion), $caractPorlinea * 2, " ", STR_PAD_RIGHT);
        $etiqueta .= "Usuario: ";
        $etiqueta .= str_pad($user->name, $caractPorlinea - 9, " ", STR_PAD_BOTH);

        $printer->text($etiqueta);
        $printer->feed(2);
        $printer->cut();
        $printer->close();

        return "Impresión Realizada";

    }

     public function generateReporteEgresosPorCuadre($id_cuadre)
    {
        $caractPorlinea = caracteres_linea_pos();

        $user = User::find(Auth::user()->id);

        $cuadre = GenCuadreCaja::porId($id_cuadre);

        $cuadre->created_at = Carbon::create(substr($cuadre->created_at, 0, 19))->format('d-m-Y H:i');

        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $tiposEgreso = EgreTipoEgreso::all();

        $etiqueta = str_pad('', $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad('REPORTE DE EGRESOS', $caractPorlinea, " ", STR_PAD_BOTH);

        $etiqueta .= 'Cuadre caja #';
        $etiqueta .= str_pad($id_cuadre, $caractPorlinea - 13, " ", STR_PAD_LEFT);

        $etiqueta .= 'Fecha cuadre:';
        $etiqueta .= str_pad($cuadre->created_at, $caractPorlinea - 13, " ", STR_PAD_LEFT);

        $etiqueta .= 'Usuario cuadre:';
        $etiqueta .= str_pad(User::find($cuadre->usuario_id)->name, $caractPorlinea - 15, " ", STR_PAD_LEFT);

        foreach ($tiposEgreso as $tipo) {

            $egresos = EgreEgreso::where('egre_tipo_egreso_id',$tipo->id)->where('gen_cuadre_caja_id', $id_cuadre)->get();

            if (count($egresos) > 0) {
                $total = 0;

                $etiqueta .= str_pad('', $caractPorlinea, "-", STR_PAD_RIGHT);
                $etiqueta .= str_pad('', $caractPorlinea, " ", STR_PAD_RIGHT);
                $etiqueta .= str_pad(strtoupper($tipo->nombre), $caractPorlinea, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad('', $caractPorlinea, " ", STR_PAD_RIGHT);

                foreach ($egresos as $egreso) {
                    $total = intval($egreso->valor) + $total;
                    $sucursal = TerceroSucursal::find($egreso->proveedor_id);
                    $tercero = $sucursal->tercero;

                    $etiqueta .= str_pad('Consec: '.$egreso->consecutivo, $caractPorlinea/2 , " ", STR_PAD_RIGHT);
                    $etiqueta .= str_pad('Valor: $'.number_format($egreso->valor, 0, ',', '.'), $caractPorlinea/2, " ", STR_PAD_LEFT);
                    $etiqueta .= str_pad('Tercero: '.$tercero->nombre, $caractPorlinea, " ", STR_PAD_RIGHT);
                    $etiqueta .= str_pad('Descripcion: '.$egreso->descripcion, $caractPorlinea*2, " ", STR_PAD_RIGHT);
                    $etiqueta .= str_pad('', $caractPorlinea, " ", STR_PAD_RIGHT);
                }

                $etiqueta .= 'Total ';
                $etiqueta .= str_pad('$'.number_format($total, 0, ',', '.'), $caractPorlinea-6, " ", STR_PAD_LEFT);
            }
        }

        $printer->text($etiqueta);
        $printer->feed(2);
        $printer->cut();
        $printer->close();

        return "Impresión Realizada";

    }
}
