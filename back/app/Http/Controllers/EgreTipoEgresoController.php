<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\EgreTipoEgreso;

class EgreTipoEgresoController extends Controller
{

    public function index()
    {
        $index= EgreTipoEgreso::all();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new EgreTipoEgreso($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = EgreTipoEgreso::find($id);
        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = EgreTipoEgreso::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = EgreTipoEgreso::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }
}
