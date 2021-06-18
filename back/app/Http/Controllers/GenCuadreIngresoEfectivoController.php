<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenCuadreCaja;
use Illuminate\Support\Facades\Auth;
use App\GenCuadreIngresoEfectivo;
use App\User;
use App\GenEmpresa;
use App\GenMunicipio;
use App\GenDepartamento;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Carbon\Carbon;
use App\GenImpresora;

class GenCuadreIngresoEfectivoController extends Controller
{
    public function index()
    {
        $index= GenCuadreIngresoEfectivo::todosConUsuario();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenCuadreIngresoEfectivo($request->all());

        if ( count(GenCuadreIngresoEfectivo::all()) > 0 ){
            $consecutivo = GenCuadreIngresoEfectivo::all()->last();
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
        $model = GenCuadreIngresoEfectivo::find($id);
        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = GenCuadreIngresoEfectivo::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenCuadreIngresoEfectivo::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function generatePDF($id)
    {

        $caractPorlinea = caracteres_linea_pos();

        $ingreso = GenCuadreIngresoEfectivo::porId($id);
        $ingreso->created_at = formato_fecha($ingreso->created_at);

        $cuadre = GenCuadreCaja::find($ingreso->gen_cuadre_caja_id);

        $user = User::find(Auth::user()->id);

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
        // ingreso
        $etiqueta .= str_pad('Consecutivo: '.$ingreso->consecutivo, $caractPorlinea, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);
        $etiqueta .= "Fecha: ";
        $etiqueta .= str_pad($ingreso->created_at, $caractPorlinea - 7, " ", STR_PAD_BOTH);
        $etiqueta .= "Valor: ";
        $etiqueta .= str_pad('$ '.number_format($ingreso->valor, 0, ',', '.'), $caractPorlinea - 7, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("Descripcion: ", $caractPorlinea, " ", STR_PAD_RIGHT);
        $etiqueta .= str_pad('- '.eliminar_acentos($ingreso->descripcion), $caractPorlinea * 2, " ", STR_PAD_RIGHT);
        $etiqueta .= "Usuario: ";
        $etiqueta .= str_pad($user->name, $caractPorlinea - 9, " ", STR_PAD_BOTH);

        $printer->text($etiqueta);
        $printer->feed(2);
        $printer->cut();
        $printer->close();

        return "Impresión Realizada";

    }


}
