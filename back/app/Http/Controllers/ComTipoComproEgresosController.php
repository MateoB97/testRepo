<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComTipoComproEgreso;

class ComTipoComproEgresosController extends Controller
{
    public function index()
    {
        $list = ComTipoComproEgreso::conDocRelacionado();
        return $list;
    }

    public function store(Request $request)
    {
        $nuevoItem = new ComTipoComproEgreso($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = ComTipoComproEgreso::find($id);
        return $model;
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
