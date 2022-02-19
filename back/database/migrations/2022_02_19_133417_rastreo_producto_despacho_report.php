<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RastreoProductoDespachoReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view trasadoProductoDespacho as
        select
        p.id idProd
        ,p.nombre prodStock
        ,sg.id sbGrupo
        ,sg.nombre sbgNom
        ,g.id grupo
        ,g.nombre gNom
        ,lp.id prog
        ,l.id loteId
        ,l.marca
        ,i.id invId
        ,l.consecutivo loteConsec
        ,i.cantidad stock --cantidad en planta al momento de inv
        ,sp.cantidad cantSal --cantidad de producto despachado
        ,ts.nombre sucDesp
        ,sm.id salida
        ,sm.vehiculo
        ,sm.created_at fechaDesp
        ,pt.almacenamiento
        from
        -- invProdLot
        inventarios i
        inner join producto_terminados pt on pt.invent_id = i.id
        inner join lot_programaciones lp on lp.id = pt.prog_lotes_id
        inner join lotes l on l.id = lp.lote_id
        inner join productos p on p.id = i.producto_id
        inner join prod_subgrupos sg on sg.id = p.prod_subgrupo_id
        inner join prod_grupos g on g.id = sg.prodGrupo_id
        -- salmerInv
        inner join sal_pivot_invent_salidas si on si.inventario_id = i.id
        inner join sal_mercancias sm on sm.id = si.salMercancia_id
        inner join tercero_sucursales ts on ts.id = sm.terceroSucursal_id
        inner join sal_pivot_sal_producto sp on sp.sal_mercancia_id = sm.id and sp.producto_id = p.id
        where
        i.tipo_invent = 2
        go");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('DROP VIEW if exists trasadoProductoDespacho');
    }
}
