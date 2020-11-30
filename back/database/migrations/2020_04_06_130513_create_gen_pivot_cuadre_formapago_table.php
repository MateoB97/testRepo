<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenPivotCuadreFormapagoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gen_pivot_cuadre_formapago_caja', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('gen_cuadre_caja_id');
            $table->foreign('gen_cuadre_caja_id')->references('id')->on('gen_cuadre_caja');

            $table->integer('referente');
            // 1 POS , 2 RC

            $table->unsignedBigInteger('fac_formas_pago_id');
            $table->foreign('fac_formas_pago_id')->references('id')->on('fac_formas_pago');

            $table->bigInteger('valor');

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
        Schema::dropIfExists('gen_pivot_cuadre_formapago_caja');
    }
}
