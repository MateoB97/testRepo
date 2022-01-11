<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasXFechaFormaPago extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE OR ALTER VIEW [dbo].[ViewComprasvFechaFormasPago]
        as

        select
        --compras
        	 com_compras.consecutivo as consec_egre
        	,com_compras.id as id_egre
        	,com_compras.subtotal as subtotal_egre
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
        	,ROW_NUMBER() OVER(ORDER BY com_tipo_compro_egresos.nombre, com_compras.consecutivo ASC) AS [NumRegistro] --para compras x fecha
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
        DB::statement('DROP VIEW if exists ViewComprasvFechaFormasPago');
    }
}
