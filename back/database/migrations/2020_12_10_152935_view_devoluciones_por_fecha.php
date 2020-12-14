<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViewDevolucionesPorFecha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view ViewDevolucionesPorFecha
            as select
                fac_movimientos.id As id_devol,
                fac_movimientos.consecutivo As consecutivo_devol,
                fac_movimientos.total as total_devol,
                fac_movimientos.subtotal as subtotal_devol,
                fac_movimientos.saldo as saldo_devol,
                fac_movimientos.fecha_facturacion as fecha_devol,
                tercero_sucursales.nombre as sucursal_devol,
                tercero_sucursales.id as sucursal_id_devol,
                terceros.id as tercero_devol_id,
                terceros.nombre as tercero_devol,
                terceros.documento as documento_devol,
                fac_tipo_doc.id as tipo_id_devol,
                'Devolucion - ' + fac_tipo_doc.nombre as tipo_devol,
                ROW_NUMBER() OVER(ORDER BY fac_tipo_doc.nombre, fac_movimientos.consecutivo ASC) AS [NumRegistro]
            from fac_movimientos
            inner join tercero_sucursales on tercero_sucursales.id = fac_movimientos.cliente_id
            inner join terceros on terceros.id = tercero_sucursales.tercero_id
            inner join fac_tipo_doc on fac_tipo_doc.id = fac_movimientos.fac_tipo_doc_id
            where fac_movimientos.estado = 3
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
        DB::statement("DROP VIEW ViewDevolucionesPorFecha");
    }
}
