<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class FacMovimiento extends Model
{
    protected $table = 'fac_movimientos';

    protected $fillable = [
        'subtotal',
        'consecutivo',
        'descuento',
        'ivatotal',
        'total',
        'saldo',
        'estado',
        'fecha_vencimiento',
        'fecha_facturacion',
        'fac_tipo_doc_id',
        'cliente_id',
        'gen_cuadre_caja_id',
        'cufe',
        'qr',
        'estado_fe',
        'nota',
        'prod_grupo_id',
        'sal_mercancia_consec',
        'afectar_inventario'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public function tipoDoc()
    {
        // belongsTo(RelatedModel, foreignKey = tercero_id, keyOnRelatedModel = id)
        return $this->belongsTo('App\FacTipoDoc', 'fac_tipo_doc_id');
    }

    public static function todosConTipoSucursalGrupoTipo(){
    return DB::table('fac_movimientos')
            ->select(
            	'fac_movimientos.id As id',
            	'fac_movimientos.consecutivo As consecutivo',
            	'fac_movimientos.subtotal as subtotal',
            	'fac_movimientos.descuento as descuento',
            	'fac_movimientos.ivatotal as ivatotal',
            	'fac_movimientos.total as total',
            	'fac_movimientos.saldo as saldo',
            	'fac_movimientos.estado as estado',
            	'fac_movimientos.fecha_facturacion as fecha_facturacion',
                'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
            	'fac_movimientos.cufe as cufe',
                'tercero_sucursales.nombre as sucursal',
            	'tercero_sucursales.email as email',
            	'terceros.nombre as tercero',
                'fac_tipo_doc.nombre as tipo',
                'users.name as usuario',
                'fac_tipo_doc.naturaleza',
                'fac_movimientos.estado_fe as estado_fe',
                'fac_tipo_doc.soenac_tipo_doc_api_id as soenac_tipo_doc'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->join('gen_cuadre_caja','gen_cuadre_caja.id', '=', 'fac_movimientos.gen_cuadre_caja_id')
            ->join('users','users.id', '=', 'gen_cuadre_caja.usuario_id')
            ->orderBy('fac_movimientos.id','desc')
            // ->take(5000)
            ->get();
    }

    public static function facturasPendientesPorSucursal($sucursal_id){
    return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.subtotal as subtotal',
                'fac_movimientos.descuento as descuento',
                'fac_movimientos.ivatotal as ivatotal',
                'fac_movimientos.total as total',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha_facturacion',
                'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_tipo_doc.naturaleza', 1)
            ->where('tercero_sucursales.id', $sucursal_id)
            ->where('fac_movimientos.estado', 1)
            ->orderBy('id','asc')
            ->get();
    }

    public static function facturasPendientesPorSucursalYTipo($sucursal_id, $tipodoc_id){
    return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.subtotal as subtotal',
                'fac_movimientos.descuento as descuento',
                'fac_movimientos.ivatotal as ivatotal',
                'fac_movimientos.total as total',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha_facturacion',
                'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_tipo_doc.naturaleza', 1)
            ->where('tercero_sucursales.id', $sucursal_id)
            ->whereIn('fac_tipo_doc.id', $tipodoc_id)
            ->where('fac_movimientos.estado', 1)
            ->orderBy('fac_movimientos.consecutivo','asc')
            ->get();
    }

    public static function sumMovCuadre ($tipodoc, $cuadre) {
        return DB::table('fac_movimientos')
        ->select( DB::raw( 'sum(fac_movimientos.total) as total, sum(fac_movimientos.subtotal) as subtotal, sum(fac_movimientos.ivatotal) as ivatotal, sum(fac_movimientos.descuento) as descuento' ))
        ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
        ->where('fac_tipo_doc_id', $tipodoc)
        ->where('fac_tipo_doc.traslado', 0)
        ->where('gen_cuadre_caja_id', $cuadre)
        ->get();
    }

    public static function sumMovCuadreDevoluciones ($tipodoc, $cuadre) {
        return DB::table('fac_movimientos')
        ->select( DB::raw( 'sum(fac_movimientos.total) as total, sum(fac_movimientos.subtotal) as subtotal, sum(fac_movimientos.ivatotal) as ivatotal, sum(fac_movimientos.descuento) as descuento' ))
        ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
        ->where('fac_tipo_doc_id', $tipodoc)
        ->where('fac_tipo_doc.traslado', 0)
        ->where('gen_cuadre_caja_id', $cuadre)
        ->where('fac_movimientos.estado', 3)
        ->get();
    }

    public static function saldosEnCartera(){
    return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.total as total',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha_facturacion',
                'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_tipo_doc.naturaleza', 1)
            ->where('fac_movimientos.estado', 1)
            // ->whereIn('terceros.documento', ['1035861401','1017217002'])
            // ->whereIn('terceros.documento', ['98504804','901238930'])
            ->orderBy('terceros.nombre','asc')
            ->orderBy('tercero_sucursales.nombre','asc')
            ->orderBy('fac_movimientos.created_at','asc')
            ->get();
    }

    public static function saldosEnCarteraxSucursal($sucursal_id){
    return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.total as total',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha_facturacion',
                'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_tipo_doc.naturaleza', 1)
            ->where('fac_movimientos.estado', 1)
            ->where('tercero_sucursales.id', $sucursal_id)
            ->orderBy('fac_movimientos.created_at','asc')
            ->get();
    }

    public static function ventasNetasPorFecha($tipodoc, $fecha_inicial, $fecha_final){
        return DB::table('fac_movimientos')
        ->select( DB::raw( 'sum(fac_movimientos.total) as total, sum(fac_movimientos.subtotal) as subtotal, sum(fac_movimientos.ivatotal) as ivatotal, sum(fac_movimientos.descuento) as descuento' ))
        ->where('fac_tipo_doc_id', $tipodoc)
        ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
        ->get();
    }

    public static function ventasPorFecha( $tipodoc, $fecha_inicial, $fecha_final, $sucursal_id){
        return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.total as total',
                'fac_movimientos.subtotal as subtotal',
                'fac_movimientos.ivatotal as ivatotal',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.descuento as descuento',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_tipo_doc_id', $tipodoc)
            ->where('fac_movimientos.estado', '!=', 3)
            // ->whereNotIn('fac_tipo_doc_id', [1,2])
            ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
            ->when($sucursal_id, function ($query, $sucursal_id) {
                return $query->where('tercero_sucursales.id', $sucursal_id);
            })
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->get();
    }

    public static function ventasPorFechaT80($fecha_inicial, $fecha_final){
        return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.total as total',
                'fac_movimientos.subtotal as subtotal',
                'fac_movimientos.ivatotal as ivatotal',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.descuento as descuento',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_movimientos.estado', '!=', 3)
            ->whereNotIn('fac_tipo_doc_id', [1,2])
            ->where('fac_tipo_doc.nombre', 'not like','%hab%')
            ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->get();
    }

    public static function DevolucionesPorFecha($tipodoc, $fecha_inicial, $fecha_final, $sucursal_id){
        return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.total as total',
                'fac_movimientos.subtotal as subtotal',
                'fac_movimientos.ivatotal as ivatotal',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.descuento as descuento',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.nombre as tipo',
                'fac_tipo_doc.id as tipoid'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_tipo_doc_id', $tipodoc)
            ->where('fac_movimientos.estado', 3)
            ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
            ->when($sucursal_id, function ($query, $sucursal_id) {
                return $query->where('tercero_sucursales.id', $sucursal_id);
            })
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->get();
    }

    public static function DevolucionesPorFechaT80($fecha_inicial, $fecha_final){
        return DB::table('fac_movimientos')
            ->select(
                'fac_movimientos.id As id',
                'fac_movimientos.consecutivo As consecutivo',
                'fac_movimientos.total as total',
                'fac_movimientos.subtotal as subtotal',
                'fac_movimientos.ivatotal as ivatotal',
                'fac_movimientos.saldo as saldo',
                'fac_movimientos.descuento as descuento',
                'fac_movimientos.estado as estado',
                'fac_movimientos.fecha_facturacion as fecha',
                'tercero_sucursales.nombre as sucursal',
                'tercero_sucursales.id as sucursal_id',
                'tercero_sucursales.direccion as direccion',
                'tercero_sucursales.telefono as telefono',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_movimientos.estado', 3)
            ->where('fac_tipo_doc.nombre', 'not like','%hab%')
            ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
            ->orderBy('terceros.documento','asc')
            ->orderBy('tercero_sucursales.id','asc')
            ->get();
    }

    public static function devolucionesVentasNetasPorFecha($tipodoc, $fecha_inicial, $fecha_final){
        return DB::table('fac_movimientos')
        ->select( DB::raw( 'sum(fac_movimientos.total) as total, sum(fac_movimientos.subtotal) as subtotal, sum(fac_movimientos.ivatotal) as ivatotal, sum(fac_movimientos.descuento) as descuento' ))
        ->where('fac_tipo_doc_id', $tipodoc)
        ->where('estado', 3)
        ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
        ->get();
    }

    //Notas relacionadas a una factura
    public static function allNotas()
    {
        return  DB::table('fac_movimientos')
        ->select
        (
            'fac_movimientos.id as id',
            'fac_movimientos.consecutivo as consecutivo',
            'fac_tipo_doc.nombre as tipomov',
            'tercero_sucursales.nombre as sucursal',
            'terceros.nombre as tercero',
            'fac_movimientos.estado as estado',
            'fac_movimientos.fecha_facturacion as fecha_facturacion',
            'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
            'fac_movimientos.descuento as descuento',
            'fac_movimientos.ivatotal as ivatotal',
            'fac_movimientos.subtotal as subtotal',
            'fac_movimientos.total as total'
        )
        ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
        ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
        ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
        ->join('fac_cruce','fac_cruce.fac_mov_secundario','=','fac_movimientos.id')
        ->get();
    }

    public static function notasRelacionadas($id)
    {
        return  DB::table('fac_movimientos')
        ->select
        (
            'fac_movimientos.id as id',
            'fac_movimientos.consecutivo as consecutivo',
            'fac_tipo_doc.nombre as tipomov',
            'tercero_sucursales.nombre as sucursal',
            'terceros.nombre as tercero',
            'fac_movimientos.estado as estado',
            'fac_movimientos.fecha_facturacion as fecha_facturacion',
            'fac_movimientos.fecha_vencimiento as fecha_vencimiento',
            'fac_movimientos.descuento as descuento',
            'fac_movimientos.ivatotal as ivatotal',
            'fac_movimientos.subtotal as subtotal',
            'fac_movimientos.total as total'
        )
        ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
        ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
        ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
        ->join('fac_cruce','fac_cruce.fac_mov_secundario','=','fac_movimientos.id')
        ->where('fac_cruce.fac_mov_principal',$id)
        ->get();
    }

    //Recibos relacionados a una factura
    public static function reciboRelacionados($id)
    {
        return  DB::table('fac_recibos_caja')
        ->select
        (
            'fac_recibos_caja.id as id',
            'fac_recibos_caja.consecutivo as consecutivo',
            'fac_tipo_rec_caja.nombre as tipomov',
            'tercero_sucursales.nombre as sucursal',
            'terceros.nombre as tercero',
            'fac_pivot_rec_mov.valor as valor',
            'fac_recibos_caja.fecha_recibo as fecha'
        )
        ->join('fac_pivot_rec_mov','fac_pivot_rec_mov.fac_recibo_id', '=', 'fac_recibos_caja.id')
        ->join('fac_movimientos','fac_movimientos.id', '=', 'fac_pivot_rec_mov.fac_mov_id')
        ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
        ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
        ->join('fac_tipo_rec_caja','fac_tipo_rec_caja.id', '=', 'fac_recibos_caja.fac_tipo_rec_caja_id')
        ->where('fac_pivot_rec_mov.fac_mov_id',$id)
        ->get();
    }

    public static function limpiarTiquetesBascula () {
        DB::statement('UPDATE fac_pivot_mov_productos SET num_tiquete = null where CAST(created_at as date)  = CAST(GETDATE() as date)');
        // return DB::raw("UPDATE fac_pivot_mov_productos SET num_tiquete = null where CAST(created_at as date)  = CAST(GETDATE() as date)");
    }

    //Recibos relacionados a una factura
    public static function notasCreditoPorCuadre($cuadreId)
    {
        return  DB::table('fac_movimientos')
        ->select
        (
            'fac_movimientos.id as id',
            'fac_movimientos.consecutivo as consecutivo',
            'fac_movimientos.total as total'
        )
        ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
        ->where('fac_movimientos.gen_cuadre_caja_id',$cuadreId)
        ->where('fac_tipo_doc.naturaleza', 2)
        ->get();
    }

}
