<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenImpuesto;
use App\GenTipoImpuesto;


class GenImpuestoController extends Controller
{
    public function index()
    {
        $index= GenImpuesto::todosConTipos();

        return $index;
    }

    public function activos()
    {
        $index= GenImpuesto::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenImpuesto($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenImpuesto::find($id);

        $model->genTipoImpuesto_id = $model->GenTipoImpuesto;

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenImpuesto::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenImpuesto::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }
    
    public function destroy($id)
    {
        $model = GenImpuesto::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
