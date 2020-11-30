<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComPivotCompraProducto extends Model
{
	protected $table = 'com_pivot_compra_producto';

    protected $fillable = ['descporcentaje','iva','precio','cantidad','producto_id','com_compras_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function porCompra($id){
    return DB::table('com_pivot_compra_producto')
            ->select(
            	'com_pivot_compra_producto.descporcentaje',
            	'com_pivot_compra_producto.iva',
            	'com_pivot_compra_producto.precio',
            	'com_pivot_compra_producto.cantidad',
            	'productos.nombre as producto',
            	'productos.id as producto_id'
			)
            ->join('productos', 'com_pivot_compra_producto.producto_id', '=', 'productos.id')
            ->where('com_pivot_compra_producto.com_compras_id','=', $id)
            ->get();
    }

}
