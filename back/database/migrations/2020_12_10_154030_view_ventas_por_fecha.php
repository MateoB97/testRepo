<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViewVentasPorFecha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view ViewVentasPorFecha
            as select
                fac_movimientos.id As id_venta,
                fac_movimientos.consecutivo As consecutivo_venta,
                fac_movimientos.total as total_venta,
                fac_movimientos.subtotal as subtotal_venta,
                fac_movimientos.saldo as saldo_venta,
                fac_movimientos.fecha_facturacion as fecha_venta,
                tercero_sucursales.nombre as sucursal_venta,
                tercero_sucursales.id as sucursal_id_venta,
                terceros.id as tercero_venta_id,
                terceros.nombre as tercero_venta,
                terceros.documento as documento_venta,
                fac_tipo_doc.id as tipo_id_venta,
                fac_tipo_doc.nombre as tipo_venta,
                ROW_NUMBER() OVER(ORDER BY fac_tipo_doc.nombre, fac_movimientos.consecutivo ASC) AS [NumRegistro]
            from fac_movimientos
            inner join tercero_sucursales on tercero_sucursales.id = fac_movimientos.cliente_id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            inner join fac_tipo_doc on fac_tipo_doc.id = fac_movimientos.fac_tipo_doc_id
            where fac_movimientos.estado != 3
            and fac_tipo_doc_id not in (1,2)
            and fac_tipo_doc.nombre not like '%hab%'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        B::statement("DROP VIEW ViewVentasPorFecha");
    }
}
