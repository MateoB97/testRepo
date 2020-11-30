<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenTipoImpuesto;

class GenTipoImpuestoController extends Controller
{
    public function index()
    {
        $index= GenTipoImpuesto::orderBy('created_at', 'desc')->exclude(['created_at','updated_at'])->get();
        return $index;
    }

    public function activos()
    {
        $index= GenTipoImpuesto::where('activo','1')->orderBy('created_at', 'desc')->exclude(['created_at','updated_at'])->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenTipoImpuesto($request->all());
        $nuevoItem->save();

        return 'done';
    }
    
    public function show($id)
    {
        $model = GenTipoImpuesto::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenTipoImpuesto::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenTipoImpuesto::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }
    
    public function destroy($id)
    {
        $model = GenTipoImpuesto::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
