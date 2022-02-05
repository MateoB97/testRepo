<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class InvCierreInventario extends Model
{
    protected $table = 'inv_cierre_inventario';

    protected $fillable = [
    	'fecha_cierre',
    	'total_diferencia_kilos',
    	'total_diferencia_dinero'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function getDataCierreInventario(){
        return DB::select("
            select
                *
            from
            (
            select
                ci.fecha_cierre
                ,sg.nombre as [SubGrupo]
                ,p.nombre as [Producto]
                ,sum(dci.cantidad_cierre) as [CantidadPesada]
                ,um.abrev_pos as [UM]
                ,i.costo_promedio as [Costo]
            from inv_cierre_inventario ci
            inner join inv_pivot_cierre_productos dci on ci.id = dci.inv_cierre_id
            inner join productos p on dci.producto_id = p.id
            inner join prod_subgrupos sg on p.prod_subgrupo_id = sg.id
            inner join gen_unidades um on p.gen_unidades_id = um.id
            inner join inventarios i on i.producto_id = p.id
            where dci.cantidad_cierre > 0
            group by ci.fecha_cierre, p.nombre, um.abrev_pos, sg.nombre, i.costo_promedio
            )a
            order by  a.fecha_cierre desc, a.SubGrupo, a.Producto
        ");
    }

    public static function getDataCierreInventarioPesadas($id){
        return DB::select("
            select
                *
            from
            (
            select
                sg.nombre as [sub_grupo]
                ,p.nombre as [producto]
                ,sum(dci.cantidad_cierre) as [cantidad_pesada]
                ,um.abrev_pos as [um]
                ,i.costo_promedio as [costo_promedio]
            from inv_cierre_inventario ci
            inner join inv_pivot_cierre_productos dci on ci.id = dci.inv_cierre_id
            inner join productos p on dci.producto_id = p.id
            inner join prod_subgrupos sg on p.prod_subgrupo_id = sg.id
            inner join gen_unidades um on p.gen_unidades_id = um.id
            inner join inventarios i on i.producto_id = p.id
            where ci.id = '$id' and dci.cantidad_cierre > 0
            group by p.nombre, um.abrev_pos, sg.nombre, i.costo_promedio
            )a
            order by a.sub_grupo, a.producto
        ");
    }

    public static function getDataCierreInventarioVariacion($fecha_final, $fecha_inicial_vcd, $cierre1, $cierre2){
        // dd($cierre1);
        return DB::select("
                select
                sp.id as [SubGrupoID]
                ,sp.nombre as [SubGrupo]
                ,p.nombre as [Producto]
                ,i.cantidad as [InvActual]
                ,ISNULL(ci.cantidad_cierre, 0) as [InvInicial]
                ,isnull(cc.cantidad_entreada_mercancia, 0 ) as [QtyEntradas]
                ,isnull(dd.cantidad_ventas, 0) as [QtyVentas]
                ,isnull(ddd.cantidad_devoluciones, 0) as  [QtyDevs]
                ,isnull(ncdd.cantidad_nota_credito, 0) as  [QtyNotas]
                ,ISNULL(cif.cantidad_cierre_final, 0) as [InvFinal]
                ,(ISNULL(ci.cantidad_cierre, 0)  +	ISNULL(cc.cantidad_entreada_mercancia, 0 ) + isnull(ncdd.cantidad_nota_credito, 0 ) - ISNULL(dd.cantidad_ventas , 0 )) as [InvTeorico]
	            ,(ISNULL(ci.cantidad_cierre, 0) +	ISNULL(cc.cantidad_entreada_mercancia, 0 ) +isnull(ncdd.cantidad_nota_credito, 0 ) - ISNULL(dd.cantidad_ventas , 0 ))- ISNULL(cif.cantidad_cierre_final, 0) as [Merma]
            from inventarios i -- INVENTARIO ACTUAL
            inner join productos p on i.producto_id = p.id
            inner join prod_subgrupos sp on p.prod_subgrupo_id = sp.id
            inner join prod_grupos pg on sp.prodGrupo_id = pg.id
            left join
            (
                select
                    c.id as CierreID,
                    dci.producto_id,
                    sum(dci.cantidad_cierre) as cantidad_cierre
                from inv_cierre_inventario c
                inner join inv_pivot_cierre_productos dci on c.id = dci.inv_cierre_id
                where c.id = '$cierre1'
                group by c.id, dci.producto_id
            ) ci on p.id = ci.producto_id -- PRIMER CIERRE
            left join
            (
                select
                    dcc.producto_id,
                    sum(dcc.cantidad) as cantidad_entreada_mercancia
                from com_compras cc
                inner join com_pivot_compra_producto dcc on cc.id = dcc.com_compras_id
                where  cc.fecha_compra between '$fecha_inicial_vcd' and '$fecha_final'
                group by  dcc.producto_id
            )cc on p.id	 = cc.producto_id -- COMPRAS
            left join
            (
                select
                    dd.producto_id,
                    sum(dd.cantidad) as cantidad_ventas
                from fac_movimientos d
                inner join fac_pivot_mov_productos dd on d.id = dd.fac_mov_id
                inner join fac_tipo_doc td on d.fac_tipo_doc_id = td.id
                where d.fecha_facturacion between '$fecha_inicial_vcd'  and '$fecha_final'
                and td.naturaleza in (1, 4)
                and d.estado != 3
                group by dd.producto_id
            ) dd on p.id = dd.producto_id -- FACTURAS - VENTAS
            left join
            (
                select
                    dd.producto_id,
                    sum(dd.cantidad) as cantidad_devoluciones
                from fac_movimientos d
                inner join fac_pivot_mov_productos dd on d.id = dd.fac_mov_id
                inner join fac_tipo_doc td on d.fac_tipo_doc_id = td.id
                where d.fecha_facturacion between '$fecha_inicial_vcd'  and '$fecha_final'
                and td.naturaleza in (1, 4) and d.estado = 3
                group by dd.producto_id
            ) ddd on p.id = ddd.producto_id -- DEVOLUCIONES - FACTURAS - VENTAS
            left join
            (
                select
                    dd.producto_id,
                    sum(dd.cantidad) as cantidad_nota_credito
                from fac_movimientos d
                inner join fac_pivot_mov_productos dd on d.id = dd.fac_mov_id
                inner join fac_tipo_doc td on d.fac_tipo_doc_id = td.id
                where d.fecha_facturacion between '$fecha_inicial_vcd'  and '$fecha_final'
                and td.naturaleza in (2) and d.afectar_inventario > 0
                group by dd.producto_id
            ) ncdd on p.id = ncdd.producto_id -- NOTAS - FACTURAS - VENTAS
            left join
            (
                select
                    c.id as CierreID,
                    dci.producto_id,
                    sum(dci.cantidad_cierre) as cantidad_cierre_final
                from inv_cierre_inventario c
                inner join inv_pivot_cierre_productos dci on c.id = dci.inv_cierre_id
                where c.id = '$cierre2'
                group by c.id, dci.producto_id
            ) cif on p.id = cif.producto_id -- CIERRE FINAL
        ");
    }
}
