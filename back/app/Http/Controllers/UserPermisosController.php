<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserPermisos;


class UserPermisosController extends Controller
{
    public function index()
    {
        $index= UserPermisos::indexConCategorias();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new UserPermisos($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = UserPermisos::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = UserPermisos::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = UserPermisos::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function permisosAgrupadosCategorias () {

        $data = $this->index();

        $arrayCategorias = array();

        foreach ($data as $item) {
            $arrayCategorias[$item->categoria][] = $item;
        }

        return $arrayCategorias;

    }

}
