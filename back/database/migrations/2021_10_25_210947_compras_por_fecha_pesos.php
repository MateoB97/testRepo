<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasPorFechaPesos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW [dbo].[ViewComprasPorFechaPesos]
        as
        select
        	prod_grupos.nombre as grupo
        	,prod_subgrupos.nombre as subgrupo
        	,productos.nombre as producto
        	,sum(com_pivot_compra_producto.cantidad) as cantidad
        	,gen_unidades.nombre as unidades
        from com_compras
        inner join com_tipo_compras on com_tipo_compras.id = com_compras.estado
        inner join com_pivot_compra_producto on com_pivot_compra_producto.com_compras_id = com_compras.id
        inner join productos on productos.id  = com_pivot_compra_producto.producto_id
        inner join prod_subgrupos on prod_subgrupos.id = productos.prod_subgrupo_id
        inner join prod_grupos on prod_grupos.id = prod_subgrupos.prodGrupo_id
        inner join gen_unidades on gen_unidades.id = productos.gen_unidades_id
        where com_compras.estado != 3
        group by prod_grupos.nombre, prod_subgrupos.nombre, productos.nombre, gen_unidades.nombre");

        DB::statement("CREATE OR ALTER VIEW [dbo].[ViewComprasDevPorFechaPesos]
        as
        select
        	productos.nombre as producto_dev
        	,prod_grupos.nombre as grupo_dev
        	,prod_subgrupos.nombre as subgrupo_dev
        	,com_pivot_compra_producto.cantidad as cantidad_dev
        	,com_pivot_compra_producto.precio as precio_dev
        	,gen_unidades.nombre as unidades_dev
        	,com_compras.fecha_compra as fecha_dev
        from com_compras
        inner join com_tipo_compras on com_tipo_compras.id = com_compras.estado
        inner join com_pivot_compra_producto on com_pivot_compra_producto.com_compras_id = com_compras.id
        inner join productos on productos.id  = com_pivot_compra_producto.producto_id
        inner join prod_subgrupos on prod_subgrupos.id = productos.prod_subgrupo_id
        inner join prod_grupos on prod_grupos.id = prod_subgrupos.prodGrupo_id
        inner join gen_unidades on gen_unidades.id = productos.gen_unidades_id
        where com_compras.estado = 3");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW if exists ViewComprasPorFechaPesos');
        DB::statement('DROP VIEW if exists ViewComprasDevPorFechaPesos');
    }
}
