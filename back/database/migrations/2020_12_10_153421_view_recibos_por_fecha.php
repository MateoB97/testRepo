<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViewRecibosPorFecha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view ViewRecibosPorFecha
            as select
                fac_recibos_caja.id As id_recibo,
                fac_recibos_caja.consecutivo As consecutivo_recibo,
                fac_recibos_caja.total as total_recibo,
                fac_recibos_caja.total as subtotal_recibo,
                fac_recibos_caja.total as saldo_recibo,
                fac_recibos_caja.fecha_recibo as fecha_recibo,
                tercero_sucursales.nombre as sucursal_recibo,
                tercero_sucursales.id as sucursal_id_recibo,
                terceros.id as tercero_recibo_id,
                terceros.nombre as tercero_recibo,
                terceros.documento as documento_recibo,
                fac_tipo_rec_caja.id as tipo_id_recibo,
                fac_tipo_rec_caja.nombre as tipo_recibo,
                ROW_NUMBER() OVER(ORDER BY fac_tipo_rec_caja.nombre, fac_recibos_caja.consecutivo ASC) AS [NumRegistro]
            from fac_recibos_caja
            inner join tercero_sucursales  on tercero_sucursales.id =  fac_recibos_caja.tercero_sucursal_id
            inner join terceros on terceros.id =  tercero_sucursales.tercero_id
            inner join fac_tipo_rec_caja on fac_tipo_rec_caja.id = fac_recibos_caja.fac_tipo_rec_caja_id
            where fac_recibos_caja.estado  = 1");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW ViewRecibosPorFecha");
    }
}
