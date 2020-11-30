<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalPivotSalProducto extends Model
{
    protected $table = 'sal_pivot_sal_producto';

    protected $fillable = [
    	'cantidad',
    	'producto_id',
    	'sal_mercancia_id'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
    
    public static function pesoDespachoParaFactura($id, $lista_precio){
    return DB::table('sal_pivot_sal_producto')
            ->select(
                'productos.id as producto_id',
                'productos.codigo As codigo',
                'sal_pivot_sal_producto.cantidad as peso',
                'prod_pivot_lista_productos.precio'
            )
            ->join('sal_mercancias', 'sal_mercancias.id', '=', 'sal_pivot_sal_producto.sal_mercancia_id')
            ->join('productos', 'sal_pivot_sal_producto.producto_id', '=', 'productos.id')
            ->join('prod_pivot_lista_productos', 'prod_pivot_lista_productos.producto_id', '=', 'productos.id')
            ->join('tercero_sucursales', 'tercero_sucursales.id', '=', 'sal_mercancias.terceroSucursal_id')
            ->where('prod_pivot_lista_productos.prodListaPrecio_id', $lista_precio)
            ->where('sal_pivot_sal_producto.sal_mercancia_id','=', $id)
            ->get();
    }
}
