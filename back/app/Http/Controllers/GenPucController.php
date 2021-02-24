<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenPuc;

class GenPucController extends Controller
{
    public function index()
    {
        $index= GenPuc::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function activos()
    {
        $index= GenPuc::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenPuc($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenPuc::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenPuc::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenPuc::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenPuc::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

}
