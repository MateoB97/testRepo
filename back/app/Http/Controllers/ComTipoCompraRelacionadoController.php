<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComTipoCompraRelacionado;
use App\ComCompra;

class ComTipoCompraRelacionadoController extends Controller
{
    public function getItemsMovPrimarios($id)
    {
        $sec = ComTipoCompraRelacionado::where('com_tipo_compra_sec_id', $id)->get()->first();
        $item = ComCompra::where('com_tipo_compras_id', $sec->com_tipo_compra_prim_id)->where('estado','!=', 3)->get();
        return $item;
    }
}
