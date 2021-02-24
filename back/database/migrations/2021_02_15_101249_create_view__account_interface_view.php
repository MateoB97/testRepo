<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateViewAccountInterfaceView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view AccountInterfaceView
            as select
                dd.prefijo,
                dd.consecutivo,
                dd.fecha_facturacion,
                sum(dd.subtotal) as sub,
                sum(iva) as iva,
                sum(total) as total,
                dd.cuenta_contable_iva,
                dd.cuenta_contable_venta,
                dd.valor_porcentaje,
                dd.nombre_contable_venta,
                dd.nombre_contable_iva
            from (
                select
                    fac_tipo_doc.prefijo,
                    fac_movimientos.consecutivo,
                    fac_movimientos.fecha_facturacion,
                    a.precio * cantidad as subtotal,
                    (a.precio * cantidad) * (cast(iv.valor_porcentaje as float)/100) as iva,
                    (a.precio * cantidad) + ((a.precio * cantidad) * (cast(iv.valor_porcentaje as float)/100)) as total,
                    case when productos.cuenta_contable_venta_id is null then gpv.codigo else gp.codigo end as cuenta_contable_venta,
                    case when productos.cuenta_contable_venta_id is null then gpv.nombre else gp.nombre end as nombre_contable_venta,
                    iv.valor_porcentaje,
                    gpi.codigo as cuenta_contable_iva,
                    gpi.nombre as nombre_contable_iva
                from fac_pivot_mov_productos a
                inner join productos on a.producto_id = productos.id
                inner join fac_movimientos on fac_movimientos.id = a.fac_mov_id
                inner join fac_tipo_doc on fac_movimientos.fac_tipo_doc_id = fac_tipo_doc.id
                inner join gen_iva iv on iv.valor_porcentaje = a.iva
                full join gen_puc gp on gp.id = productos.cuenta_contable_venta_id
                full join gen_puc gpi on gpi.id = iv.cuenta_contable_iva_id
                full join gen_puc gpv on gpv.id = iv.cuenta_contable_venta_id
                inner join tercero_sucursales ts on fac_movimientos.cliente_id = ts.id
                where a.iva != 0
            )dd group by dd.prefijo, dd.consecutivo, dd.fecha_facturacion, dd.cuenta_contable_venta, dd.valor_porcentaje, dd.cuenta_contable_iva, dd.nombre_contable_venta, dd.nombre_contable_iva");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW AccountInterfaceView");
    }
}
