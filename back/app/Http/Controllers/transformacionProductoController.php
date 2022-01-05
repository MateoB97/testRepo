<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TransformacionProducto;
use App\Producto;
use App\Inventario;

class transformacionProductoController extends Controller
{
    //
    public function store(Request $request)
    {
        $nuevoitem = new TransformacionProducto();
        $nuevoitem->producto_id_out = $request->producto_id_ent['id'];
        $nuevoitem->cantidad_out = $request->cantidad;
        $nuevoitem->producto_id_in = $request->producto_id_sal['id'];
        $nuevoitem->cantidad_in = $request->cantidad;
        $nuevoitem->save();
        self::afectarInventario($request->producto_id_ent['id'], $request->cantidad, 1);
        self::afectarInventario($request->producto_id_sal['id'], $request->cantidad, 0);
        return 'done';
    }

    public static function afectarInventario ($producto_id, $cantidad, $tipo_operacion) {

        if ($tipo_operacion == 1){
            $cantidad = floatval($cantidad);
        } else {
            $cantidad = -floatval($cantidad);
        }
        $producto = Producto::find($producto_id);
        $producto_id = 0;
        $prod_padre = Producto::where('codigo', $producto->cod_prod_padre)->get()->first();
        if($prod_padre) {
            $producto_id = $prod_padre->id;
        } else {
            $producto_id = $producto->id;
        }
        $itemInventario = Inventario::where('producto_id', $producto_id)->where('tipo_invent','!=',2)->get()->first();
        if ($itemInventario) {
            $itemInventario->cantidad += $cantidad;
            $itemInventario->save();
        } else {
            $nuevoInventario = new Inventario();
            $nuevoInventario->cantidad = $cantidad;
            $nuevoInventario->producto_id = $producto_id;
            $nuevoInventario->costo_promedio = 0;
            $nuevoInventario->tipo_invent = 1;
            $nuevoInventario->save();
        }
    }

}
