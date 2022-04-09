<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportesGenerados extends Model
{


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

    public static function selectViewAccount($fecha_inicial, $fecha_final){
        return DB::select("select * from AccountantView where fecha_facturacion between '$fecha_inicial' and '$fecha_final' order by consecutivo");
    }

    public static function impuestosFactura(){
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
            ->join('fac_pivot_mov_productos','fac_pivot_mov_productos.fac_mov_id', '=', 'fac_movimientos.id')
            ->join('productos','productos.id', '=', 'fac_pivot_mov_productos.producto_id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            // inner join prod_subgrupos sp on p.prod_subgrupo_id = sp.id
            ->get();
    }

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

    // Reporte para interfaz de las contadoras
    public static function reporteFacturasIva($fecha_inicial, $fecha_final){
        return DB::select("
        select
            *
        from AccountInterfaceView
        where fecha_facturacion between '$fecha_inicial' and '$fecha_final' order by consecutivo");
    }


    public static function ventasContadoCredito($fecha){
        return DB::select(
            "
            select
                fac_tipo_doc.nombre as VentasTipoDoc,
                min(fac_movimientos.consecutivo) as VentasMinConsecutivo,
                max(fac_movimientos.consecutivo) as VentasMaxConsecutivo,
                sum(fac_movimientos.total) as total
            from fac_movimientos
            inner join fac_tipo_doc on fac_movimientos.fac_tipo_doc_id =  fac_tipo_doc.id
            where fecha_facturacion = '$fecha'
                and fac_movimientos.estado != 3
                and fac_tipo_doc.naturaleza in (1,4)
                and fac_tipo_doc.legal >0
            group by fac_tipo_doc.nombre
            "
        );
    }

    public static function devolucionesContadoCredito($fecha){
        return DB::select(
            "
            select
                fac_tipo_doc.nombre as DevTipoDoc,
                sum(fac_movimientos.total) as total
            from fac_movimientos
            inner join fac_tipo_doc on fac_movimientos.fac_tipo_doc_id =  fac_tipo_doc.id
            where fecha_facturacion = '$fecha'
                and fac_movimientos.estado = 3
                and fac_tipo_doc.naturaleza in (1,4)
                and fac_tipo_doc.legal >0
            group by fac_tipo_doc.nombre
            "
        );
    }

    public static function recibosAbonosCreditos($fecha){
        return DB::select(
            "
            select
                trc.nombre,
                sum(fp.valor) as Valor
            from fac_recibos_caja rc
            inner join fac_pivot_forma_recibo fp on rc.id =fp.fac_recibo_id
            inner join fac_formas_pago fm on fp.fac_formas_pago_id = fm.id
            inner join fac_tipo_rec_caja trc on rc.fac_tipo_rec_caja_id = trc.id
            where rc.fecha_recibo = '$fecha'
            group by trc.nombre
            "
        );
    }

    public static function efectivosRecibos($fecha){
        return
        DB::select("
        select
            fac_formas_pago.nombre,
            sum(fac_pivot_forma_recibo.valor) as Valor
        from fac_recibos_caja
        inner join fac_pivot_forma_recibo on  fac_recibos_caja.id =  fac_pivot_forma_recibo.fac_recibo_id
        inner join fac_formas_pago on  fac_pivot_forma_recibo.fac_formas_pago_id = fac_formas_pago.id
        where fac_recibos_caja.fecha_recibo = '$fecha'
        group by fac_formas_pago.nombre
        ");

    }

    public static function impuestoFiscal($fecha){
        return DB::select("
        select
        a.tipo_documento,
        a.fecha_facturacion,
        a.naturaleza,
        a.legal,
        a.[impuesto],
        sum(a.ValorTotalSinIva) As [ValorTotalSinIVA],
        sum(a.ValorIva) as [ValorIVA],
        sum(a.ValorTotalSinIva) + sum(a.ValorIva) as [ValorTotalConIVA]
    from (
    select
            d.consecutivo,
            d.fecha_facturacion,
            td.id,
            td.naturaleza,
            td.nombre as tipo_documento,
            td.legal,
            dd.descporcentaje,
            dd.iva as [impuesto],
            dd.precio as ValorUnit,
            dd.cantidad,
            (dd.precio * dd.cantidad) as ValorTotalSinIva,
            (dd.precio * dd.cantidad) * (cast(dd.iva as float)/100) as ValorIva,
            (dd.precio * dd.cantidad) + ((dd.precio * dd.cantidad) * (cast(dd.iva as float)/100)) as ValorTotal
        from fac_movimientos d
        inner join tercero_sucursales ts on d.cliente_id = ts.id
        inner join terceros t on t.id = ts.tercero_id
        inner join fac_pivot_mov_productos dd on d.id = dd.fac_mov_id
        inner join productos p on dd.producto_id = p.id
        inner join fac_tipo_doc td on d.fac_tipo_doc_id = td.id
        where
            td.naturaleza in (1,4) and td.legal >0
            and d.estado != 3
            and  fecha_facturacion = '$fecha'
    )a
    group by a.tipo_documento, a.fecha_facturacion, a.naturaleza,a.legal, a.[impuesto]
    order by a.tipo_documento");
    }

    public static function pesoAcomuladoVentasNotasDevs($fecha_inicial, $fecha_final){
        return
        DB::select("
        select
            a.producto_id,
            a.nombre,
            isnull(a.PesoVenta, 0) as PesoVenta,
            isnull(b.PesoDevs, 0) as PesoDevs,
            isnull(c.PesoNotas, 0) as PesoNotas,
            isnull(a.PesoVenta, 0) - isnull(b.PesoDevs, 0) - isnull(c.PesoNotas, 0) as PesoTotal
        from (
        select
            p.id as producto_id,
            p.nombre,
            sum(dd.cantidad) as PesoVenta
        from fac_movimientos d
        inner join fac_pivot_mov_productos dd on d.id = dd.fac_mov_id
        inner join fac_tipo_doc td on d.fac_tipo_doc_id = td.id
        inner join productos p on dd.producto_id = p.id
        where d.fecha_facturacion between '$fecha_inicial' and '$fecha_final' and d.estado != 3 and td.naturaleza in (1,4)
        group by p.id, p.nombre
        )a
        left join (
        select
            p.id as producto_id,
            p.nombre,
            sum(dd.cantidad) as PesoDevs
        from fac_movimientos d
        inner join fac_pivot_mov_productos dd on d.id = dd.fac_mov_id
        inner join fac_tipo_doc td on d.fac_tipo_doc_id = td.id
        inner join productos p on dd.producto_id = p.id
        where d.fecha_facturacion between '$fecha_inicial' and '$fecha_final' and d.estado = 3
        group by p.id, p.nombre
        )b on a.producto_id = b.producto_id
        left join (
        SELECT
            productos.id as producto_id,
            productos.nombre,
            sum(cast(fac_pivot_mov_productos.cantidad as float) ) as PesoNotas
        FROM fac_cruce
        inner join fac_movimientos mf on mf.id = fac_cruce.fac_mov_principal
        inner join fac_movimientos mn on mn.id = fac_cruce.[fac_mov_secundario]
        inner join fac_pivot_mov_productos on mn.id = fac_pivot_mov_productos.fac_mov_id
        inner join fac_tipo_doc on fac_tipo_doc.id = mn.fac_tipo_doc_id
        inner join productos on fac_pivot_mov_productos.producto_id = productos.id
        where cast(mf.created_at as date) between '$fecha_inicial' and '$fecha_final'  and cast(mn.created_at as date) between '$fecha_inicial' and '$fecha_final' and fac_tipo_doc.naturaleza = 2
        group by productos.id, productos.nombre
        )c on a.producto_id = c.producto_id
        ");
    }

    public static function reporteFiscal ($fecha_inicial, $fecha_final){
        return DB::select("
        select
            gen_empresa.nombre as Empresa,
            gen_empresa.nit as Nit,
            fac_movimientos.id As id,
            fac_pivot_mov_productos.id,
            fac_movimientos.consecutivo  as consecutivo,
            fac_movimientos.fecha_facturacion as fecha,
            fac_movimientos.subtotal,
            fac_movimientos.total,
            fac_movimientos.saldo,
            fac_pivot_mov_productos.cantidad as cantidad,
            fac_pivot_mov_productos.precio as precio,
            fac_pivot_mov_productos.descporcentaje,
            gen_iva.id iva_id,
            gen_iva.nombre Tipo_IVA,
            fac_pivot_mov_productos.iva iva_detalle,
            (fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) as precio_base,
            (fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * cast(fac_pivot_mov_productos.iva as float)/100 as iva_base_producto,
            (fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) - ((fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * (fac_pivot_mov_productos.descporcentaje/100)) + ((fac_pivot_mov_productos.cantidad *	fac_pivot_mov_productos.precio) * (cast(fac_pivot_mov_productos.iva as float)/100)) as precio_total_producto,
            fac_tipo_doc.id  as tipo_id,
            fac_tipo_doc.nombre  as tipo,
            fac_tipo_doc.naturaleza
        from fac_movimientos
        inner join fac_pivot_mov_productos on fac_pivot_mov_productos.fac_mov_id = fac_movimientos.id
        inner join fac_tipo_doc on  fac_tipo_doc.id  =  fac_movimientos.fac_tipo_doc_id
        inner join productos on productos.id = fac_pivot_mov_productos.producto_id
        inner join gen_iva on gen_iva.id = productos.gen_iva_id
        inner join gen_empresa on gen_empresa.id = 1
        where
            fac_movimientos.estado not in ( 3 , 10)
            and fac_movimientos.fecha_facturacion between '$fecha_inicial' and '$fecha_final'
            and fac_tipo_doc.naturaleza in (1, 2, 3, 4)
            and fac_tipo_doc.legal in (1)
        order by fac_tipo_doc.nombre, fac_movimientos.consecutivo, fac_movimientos.fecha_facturacion
        ");
    }

    public static function  HeadersFiscal($fecha_ini, $fecha_fin){
        return DB::select("
        select
        a.naturaleza,
        a.tipo,
        min(a.consecutivo) as consec_ini,
        max(a.consecutivo) as consec_fin
    from (
    select
        fac_movimientos.consecutivo  as consecutivo,
        fac_tipo_doc.id  as tipo_id,
        fac_tipo_doc.nombre  as tipo,
        fac_tipo_doc.naturaleza,
        fac_movimientos.fecha_facturacion as fecha
    from fac_movimientos
    inner join fac_tipo_doc on  fac_tipo_doc.id  =  fac_movimientos.fac_tipo_doc_id
    where
        fac_movimientos.estado not in (3, 10)
        and fac_movimientos.fecha_facturacion between '$fecha_ini' and '$fecha_fin'
        and fac_tipo_doc.naturaleza in (1, 2 ,3, 4)
        and fac_tipo_doc.legal in (1)
    )a
    group by  a.naturaleza, a.tipo
        ");
    }

    public static function DetailsFiscal($fecha_ini, $fecha_fin){
        return DB::select("
        select
	a.fecha,
	sum(a.precio_base_POS) as TotalEfectivo,
	sum(a.precio_base_creditos) as TotalCreditos,
	sum(a.precio_base_creditos + a.precio_base_POS) as VentaTotalDia,
	sum(a.iva_base_producto_creditos + a.iva_base_producto_POS) as Impuesto
from (
select
	fac_movimientos.fecha_facturacion as fecha,
    fac_movimientos.consecutivo  as consecutivo,
	fac_tipo_doc.id  as tipo_id,
    fac_tipo_doc.nombre  as tipo,
	fac_tipo_doc.naturaleza,
	isnull(case when fac_tipo_doc.naturaleza = 1 then (fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) end, 0 ) as precio_base_creditos,
	isnull(case when fac_tipo_doc.naturaleza = 1 then(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * cast(fac_pivot_mov_productos.iva as float)/100 end , 0 ) as iva_base_producto_creditos,
	isnull(case when fac_tipo_doc.naturaleza = 4 then (fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) end, 0 ) as precio_base_POS,
	isnull(case when fac_tipo_doc.naturaleza = 4 then(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * cast(fac_pivot_mov_productos.iva as float)/100 end , 0 ) as iva_base_producto_POS
from fac_movimientos
inner join fac_pivot_mov_productos on fac_pivot_mov_productos.fac_mov_id = fac_movimientos.id
inner join fac_tipo_doc on  fac_tipo_doc.id  =  fac_movimientos.fac_tipo_doc_id
inner join productos on productos.id = fac_pivot_mov_productos.producto_id
inner join gen_iva on gen_iva.id = productos.gen_iva_id
where
	fac_movimientos.estado not in (3, 10)
	and fac_movimientos.fecha_facturacion between '$fecha_ini' and '$fecha_fin'
	and fac_tipo_doc.naturaleza in (1, 2 ,3, 4)
	and fac_tipo_doc.legal in (1)
)a
group by a.fecha
        ");
    }

    public static function impuestosFiscal($fecha_ini,$fecha_fin) {
        return DB::select("
        select
            a.nombre_iva,
            sum(a.precio_base) as base,
            sum(a.iva_base_producto) as impuestos
from (
select
    fac_movimientos.consecutivo  as consecutivo,
	(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) as precio_base,
	(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * cast(fac_pivot_mov_productos.iva as float)/100 as iva_base_producto,
	(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) - ((fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * (fac_pivot_mov_productos.descporcentaje/100)) + ((fac_pivot_mov_productos.cantidad *	fac_pivot_mov_productos.precio) * (cast(fac_pivot_mov_productos.iva as float)/100)) as precio_total_producto,
	fac_tipo_doc.id  as tipo_id,
    fac_tipo_doc.nombre  as tipo,
	fac_tipo_doc.naturaleza,
	fac_movimientos.fecha_facturacion as fecha,
	gen_iva.nombre nombre_iva
from fac_movimientos
inner join fac_pivot_mov_productos on fac_pivot_mov_productos.fac_mov_id = fac_movimientos.id
inner join fac_tipo_doc on  fac_tipo_doc.id  =  fac_movimientos.fac_tipo_doc_id
inner join productos on productos.id = fac_pivot_mov_productos.producto_id
inner join gen_iva on gen_iva.id = productos.gen_iva_id
where
	fac_movimientos.estado not in (3, 10)
	and fac_movimientos.fecha_facturacion between '$fecha_ini' and '$fecha_fin'
	and fac_tipo_doc.naturaleza in (1, 2 ,3, 4)
	and fac_tipo_doc.legal in (1)
)a group by a.nombre_iva
        ");
    }

    public static function impuestoNcFiscal ($fecha_ini,$fecha_fin) {
        return DB::select("
        select
            a.nombre_iva,
            sum(a.precio_base) as base,
            sum(a.iva_base_producto) as impuestos
from (
select
    fac_movimientos.consecutivo  as consecutivo,
	(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) as precio_base,
	(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * cast(fac_pivot_mov_productos.iva as float)/100 as iva_base_producto,
	(fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) - ((fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) * (fac_pivot_mov_productos.descporcentaje/100)) + ((fac_pivot_mov_productos.cantidad *	fac_pivot_mov_productos.precio) * (cast(fac_pivot_mov_productos.iva as float)/100)) as precio_total_producto,
	fac_tipo_doc.id  as tipo_id,
    fac_tipo_doc.nombre  as tipo,
	fac_tipo_doc.naturaleza,
	fac_movimientos.fecha_facturacion as fecha,
	gen_iva.nombre nombre_iva
from fac_movimientos
inner join fac_pivot_mov_productos on fac_pivot_mov_productos.fac_mov_id = fac_movimientos.id
inner join fac_tipo_doc on  fac_tipo_doc.id  =  fac_movimientos.fac_tipo_doc_id
inner join productos on productos.id = fac_pivot_mov_productos.producto_id
inner join gen_iva on gen_iva.id = productos.gen_iva_id
where
	fac_movimientos.estado not in (3, 10)
	and fac_movimientos.fecha_facturacion between '$fecha_ini' and '$fecha_fin'
	and fac_tipo_doc.naturaleza in (2)
	and fac_tipo_doc.legal in (1)
)a group by a.nombre_iva
        ");
    }

    public static function impuestoBolsaFiscal($fecha_ini,$fecha_fin) {
        return DB::select("SELECT
        p.nombre,
        dd.precio,
        isnull(case when d.estado = 3 or ftd.naturaleza = 2 then sum(dd.cantidad) end, 0 ) as devoluciones,
        ISNULL(case when d.estado != 3 and ftd.naturaleza != 2 then SUM(dd.cantidad) end, 0) as ventas,
        ftd.nombre as tipo_doc
    from gen_empresa e
    inner join productos p on e.producto_bolsa_id = p.id
    inner join fac_pivot_mov_productos dd on p.id = dd.producto_id
    inner join fac_movimientos d on dd.fac_mov_id = d.id
    inner join fac_tipo_doc ftd on ftd.id = d.fac_tipo_doc_id
    where d.fecha_facturacion between '$fecha_ini' and '$fecha_fin'
    --and d.estado != 3
    and ftd.nombre not like '%hab%'
    -- and ftd.legal = 1
    group by p.nombre, dd.precio, ftd.nombre, d.estado, ftd.naturaleza");
    }

    public static function formaPagoFiscal($fecha_ini,$fecha_fin) {
        return DB::select("
            select
                fp.nombre,
                sum(fpd.valor_pagado) as valor
            from fac_pivot_mov_formaspago fpd
            inner join fac_movimientos d on fpd.fac_mov_id = d.id
            inner join fac_formas_pago fp on fpd.fac_formas_pago_id = fp.id
            where d.fecha_facturacion between '$fecha_ini' and '$fecha_fin' and d.estado != 3
            group by fp.nombre
        ");
    }
}
