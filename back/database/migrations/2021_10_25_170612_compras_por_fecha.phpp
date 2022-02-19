<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ComprasPorFecha extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE VIEW [dbo].[ViewComprasPorFecha]
            as select
            --com_compras
                com_compras.id As id_compra,
                com_compras.consecutivo As consecutivo_compra,
                com_compras.total as total_compra,
                com_compras.subtotal as subtotal_compra,
                com_compras.saldo as saldo_compra,
                com_compras.fecha_compra as fecha_compra,
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
                ROW_NUMBER() OVER(ORDER BY com_tipo_compras.nombre, com_compras.consecutivo ASC) AS [NumRegistro]
            from com_compras
			inner join tercero_sucursales on tercero_sucursales.id = com_compras.proveedor_id
			inner join terceros on terceros.id = tercero_sucursales.tercero_id
			inner join com_tipo_compras on com_tipo_compras.id = com_compras.com_tipo_compras_id
            where com_compras.estado != 3"
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       DB::statement('DROP VIEW if exists ViewComprasPorFecha');
    }
}
