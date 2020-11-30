<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenEmpresa;
use App\TerceroSucursal;

class GenEmpresaController extends Controller
{
    public function index()
    {
        $index= GenEmpresa::orderBy('created_at', 'desc')->get()->first();

        $index->fact_grupo = ($index->fact_grupo == 1) ? true : false;

        return $index;
    }

    public function show($id)
    {
        $model = GenEmpresa::find($id);
        $model->fact_grupo = ($model->fact_grupo == 1) ? true : false;

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = GenEmpresa::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function terceroPOS()
    {
        $model = GenEmpresa::find(1);

        return $model->tercero_sucursal_pos_id;
    }

    public function obtenerMAC()
    {
        echo exec('getmac');
        
    }

}
