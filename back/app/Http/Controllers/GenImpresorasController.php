<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenImpresora;

class GenImpresorasController extends Controller
{
    public function index()
    {
        $index= GenImpresora::where('activo', '!=', 0)->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function activos()
    {
        $index= GenImpresora::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenImpresora($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenImpresora::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenImpresora::find($id);

         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenImpresora::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenImpresora::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
