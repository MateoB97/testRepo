<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenBascula;

class GenBasculasController extends Controller
{
    public function index()
    {
        $index= GenBascula::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function activos()
    {
        $index= GenBascula::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenBascula($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenBascula::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenBascula::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenBascula::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenBascula::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
