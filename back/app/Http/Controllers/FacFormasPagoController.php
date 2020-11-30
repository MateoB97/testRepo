<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacFormaPago;

class FacFormasPagoController extends Controller
{

    public function index()
    {
        $index= FacFormaPago::all();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new FacFormaPago($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        //
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
