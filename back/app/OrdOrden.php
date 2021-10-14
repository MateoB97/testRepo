<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class OrdOrden extends Model
{
    protected $table = 'ord_ordenes';

    protected $fillable = [
        'subtotal',
        'consecutivo',
        'descuento',
        'ivatotal',
        'total',
        'saldo',
        'estado',
        'fecha_orden',
        'ord_tipo_orden_id',
        'tercero_sucursal_id',
        'user_id'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function todos(){
    return DB::table('ord_ordenes')
            ->select(
                'ord_ordenes.id as id',
                'ord_ordenes.consecutivo as consecutivo',
                'ord_ordenes.subtotal as subtotal',
                'ord_ordenes.descuento as descuento',
                'ord_ordenes.ivatotal as ivatotal',
                'ord_ordenes.total as total',
                'ord_ordenes.saldo as saldo',
                'ord_ordenes.estado as estado',
                'ord_ordenes.fecha_orden as fecha_orden',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'ord_tipo_ordenes.nombre as tipo',
                'users.name as usuario',
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'ord_ordenes.tercero_sucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('ord_tipo_ordenes','ord_tipo_ordenes.id', '=', 'ord_ordenes.ord_tipo_orden_id')
            ->join('users','users.id', '=', 'ord_ordenes.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }
}
