<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserPermisosCategorias;

class UserPermisosCategoriasController extends Controller
{
    public function index()
    {
        $index= UserPermisosCategorias::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new UserPermisosCategorias($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = UserPermisosCategorias::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = UserPermisosCategorias::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = UserPermisosCategorias::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
