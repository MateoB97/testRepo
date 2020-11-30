<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TerceroSucursal;

class TerceroSucursalController extends Controller
{
    public function index()
    {
        $index= TerceroSucursal::all();
        return $index;
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $nuevoItem = new TerceroSucursal($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = TerceroSucursal::find($id);

        return $model;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        $model = TerceroSucursal::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function terceroFilter($id){
        $list = TerceroSucursal::where('tercero_id', $id)->get();
        return $list;
    }
}
