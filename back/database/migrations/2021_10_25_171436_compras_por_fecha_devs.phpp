<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasPorFechaDevs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW [dbo].[ViewDevolucionesCompraPorFecha]
            as select
                com_compras.id As id_devol,
                com_compras.consecutivo As consecutivo_devol,
                com_compras.total as total_devol,
                com_compras.subtotal as subtotal_devol,
                com_compras.saldo as saldo_devol,
                com_compras.fecha_compra as fecha_devol,
                tercero_sucursales.nombre as sucursal_devol,
                tercero_sucursales.id as sucursal_id_devol,
                terceros.id as tercero_devol_id,
                terceros.nombre as tercero_devol,
                terceros.documento as documento_devol,
                com_tipo_compras.id as tipo_id_devol,
                com_tipo_compras.nombre as tipo_devol,
                ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, com_compras.consecutivo ASC) AS [NumRegistro]
            from com_compras
			inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
			inner join terceros on terceros.id = tercero_sucursales.tercero_id
			inner join com_tipo_compras on com_tipo_compras.id = com_compras.com_tipo_compras_id
            where com_compras.estado = 3");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW if exists ViewDevolucionesCompraPorFecha');
    }
}
