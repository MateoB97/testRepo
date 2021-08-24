<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class ComCompra extends Model
{
    protected $table = 'com_compras';

    protected $fillable = [
        'subtotal',
        'doc_referencia',
        'consecutivo',
        'descuento',
        'ivatotal',
        'total',
        'saldo',
        'estado',
        'fecha_vencimiento',
        'fecha_compra',
        'com_tipo_compras_id',
        'proveedor_id',
        'usuario_id'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function todosConTipoSucursalGrupoTipo(){
    return DB::table('com_compras')
            ->select(
                'com_compras.id As id',
                'com_compras.consecutivo As consecutivo',
                'com_compras.subtotal as subtotal',
                'com_compras.descuento as descuento',
                'com_compras.ivatotal as ivatotal',
                'com_compras.total as total',
                'com_compras.saldo as saldo',
                'com_compras.estado as estado',
                'com_compras.fecha_compra as fecha_compra',
                'com_compras.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'com_tipo_compras.nombre as tipo',
                'users.name as usuario',
                'com_tipo_compras.naturaleza'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'com_compras.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('com_tipo_compras','com_tipo_compras.id', '=', 'com_compras.com_tipo_compras_id')
            ->join('users','users.id', '=', 'com_compras.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }

    public static function comprasPendientesPorSucursalYTipo($sucursal_id, $tipodoc_id){
    return DB::table('com_compras')
            ->select(
                'com_compras.id As id',
                'com_compras.consecutivo As consecutivo',
                'com_compras.subtotal as subtotal',
                'com_compras.descuento as descuento',
                'com_compras.ivatotal as ivatotal',
                'com_compras.total as total',
                'com_compras.saldo as saldo',
                'com_compras.estado as estado',
                'com_compras.fecha_compra as fecha_compra',
                'com_compras.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'com_tipo_compras.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'com_compras.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('com_tipo_compras','com_tipo_compras.id', '=', 'com_compras.com_tipo_compras_id')
            ->where('com_tipo_compras.naturaleza', 1)
            ->where('tercero_sucursales.id', $sucursal_id)
            ->where('com_tipo_compras.id', $tipodoc_id)
            ->where('com_compras.estado', 1)
            ->orderBy('id','asc')
            ->get();
    }

    public static function cuentasPorPagar(){
    return DB::table('com_compras')
            ->select(
                'com_compras.id As id',
                'com_compras.consecutivo As consecutivo',
                'com_compras.total as total',
                'com_compras.saldo as saldo',
                'com_compras.estado as estado',
                'com_compras.fecha_compra as fecha_compra',
                'com_compras.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'com_tipo_compras.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'com_compras.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('com_tipo_compras','com_tipo_compras.id', '=', 'com_compras.com_tipo_compras_id')
            ->where('com_tipo_compras.naturaleza', 1)
            ->where('com_compras.estado', 1)
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->orderBy('com_compras.fecha_compra', 'asc')
            ->get();
    }

    public static function cuentasPorPagarxTercero($tercero_id){
    return DB::table('com_compras')
            ->select(
                'com_compras.id As id',
                'com_compras.consecutivo As consecutivo',
                'com_compras.total as total',
                'com_compras.saldo as saldo',
                'com_compras.estado as estado',
                'com_compras.fecha_compra as fecha_compra',
                'com_compras.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'com_tipo_compras.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'com_compras.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('com_tipo_compras','com_tipo_compras.id', '=', 'com_compras.com_tipo_compras_id')
            ->where('com_tipo_compras.naturaleza', 1)
            ->where('com_compras.estado', 1)
            ->where('terceros.id', $tercero_id)
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->orderBy('com_compras.fecha_compra', 'asc')
            ->get();
    }

    public static function comprasNetasPorFecha($tipodoc, $fecha_inicial, $fecha_final){
        return DB::table('com_compras')
        ->select( DB::raw( 'sum(com_compras.total) as total, sum(com_compras.subtotal) as subtotal, sum(com_compras.ivatotal) as ivatotal, sum(com_compras.descuento) as descuento' ))
        ->where('com_tipo_compras_id', $tipodoc)
        ->whereBetween('com_compras.fecha_compra', [$fecha_inicial, $fecha_final])
        ->get();
    }

    public static function devolucionesComprasNetasPorFecha($tipodoc, $fecha_inicial, $fecha_final){
        return DB::table('com_compras')
        ->select( DB::raw( 'sum(com_compras.total) as total, sum(com_compras.subtotal) as subtotal, sum(com_compras.ivatotal) as ivatotal, sum(com_compras.descuento) as descuento' ))
        ->where('com_tipo_compras_id', $tipodoc)
        ->where('estado', 3)
        ->whereBetween('com_compras.fecha_compra', [$fecha_inicial, $fecha_final])
        ->get();
    }

    public static function proveedorPorProgramacion($id){
    return DB::table('com_compras')
            ->select('terceros.nombre As proveedor','tercero_sucursales.direccion as sucursal')
            ->join('tercero_sucursales', 'com_compras.proveedor_id', '=', 'tercero_sucursales.id')
            ->join('terceros', 'tercero_sucursales.tercero_id', '=', 'terceros.id')
            ->join('lotes', 'lotes.com_compras_id', '=', 'com_compras.id')
            ->join('lot_programaciones', 'lot_programaciones.lote_id', '=', 'lotes.id')
            ->where('lot_programaciones.id',$id)
            ->orderBy('lot_programaciones.id','desc')
            ->get();
    }

}
