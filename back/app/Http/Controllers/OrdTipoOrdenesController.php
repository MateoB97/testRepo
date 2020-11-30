<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OrdTipoOrden;

class OrdTipoOrdenesController extends Controller
{
    public function index()
    {
        $index= OrdTipoOrden::all();

        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new OrdTipoOrden($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = OrdTipoOrden::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = OrdTipoOrden::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = OrdTipoOrden::find($id);
        $v = $model->delete();

        if ($v) {
            return 'done';
        } else {
           return 'Imposible eliminar'; 
        }
    }
}
