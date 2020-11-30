<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacPivotMovProducto;

class FacPivotMovProductosController extends Controller
{
    public function index()
    {
        //
    }

    public function porMovimiento($id)
    {
        $index = FacPivotMovProducto::porMovimiento($id);
        return $index;
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
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
        //
    }
}
