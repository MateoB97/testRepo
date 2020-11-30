<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenIva;

class GenIvaController extends Controller
{
    public function index()
    {
        $index= GenIva::all();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenIva($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenIva::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = GenIva::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenIva::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
