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
    // return DB::table('ord_ordenes')
        return DB::select("
            select top(100)
                ord_ordenes.id as id,
                ord_ordenes.consecutivo as consecutivo,
                ord_ordenes.subtotal as subtotal,
                ord_ordenes.descuento as descuento,
                ord_ordenes.ivatotal as ivatotal,
                ord_ordenes.total as total,
                ord_ordenes.saldo as saldo,
                ord_ordenes.estado as estado,
                ord_ordenes.fecha_orden as fecha_orden,
                tercero_sucursales.nombre as sucursal,
                terceros.nombre as tercero,
                ord_tipo_ordenes.nombre as tipo,
                users.name as usuario,
                ord_ordenes.autorizacion as autorizacion_id,
                CASE WHEN ord_ordenes.autorizacion = 1 THEN 'Autorizado' when ord_ordenes.autorizacion = 2 THEN 'Pendiente' ELSE 'Rechazado' END as autorizacion
            from ord_ordenes
            inner join tercero_sucursales  on ord_ordenes.tercero_sucursal_id = tercero_sucursales.id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            inner join ord_tipo_ordenes on ord_ordenes.ord_tipo_orden_id = ord_tipo_ordenes.id
            inner join users on ord_ordenes.usuario_id = users.id
            order by ord_ordenes.id desc
        ");
            // ->select(
            //     'ord_ordenes.id as id',
            //     'ord_ordenes.consecutivo as consecutivo',
            //     'ord_ordenes.subtotal as subtotal',
            //     'ord_ordenes.descuento as descuento',
            //     'ord_ordenes.ivatotal as ivatotal',
            //     'ord_ordenes.total as total',
            //     'ord_ordenes.saldo as saldo',
            //     'ord_ordenes.estado as estado',
            //     'ord_ordenes.fecha_orden as fecha_orden',
            //     'tercero_sucursales.nombre as sucursal',
            //     'terceros.nombre as tercero',
            //     'ord_tipo_ordenes.nombre as tipo',
            //     'users.name as usuario'
            // )
            // ->join('tercero_sucursales','tercero_sucursales.id', '=', 'ord_ordenes.tercero_sucursal_id')
            // ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            // ->join('ord_tipo_ordenes','ord_tipo_ordenes.id', '=', 'ord_ordenes.ord_tipo_orden_id')
            // ->join('users','users.id', '=', 'ord_ordenes.usuario_id')
            // ->orderBy('id','desc')
            // ->get();
    }

    public static function ordenesPorAutorizacion($auth){
        return DB::select("
            select top(100)
                ord_ordenes.id as id,
                ord_ordenes.consecutivo as consecutivo,
                ord_ordenes.subtotal as subtotal,
                ord_ordenes.descuento as descuento,
                ord_ordenes.ivatotal as ivatotal,
                ord_ordenes.total as total,
                ord_ordenes.saldo as saldo,
                ord_ordenes.estado as estado,
                ord_ordenes.fecha_orden as fecha_orden,
                tercero_sucursales.nombre as sucursal,
                terceros.nombre as tercero,
                ord_tipo_ordenes.nombre as tipo,
                users.name as usuario,
                ord_ordenes.autorizacion as autorizacion_id,
                CASE WHEN ord_ordenes.autorizacion = 1 THEN 'Autorizado' when ord_ordenes.autorizacion = 2 THEN 'Pendiente' ELSE 'Rechazado' END as autorizacion
            from ord_ordenes
            inner join tercero_sucursales  on ord_ordenes.tercero_sucursal_id = tercero_sucursales.id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            inner join ord_tipo_ordenes on ord_ordenes.ord_tipo_orden_id = ord_tipo_ordenes.id
            inner join users on ord_ordenes.usuario_id = users.id
            where ord_ordenes.autorizacion = '$auth'
            order by ord_ordenes.id desc
        ");
    }

    public static function ordenesPorTipoOrden($tipo_id){
        return DB::select("
            select top(100)
                ord_ordenes.id as id,
                ord_ordenes.consecutivo as consecutivo,
                ord_ordenes.subtotal as subtotal,
                ord_ordenes.descuento as descuento,
                ord_ordenes.ivatotal as ivatotal,
                ord_ordenes.total as total,
                ord_ordenes.saldo as saldo,
                ord_ordenes.estado as estado,
                ord_ordenes.fecha_orden as fecha_orden,
                tercero_sucursales.nombre as sucursal,
                terceros.nombre as tercero,
                ord_tipo_ordenes.nombre as tipo,
                users.name as usuario,
                ord_ordenes.autorizacion as autorizacion_id,
                CASE WHEN ord_ordenes.autorizacion = 1 THEN 'Autorizado' when ord_ordenes.autorizacion = 2 THEN 'Pendiente' ELSE 'Rechazado' END as autorizacion
            from ord_ordenes
            inner join tercero_sucursales  on ord_ordenes.tercero_sucursal_id = tercero_sucursales.id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            inner join ord_tipo_ordenes on ord_ordenes.ord_tipo_orden_id = ord_tipo_ordenes.id
            inner join users on ord_ordenes.usuario_id = users.id
            where ord_ordenes.ord_tipo_orden_id = '$tipo_id'
            order by ord_ordenes.id desc
        ");
    }
}
