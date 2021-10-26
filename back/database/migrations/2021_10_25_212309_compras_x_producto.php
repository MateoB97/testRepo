<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasXProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view [dbo].[ViewComprasXProducto]
        as
        select
        	com_compras.consecutivo as consec_compra
        	,com_compras.fecha_compra as fecha_compra
        	,com_compras.subtotal as subtotal_compra
        	,com_compras.total as total_compra
        	,com_compras.saldo as saldo_compra
        	,com_pivot_compra_producto.cantidad as cantidad_compra_p
        	,com_pivot_compra_producto.precio as precio_compra_p
        	,terceros.id as tercero_id
        	,terceros.nombre as tercero
        	,tercero_sucursales.id as sucursales_id
        	,tercero_sucursales.nombre as sucursal
        	,com_tipo_compras.nombre as tipo
        	,productos.id as productos_id
        	,productos.nombre as nombre_prod
        	,prod_subgrupos.id as subprod_id
        	,prod_subgrupos.nombre as nombre_subg
        	,prod_grupos.id as grupos_id
        	,prod_grupos.nombre as nombre_g
        	,ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, com_compras.consecutivo ASC) AS [NumRegistro]
        from com_compras
        --compras
        inner join com_pivot_compra_producto on com_pivot_compra_producto.com_compras_id = com_compras.id
        --productos
        inner join productos on productos.id = com_pivot_compra_producto.producto_id
        inner join prod_subgrupos on prod_subgrupos.id = productos.prod_subgrupo_id
        inner join prod_grupos on prod_grupos.id = prod_subgrupos.prodGrupo_id
        --terceros
        inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
        inner join terceros on terceros.id = tercero_sucursales.tercero_id
        --tipo compras
        inner join com_tipo_compras on com_tipo_compras.id = com_compras.com_tipo_compras_id
        where com_compras.estado != 3");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ViewComprasXProducto');
    }
}
