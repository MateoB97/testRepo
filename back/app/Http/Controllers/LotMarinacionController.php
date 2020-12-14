<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LotMarinacion;

class LotMarinacionController extends Controller
{

    public function index()
    {
        $list = LotMarinacion::pesosTotalesLote();
        return $list;

    }


    public function store(Request $request)
    {
        
        //verifico que no se halla borrado ninguno en el nuevo guardado, si se borro alguno, se elimina
        $existentes = LotMarinacion::where('lotProgramacion_id',$request->programacion_id)->get();

        foreach ($existentes as $existente) {
            $existente->delete();
        }


        foreach ($request->productos as $item) {

            $newItem = new  LotMarinacion($item);
            $newItem->lotProgramacion_id = $request->programacion_id;
            $newItem->save();

        }
        
        return 'done';

    }

    public function GetData($programacion_id)
    {
        $data = LotMarinacion::productosPorProgramacion($programacion_id);
        return $data;
    }

}
