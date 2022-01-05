<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LotProgramacion;
use App\LotPesosProgramacion;

class LotProgramacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function programacionLotesAbiertos($producto_empacado)
    {
        $data = explode(',', $producto_empacado);
        $list = LotProgramacion::todosConLoteAbierto($data);
        return $list;
    }

    public function programacionLotesAbiertosPorGrupo($id, $producto_empacado)
    {
        $list = LotProgramacion::todosConLoteAbiertoPorGrupo($id, $producto_empacado);
        return $list;
    }

    public function programacionPorId($id, $producto_empacado)
    {
        $producto_empacado = ($producto_empacado == 'null') ? false : $producto_empacado;
        $list = LotProgramacion::conLoteAbiertoPorId($id, $producto_empacado);
        return $list;
    }

    public function programacionPreliquidacion($id, $producto_empacado){
        $producto_empacado = ($producto_empacado == 'null') ? false : $producto_empacado;
        $list = LotProgramacion::conLoteAbiertoPorIdPreLiquidacion($id, $producto_empacado);
        return $list;
    }

    public function pesosPorProgramacion($id)
    {
        $list = LotPesosProgramacion::where('lotProgramacion_id',$id)->get();
        return $list;
    }

    public function storePesoProgramacion(Request $request)
    {
        $programacion = LotProgramacion::find($request->lotProgramacion_id);

        if ($programacion->estado != 2) {
            $nuevoItem = new LotPesosProgramacion($request->all());
            $nuevoItem->save();

            return 'done';
        } else {
            return 'No puede guardar el peso, la programación ya se encuentra liquidada.';
        }


    }

    public function deletePesoProgramacion($id)
    {

        $item = LotPesosProgramacion::find($id);

        $programacion = LotProgramacion::find($item->lotProgramacion_id);

        if ($programacion->estado != 2) {
            $item->delete();
            return 'done';
        } else {
            return 'No puede eliminar el peso, la programación ya se encuentra liquidada.';
        }
    }


}
