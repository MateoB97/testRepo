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
use App\ReportesT80;

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

        $user = User::find(Auth::user()->id);
        $t80 = new ReportesT80();
        $str = '';
        $ingreso = GenCuadreIngresoEfectivo::porId($id);
        // dd($ingreso);
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $t80 = new ReportesT80();
        $str = '';

        $str .= $t80->posLineaBlanco(' ');
        $str .=$t80->printLogoT80($printer);
        $str .= $t80->posLineaBlanco('-');
        $str .= $t80->posHeaderEmpresa();
        $str .= $t80->posLineaBlanco();

        $str .= $t80->posLineaCentro('Ingreso de Efectivo', '-');
        $str .= $t80->posLineaCentro('Consecutivo: '. $ingreso->id);
        $str .= $t80->posLineaGuion();
        $str.= $t80->posDosItemsExtremos('Fecha:', $ingreso->created_at);
        $str.= $t80->posDosItemsExtremos('Valor:', $ingreso->valor);
        if($ingreso->descripcion) {
            $str .= $t80->posLineaCentro("nota", "-");
            $empresa = GenEmpresa::find(1);
            if(strlen($ingreso->descripcion) >= 49){
            $num_partes = strlen($ingreso->descripcion) / intval($empresa->cantidad_caracteres);
            $dataNotas = $t80->divString($ingreso->descripcion, $num_partes);
            if($dataNotas) {
                foreach($dataNotas as $nota) {
                    $str.= $t80->posLineaDerecha($nota);
                }
            }
            $str .= $t80->posLineaGuion();
        } else {
            $str.= $t80->posLineaDerecha($ingreso->descripcion);
            $str .= $t80->posLineaGuion();
        }
        }

        $str.= $t80->posFooterSgc();

        $printer->text($str);
        $printer->feed(1);
        $printer->cut();
        $printer->close();

        return "Impresión Realizada";

    }


}
