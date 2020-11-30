<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacPivotMovFormaspagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_pivot_mov_formaspago', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('valor_pagado');
            $table->bigInteger('valor_recibido');

            $table->unsignedBigInteger('fac_mov_id');
            $table->foreign('fac_mov_id')->references('id')->on('fac_movimientos');

            $table->unsignedBigInteger('fac_formas_pago_id');
            $table->foreign('fac_formas_pago_id')->references('id')->on('fac_formas_pago');

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
        Schema::dropIfExists('fac_pivot_mov_formaspago');
    }
}
