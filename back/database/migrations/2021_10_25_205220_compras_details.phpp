<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW [dbo].[ViewComprasDetails]
        as
        select
        	--com_compras
            com_compras.id As id_compra,
        	com_compras.estado as estado_compra,
            com_compras.consecutivo as consecutivo_compra,
            com_compras.fecha_compra as fecha_compra,
            com_compras.subtotal as subtotal_compra,
        	com_compras.total as total_compra,
        	com_compras.saldo as saldo_compra,
        	--pivot_compra_prod
            com_pivot_compra_producto.cantidad as cantidad_compra,
            com_pivot_compra_producto.precio as precio_compra,
        	com_pivot_compra_producto.descporcentaje as descuento_compra,
        	com_pivot_compra_producto.iva as iva_compra,
        	--calculo iva/desc
            (com_pivot_compra_producto.cantidad * com_pivot_compra_producto.precio) - ((com_pivot_compra_producto.cantidad * com_pivot_compra_producto.precio) * (com_pivot_compra_producto.descporcentaje/100)) + ((com_pivot_compra_producto.cantidad *	com_pivot_compra_producto.precio) * (cast(com_pivot_compra_producto.iva as float)/100)) as descuento_iva_cantidad_com,
            --grupos
        	prod_grupos.id as grupo_id_compra,
            prod_grupos.nombre as grupo_compra,
        	--prod
            productos.nombre as producto_compra,
        	--terceros
            terceros.id as tercero_id_compra,
            terceros.nombre  as tercero_compra,
            terceros.documento  as documento_compra,
        	--sucursales
            tercero_sucursales.id as sucursal_id_compra,
            tercero_sucursales.nombre  as sucursal_compra,
        	--tipo doc
        	com_tipo_compras.id  as tipo_id_compra,
            com_tipo_compras.nombre  as tipo_compra,
        	ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, com_compras.consecutivo ASC) AS [NumRegistro]
        from com_compras
        --terceros
        inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
        inner join terceros on terceros.id =  tercero_sucursales.tercero_id
        --compra_prod
        inner join com_pivot_compra_producto on com_pivot_compra_producto.com_compras_id = com_compras.id
        --productos
        inner join productos on productos.id  =  com_pivot_compra_producto.producto_id
        inner join prod_subgrupos on prod_subgrupos.id =  productos.prod_subgrupo_id
        inner join prod_grupos on prod_grupos.id =  prod_subgrupos.prodGrupo_id
        --tipo_doc
        inner join com_tipo_compras on  com_tipo_compras.id  =  com_compras.com_tipo_compras_id
        where com_compras.estado != 3");

        DB::statement("CREATE VIEW [dbo].[ViewDevolucionesCompraDetails]
        as
        select
        	--com_compras
            com_compras.id As id_devol,
        	com_compras.estado as estado_devol,
            com_compras.consecutivo as consecutivo_devol,
            com_compras.fecha_compra as fecha_devol,
            com_compras.subtotal as subtotal_devol,
        	com_compras.total as total_devol,
        	com_compras.saldo as saldo_devol,
        	--pivot_compra_prod
            com_pivot_compra_producto.cantidad as cantidad_devol,
            com_pivot_compra_producto.precio as precio_devol,
        	com_pivot_compra_producto.descporcentaje as descuento_devol,
        	com_pivot_compra_producto.iva as iva_devol,
        	--calculo iva/desc
            (com_pivot_compra_producto.cantidad * com_pivot_compra_producto.precio) - ((com_pivot_compra_producto.cantidad * com_pivot_compra_producto.precio) * (com_pivot_compra_producto.descporcentaje/100)) + ((com_pivot_compra_producto.cantidad *	com_pivot_compra_producto.precio) * (cast(com_pivot_compra_producto.iva as float)/100)) as descuento_iva_cantidad_com,
            --grupos
        	prod_grupos.id as grupo_id_devol,
            prod_grupos.nombre as grupo_devol,
        	--prod
            productos.nombre as producto_devol,
        	--terceros
            terceros.id as tercero_id_devol,
            terceros.nombre  as tercero_devol,
            terceros.documento  as documento_devol,
        	--sucursales
            tercero_sucursales.id as sucursal_id_devol,
            tercero_sucursales.nombre  as sucursal_devol,
        	--tipo doc
        	com_tipo_compras.id  as tipo_id_devol,
            com_tipo_compras.nombre  as tipo_devol,
        	ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, com_compras.consecutivo ASC) AS [NumRegistro]
        from com_compras
        --terceros
        inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
        inner join terceros on terceros.id =  tercero_sucursales.tercero_id
        --compra_prod
        inner join com_pivot_compra_producto on com_pivot_compra_producto.com_compras_id = com_compras.id
        --productos
        inner join productos on productos.id  =  com_pivot_compra_producto.producto_id
        inner join prod_subgrupos on prod_subgrupos.id =  productos.prod_subgrupo_id
        inner join prod_grupos on prod_grupos.id =  prod_subgrupos.prodGrupo_id
        --tipo_doc
        inner join com_tipo_compras on  com_tipo_compras.id  =  com_compras.com_tipo_compras_id
        where com_compras.estado = 3");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW if exists ViewComprasDetails');
        DB::statement('DROP VIEW if exists ViewDevolucionesCompraDetails');
    }
}
