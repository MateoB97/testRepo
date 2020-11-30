<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComPivotCompraProducto;

class ComPivotCompraProductosController extends Controller
{
    public function porCompra($id)
    {
        $index = ComPivotCompraProducto::porCompra($id);
        return $index;
    }
}
