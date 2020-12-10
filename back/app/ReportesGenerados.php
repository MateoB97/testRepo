<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportesGenerados extends Model
{
    public static function todosConTipoSucursalGrupoTipoCustom(){
    return DB::table('fac_movimientos')
    ->select(DB::raw('fac_movimientos.id As id,
                fac_movimientos.consecutivo As consecutivo,
                fac_movimientos.subtotal as subtotal,
                fac_movimientos.descuento as descuento,
                fac_movimientos.ivatotal as ivatotal,
                fac_movimientos.total as total,
                fac_movimientos.saldo as saldo,
                fac_movimientos.estado as estado,
                fac_movimientos.fecha_facturacion as fecha_facturacion,
                fac_movimientos.fecha_vencimiento as fecha_vencimiento,
                fac_movimientos.cufe as cufe,
                tercero_sucursales.nombre as sucursal,
                tercero_sucursales.email as email,
                terceros.nombre as tercero,
                fac_tipo_doc.nombre as tipo,
                users.name as usuario,
                fac_tipo_doc.naturaleza,
                fac_movimientos.estado_fe as estado_fe,
                fac_tipo_doc.soenac_tipo_doc_api_id as soenac_tipo_doc,
                isnull(a.notescount, 0 ) as notescount,
                isnull(b.ReceipsCount, 0 ) as ReceipsCount'))
        ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
        ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
        ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
        ->join('gen_cuadre_caja','gen_cuadre_caja.id', '=', 'fac_movimientos.gen_cuadre_caja_id')
        ->join('users','users.id', '=', 'gen_cuadre_caja.usuario_id')
        ->leftJoin(DB::raw('(select
                            cp.id  AS IDPrincipal,
                                COUNT(distinct cs.fac_mov_secundario) AS notescount
                        from fac_movimientos d
                        inner join fac_cruce cs on d.id = cs.fac_mov_secundario
                        inner join fac_movimientos cp on cs.fac_mov_principal = cp.id
                        group by cp.id ) a') ,
        function($join)
        {
        $join->on('fac_movimientos.id', '=', 'a.IDPrincipal');
        })
        ->leftJoin(DB::raw('(select
                            f.id as IDPrincipal,
                            COUNT(distinct rc.id) as ReceipsCount
                        from fac_recibos_caja rc
                        inner join fac_pivot_rec_mov rcp on rc.id = rcp.fac_recibo_id
                        inner join fac_movimientos f on f.id = rcp.fac_mov_id
                        group by f.id) b') ,
        function($join)
        {
        $join->on('fac_movimientos.id', '=', 'b.IDPrincipal');
        })
        ->orderBy('fac_movimientos.id','desc')
        ->get()
        ->take(20);
    }

    public static function reporteVentasPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id){
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
                'terceros.id as tercero_id',
                'terceros.nombre as tercero',
                'terceros.documento as documento',
                'fac_tipo_doc.id as tipoid',
                'fac_tipo_doc.nombre as tipo'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_movimientos.estado', '!=', 3)
            ->whereNotIn('fac_tipo_doc.id', [1,2])
            ->when($fecha_inicial, function ($query, $fecha_inicial) {
                return $query->where('fac_movimientos.fecha_facturacion', '>=', $fecha_inicial);
            })
            ->when($fecha_final, function ($query, $fecha_final) {
                return $query->where('fac_movimientos.fecha_facturacion', '<=', $fecha_final);
            })
            ->when($tercero_id, function ($query, $tercero_id) {
                return $query->where('terceros.id', $tercero_id);
            })
            ->when($sucursal_id, function ($query, $sucursal_id) {
                return $query->where('tercero_sucursales.id', $sucursal_id);
            })
            // ->when($tipodoc, function ($query, $tipodoc) {
            //     return $query->where('fac_tipo_doc.id', $tipodoc);
            // })
            // ->orderBy('terceros.documento','asc')
            // ->orderBy('tercero_sucursales.id','asc')
            ->orderBy('fac_movimientos.fecha_facturacion', 'asc')
            ->get();
    }

    public static function reporteDevolucionesPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id){
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
            ->where('fac_movimientos.estado', 3)
            ->when($fecha_inicial, function ($query, $fecha_inicial) {
                return $query->where('fac_movimientos.fecha_facturacion', '>=', $fecha_inicial);
            })
            ->when($fecha_final, function ($query, $fecha_final) {
                return $query->where('fac_movimientos.fecha_facturacion', '<=', $fecha_final);
            })
            ->when($tercero_id, function ($query, $tercero_id) {
                return $query->where('terceros.id', $tercero_id);
            })
            ->when($sucursal_id, function ($query, $sucursal_id) {
                return $query->where('tercero_sucursales.id', $sucursal_id);
            })
            // ->orderBy('terceros.documento','asc')
            // ->orderBy('tercero_sucursales.id','asc')
            ->orderBy('fac_movimientos.fecha_facturacion', 'asc')
            ->get();
    }

    public static function reporteRecibosPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id){
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
                'fac_tipo_rec_caja.nombre as tipo',
                'fac_tipo_rec_caja.id as tipoid'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_recibos_caja.tercero_sucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('fac_tipo_rec_caja','fac_tipo_rec_caja.id', '=', 'fac_recibos_caja.fac_tipo_rec_caja_id')
            ->where('fac_recibos_caja.estado', 1)
            ->when($fecha_inicial, function ($query, $fecha_inicial) {
                return $query->where('fac_recibos_caja.fecha_recibo', '>=', $fecha_inicial);
            })
            ->when($fecha_final, function ($query, $fecha_final) {
                return $query->where('fac_recibos_caja.fecha_recibo', '<=', $fecha_final);
            })
            ->when($tercero_id, function ($query, $tercero_id) {
                return $query->where('terceros.id', $tercero_id);
            })
            ->when($sucursal_id, function ($query, $sucursal_id) {
                return $query->where('tercero_sucursales.id', $sucursal_id);
            })
            // ->orderBy('terceros.documento','asc')
            // ->orderBy('tercero_sucursales.id','asc')
            ->orderBy('fac_recibos_caja.fecha_recibo','asc')
            ->get();
    }

    public static function reporteSaldosEnCartera($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {
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
                ->when($fecha_inicial, function ($query, $fecha_inicial) {
                    return $query->where('fac_movimientos.fecha_facturacion', '>=', $fecha_inicial);
                })
                ->when($fecha_final, function ($query, $fecha_final) {
                    return $query->where('fac_movimientos.fecha_facturacion', '<=', $fecha_final);
                })
                ->when($tercero_id, function ($query, $tercero_id) {
                    return $query->where('terceros.id', $tercero_id);
                })
                ->when($sucursal_id, function ($query, $sucursal_id) {
                    return $query->where('tercero_sucursales.id', $sucursal_id);
                })
                // ->when($tipodoc, function ($query, $tipodoc) {
                //     return $query->where('fac_tipo_doc.id', $tipodoc);
                // })
                ->orderBy('terceros.nombre','asc')
                ->orderBy('tercero_sucursales.nombre','asc')
                ->orderBy('fac_movimientos.created_at','asc')
                ->get();
        }

        public static function reporteVentasPorFechaPorGrupoPorTipoCustom($fecha_inicial, $fecha_final, $sucursal_id, $group_id, $tipodoc){
            return DB::table('fac_movimientos')
                ->select(
                    'fac_movimientos.id As id',
                    'fac_movimientos.consecutivo  as consecutivo',
                    'fac_movimientos.fecha_facturacion as fecha',
                    'fac_pivot_mov_productos.cantidad as cantidad',
                    'fac_pivot_mov_productos.precio as precio',
                    'prod_grupos.nombre as grupo',
                    'productos.nombre as producto',
                    'terceros.id as tercero_id',
                    'terceros.nombre  as tercero',
                    'terceros.documento  as documento',
                    'tercero_sucursales.id as sucursal_id',
                    'tercero_sucursales.nombre  as sucursal',
                    'tercero_sucursales.direccion  as direccion',
                    'tercero_sucursales.telefono as telefono',
                    'fac_tipo_doc.nombre  as tipo'
                )
                ->join('tercero_sucursales','tercero_sucursales.id', '=', 'fac_movimientos.cliente_id')
                ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
                ->join('fac_pivot_mov_productos','fac_pivot_mov_productos.fac_mov_id', '=', 'fac_movimientos.id')
                ->join('productos','productos.id', '=', 'fac_pivot_mov_productos.producto_id')
                ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
                ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
                ->join('fac_tipo_doc', 'fac_tipo_doc.id' , '=', 'fac_movimientos.fac_tipo_doc_id')
                ->where('fac_movimientos.estado', '!=', 3)
                ->where('tercero_sucursales.id', $sucursal_id)
                ->whereBetween('fac_movimientos.fecha_facturacion', [$fecha_inicial, $fecha_final])
                ->when($group_id, function ($query, $group_id) {
                    return $query->whereIn('tercero_sucursales.id', $group_id);
                })
                ->when($tipodoc, function ($query, $tipodoc) {
                    return $query->whereIn('fac_tipo_doc.id', $tipodoc);
                })
                ->orderBy('terceros.documento','asc')
                ->orderBy('tercero_sucursales.id','asc')
                ->get();
    }
}
