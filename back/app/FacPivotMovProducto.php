<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacPivotMovProducto extends Model
{
    protected $table = 'fac_pivot_mov_productos';

    protected $fillable = ['descporcentaje','iva','precio','cantidad','producto_id','fac_mov_id','puesto_tiquete','num_tiquete','num_linea_tiquete','gen_iva_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function porMovimiento($id){
    return DB::table('fac_pivot_mov_productos')
            ->select(
            	'fac_pivot_mov_productos.descporcentaje',
            	'fac_pivot_mov_productos.iva',
            	'fac_pivot_mov_productos.precio',
            	'fac_pivot_mov_productos.cantidad',
            	'productos.nombre as producto',
            	'productos.id as producto_id'
			)
            ->join('productos', 'fac_pivot_mov_productos.producto_id', '=', 'productos.id')
            ->where('fac_pivot_mov_productos.fac_mov_id','=', $id)
            ->orderBy('productos.nombre')
            ->get();
    }

    public static function lineasParaEditarFactura($id){
    return DB::table('fac_pivot_mov_productos')
            ->select(
                'fac_pivot_mov_productos.descporcentaje',
                'fac_pivot_mov_productos.iva as impuesto',
                'fac_pivot_mov_productos.precio',
                'fac_pivot_mov_productos.cantidad',
                'fac_pivot_mov_productos.gen_iva_id',
                'fac_pivot_mov_productos.num_tiquete',
                'fac_pivot_mov_productos.num_linea_tiquete',
                'fac_pivot_mov_productos.puesto_tiquete',
                'productos.nombre as producto_nombre',
                'productos.id as producto_id',
                'productos.codigo as producto_codigo',
            )
            ->join('productos', 'fac_pivot_mov_productos.producto_id', '=', 'productos.id')
            ->where('fac_pivot_mov_productos.fac_mov_id','=', $id)
            ->get();
    }

    public static function tiquetesDiaBascula($bascula, $fecha){
    return DB::table('fac_pivot_mov_productos')
            ->select(
                'fac_pivot_mov_productos.num_tiquete',
                'fac_pivot_mov_productos.num_linea_tiquete'
            )
            ->join('fac_movimientos', 'fac_movimientos.id', '=', 'fac_pivot_mov_productos.fac_mov_id')
            ->where('fac_movimientos.fecha_facturacion','=', $fecha)
            ->where('fac_pivot_mov_productos.puesto_tiquete','=', $bascula)
            ->orderBy('fac_pivot_mov_productos.id')
            ->get();
    }
}
