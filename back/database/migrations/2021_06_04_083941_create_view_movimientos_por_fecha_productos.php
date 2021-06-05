<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateViewMovimientosPorFechaProductos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view ViewMovimientoPorFechaProductos
        as select
            fac_movimientos.id As id_venta,
            fac_movimientos.consecutivo As consecutivo_venta,
            fac_movimientos.total as total_venta,
            fac_movimientos.subtotal as subtotal_venta,
            fac_movimientos.saldo as saldo_venta,
            fac_movimientos.fecha_facturacion as fecha_venta,
            tercero_sucursales.id as sucursal_id_venta,
            tercero_sucursales.nombre as sucursal_venta,
            terceros.id as tercero_venta_id,
            terceros.nombre as tercero_venta,
            fac_tipo_doc.id as tipo_id_venta,
            fac_tipo_doc.nombre as tipo_venta,
            productos.id as [Producto_id],
            productos.nombre as [Nombre Producto],
            fac_pivot_mov_productos.precio,
            fac_pivot_mov_productos.cantidad,
            (fac_pivot_mov_productos.cantidad * fac_pivot_mov_productos.precio) as [PrecioTotal],
            prod_grupos.id as grupo_id,
            prod_grupos.nombre as grupo_nombre,
            prod_subgrupos.id as subgrupo_id,
            prod_subgrupos.nombre as subgrupo_nombre
            from fac_movimientos
            inner join fac_pivot_mov_productos on fac_movimientos.id = fac_pivot_mov_productos.fac_mov_id
            inner join tercero_sucursales on tercero_sucursales.id = fac_movimientos.cliente_id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            inner join fac_tipo_doc on fac_tipo_doc.id = fac_movimientos.fac_tipo_doc_id
            inner join productos on fac_pivot_mov_productos.producto_id = productos.id
            inner join prod_subgrupos on productos.prod_subgrupo_id = prod_subgrupos.id
            inner join prod_grupos on prod_subgrupos.prodGrupo_id = prod_grupos.id
            where fac_movimientos.estado != 3
            --and fac_tipo_doc_id not in (1,2)
            and fac_tipo_doc.nombre not like '%hab%'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('view_movimientos_por_fecha_productos');
    }
}
