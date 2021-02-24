<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ViewSaldosCartera extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view ViewSaldosCartera
            as select
                fac_movimientos.id As id,
                fac_movimientos.consecutivo As consecutivo,
                fac_movimientos.total as total,
                fac_movimientos.saldo as saldo,
                fac_movimientos.estado as estado,
                replace(convert(varchar, fecha_facturacion, 103),'/','-' ) as fecha_facturacion,
                replace(convert(varchar, fecha_vencimiento, 103),'/','-' ) as fecha_vencimiento,
                tercero_sucursales.nombre as sucursal,
                tercero_sucursales.id as sucursal_id,
                tercero_sucursales.direccion as direccion,
                tercero_sucursales.telefono as telefono,
                terceros.id as tercero_id,
                terceros.nombre as tercero,
                terceros.documento as documento,
                fac_tipo_doc.nombre as tipo
            from fac_movimientos
            inner join tercero_sucursales on tercero_sucursales.id =  fac_movimientos.cliente_id
            inner join terceros on terceros.id =  tercero_sucursales.tercero_id
            inner join fac_tipo_doc on fac_tipo_doc.id = fac_movimientos.fac_tipo_doc_id
            where fac_tipo_doc.naturaleza =  1 and fac_movimientos.estado =  1
            ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW ViewSaldosCartera");
    }
}
