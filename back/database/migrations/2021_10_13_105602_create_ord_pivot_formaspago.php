<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdPivotFormaspago extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_pivot_formaspago', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('valor_abonado');

            $table->unsignedBigInteger('ord_orden_id');
            $table->foreign('ord_orden_id')->references('id')->on('ord_ordenes');

            $table->unsignedBigInteger('ord_formas_pago_id');
            $table->foreign('ord_formas_pago_id')->references('id')->on('fac_formas_pago');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ord_pivot_formaspago');
    }
}
