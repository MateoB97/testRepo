<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenVendedor;

class GenVendedoresController extends Controller
{
    public function index()
    {
        $index= GenVendedor::all();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenVendedor($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenVendedor::find($id);
        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = GenVendedor::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenVendedor::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
