<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasXFechaSucursales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW [dbo].[ViewComprasPorFechaSucursal]
            as select
			--com_compras
                com_compras.id As id_compra,
                com_compras.consecutivo As consecutivo_compra,
                com_compras.total as total_compra,
                com_compras.subtotal as subtotal_compra,
                com_compras.saldo as saldo_compra,
                com_compras.fecha_compra as fecha_compra,
				com_compras.descuento as descuento_compra,
				--terceros
                tercero_sucursales.nombre as sucursal_compra,
                tercero_sucursales.id as sucursal_id_compra,
                terceros.id as tercero_compra_id,
                terceros.nombre as tercero_compra,
                terceros.documento as documento_compra,
				--tipo_doc
                com_tipo_compras.id as tipo_id_compra,
                com_tipo_compras.nombre as tipo_compra,
				--orden sintetico para org jasper tipos_columns
                ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, terceros.nombre, tercero_sucursales.nombre ASC) AS [NumRegistro] --com_compras.consecutivo,
            from com_compras
			inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
			inner join terceros on terceros.id = tercero_sucursales.tercero_id
			inner join com_tipo_compras on com_tipo_compras.id = com_compras.com_tipo_compras_id
            where com_compras.estado != 3");

            DB::statement("CREATE VIEW  [dbo].[ViewDevolucionesCompraPorFechaSucursal]
            as select
                com_compras.id As id_devol,
                com_compras.consecutivo As consecutivo_devol,
                com_compras.total as total_devol,
                com_compras.subtotal as subtotal_devol,
                com_compras.saldo as saldo_devol,
                com_compras.fecha_compra as fecha_devol,
				com_compras.descuento as descuento_devol,
                tercero_sucursales.nombre as sucursal_devol,
                tercero_sucursales.id as sucursal_id_devol,
                terceros.id as tercero_devol_id,
                terceros.nombre as tercero_devol,
                terceros.documento as documento_devol,
                com_tipo_compras.id as tipo_id_devol,
                com_tipo_compras.nombre as tipo_devol,
                ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, terceros.nombre, tercero_sucursales.nombre ASC) AS [NumRegistro] --com_compras.consecutivo
            from com_compras
			inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
			inner join terceros on terceros.id = tercero_sucursales.tercero_id
			inner join com_tipo_compras on com_tipo_compras.id = com_compras.com_tipo_compras_id
            where com_compras.estado = 3");

            DB::statement("CREATE VIEW [dbo].[ViewComprasxFechaFormasPagoSucursal]
            as

            select
            --compras
            	 com_compras.consecutivo as consec_egre
            	,com_compras.id as id_egre
            	,com_compras.subtotal as subtotal_egre
            	,com_compras.descuento as descuento_egre
            --terceros
            	,tercero_sucursales.nombre as sucursal_egre
                ,tercero_sucursales.id as sucursal_id_egre
                ,terceros.id as tercero_egre_id
                ,terceros.nombre as tercero_egre
                ,terceros.documento as documento_egre
            	--tipo_doc
                ,com_tipo_compras.id as tipo_id_egre
                ,com_tipo_compras.nombre as tipo_egre
            	,com_tipo_compro_egresos.nombre as compro_nombre
            	--formas pago
            	,com_pivot_forma_egreso.valor as compro_abono_egre
            	,com_compro_egresos.total as compro_total_egre
            	,com_compro_egresos.fecha_comprobante as compro_fecha_egre
            	,fac_formas_pago.id as id_fac
            	,fac_formas_pago.nombre as formas_fac
            	,ROW_NUMBER() OVER(ORDER BY com_tipo_compro_egresos.nombre, terceros.nombre, tercero_sucursales.nombre ASC) AS [NumRegistro] --para compras x fecha -- com_compras.consecutivo,
            	--,ROW_NUMBER() OVER(ORDER BY fac_formas_pago.nombre, com_compras.consecutivo ASC) AS [NumRegistro]  --para compras forma pago
            from com_compras
            --terceros
            inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            --pivot compra egreso
            inner join com_pivot_compra_egreso on com_pivot_compra_egreso.com_compras_id = com_compras.id
            --compro egreso
            inner join com_compro_egresos on com_compro_egresos.id = com_pivot_compra_egreso.com_compro_egresos_id
            inner join com_tipo_compro_egresos on com_tipo_compro_egresos.id = com_compro_egresos.com_tipo_compro_egresos_id
            --pivot compro_egre-fac
            inner join com_pivot_forma_egreso on com_pivot_forma_egreso.com_compro_egresos_id = com_compro_egresos.id
            inner join fac_formas_pago on fac_formas_pago.id = com_pivot_forma_egreso.fac_formas_pago_id
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
        DB::statement('DROP VIEW if exists ViewComprasPorFechaSucursal');
        DB::statement('DROP VIEW if exists ViewDevolucionesCompraPorFechaSucursal');
        DB::statement('DROP VIEW if exists ViewComprasxFechaFormasPagoSucursal');
    }
}
