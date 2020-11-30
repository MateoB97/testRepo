<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacReciboCaja extends Model
{
    protected $table = 'fac_recibos_caja';

    protected $fillable = ['fac_tipo_rec_caja_id','tercero_sucursal_id','abono','ajuste','total','ajuste_observacion','consecutivo','fecha_recibo'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public function tipoRecCaja()
    {
    	// belongsTo(RelatedModel, foreignKey = tercero_id, keyOnRelatedModel = id)
    	return $this->belongsTo('App\FacTipoRecCaja', 'fac_tipo_rec_caja_id');
    }

    public function sucursal()
    {
    	// belongsTo(RelatedModel, foreignKey = tercero_id, keyOnRelatedModel = id)
    	return $this->belongsTo('App\TerceroSucursal', 'tercero_sucursal_id');
    }

    public static function todosConTipoSucursal(){
    return DB::table('fac_recibos_caja')
            ->select(
                'fac_recibos_caja.id As id',
                'fac_recibos_caja.consecutivo As consecutivo',
                'fac_recibos_caja.total as total',
                'fac_recibos_caja.fecha_recibo as fecha_recibo',
                'fac_recibos_caja.estado as estado',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'fac_tipo_rec_caja.nombre as tipo',
                'users.name as usuario'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_recibos_caja.tercero_sucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_rec_caja','fac_tipo_rec_caja.id', '=', 'fac_recibos_caja.fac_tipo_rec_caja_id')
            ->join('gen_cuadre_caja','gen_cuadre_caja.id', '=', 'fac_recibos_caja.gen_cuadre_caja_id')
            ->join('users','users.id', '=', 'gen_cuadre_caja.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }

    public static function RecibosPorFecha($tipodoc, $fecha_inicial, $fecha_final, $sucursal_id){
        return DB::table('fac_recibos_caja')
            ->select(
                'fac_recibos_caja.id As id',
                'fac_recibos_caja.consecutivo As consecutivo',
                'fac_recibos_caja.total as total',
                'fac_recibos_caja.total as subtotal',
                'fac_recibos_caja.total as saldo',
                'fac_recibos_caja.fecha_recibo as fecha',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_rec_caja.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_recibos_caja.tercero_sucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_rec_caja','fac_tipo_rec_caja.id', '=', 'fac_recibos_caja.fac_tipo_rec_caja_id')
            ->where('fac_tipo_rec_caja_id', $tipodoc)
            ->whereBetween('fac_recibos_caja.fecha_recibo', [$fecha_inicial, $fecha_final])
            ->where('fac_recibos_caja.estado', 1)
            ->when($sucursal_id, function ($query, $sucursal_id) {
                return $query->where('tercero_sucursales.id', $sucursal_id);
            })
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->get();
    }

    public static function RecibosPorFechaT80($fecha_inicial, $fecha_final){
        return DB::table('fac_recibos_caja')
            ->select(
                'fac_recibos_caja.id As id',
                'fac_recibos_caja.consecutivo As consecutivo',
                'fac_recibos_caja.total as total',
                'fac_recibos_caja.total as subtotal',
                'fac_recibos_caja.total as saldo',
                'fac_recibos_caja.fecha_recibo as fecha',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_rec_caja.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_recibos_caja.tercero_sucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_rec_caja','fac_tipo_rec_caja.id', '=', 'fac_recibos_caja.fac_tipo_rec_caja_id')
            ->whereBetween('fac_recibos_caja.fecha_recibo', [$fecha_inicial, $fecha_final])
            ->where('fac_recibos_caja.estado', 1)
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->get();
    }
}
