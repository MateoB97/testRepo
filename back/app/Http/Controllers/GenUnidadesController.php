<?php

namespace App\Http\Controllers;

use App\GenUnidades;
use Illuminate\Http\Request;

class GenUnidadesController extends Controller
{
    public function index()
    {
        $index= GenUnidades::orderBy('created_at', 'desc')->exclude(['created_at','updated_at'])->get();
        return $index;
    }

    public function activos()
    {
        $index= GenUnidades::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenUnidades($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenUnidades::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenUnidades::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenUnidades::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenUnidades::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
