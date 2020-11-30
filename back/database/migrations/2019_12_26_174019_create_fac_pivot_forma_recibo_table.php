Fa<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacPivotFormaReciboTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_pivot_forma_recibo', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_formas_pago_id');
            $table->foreign('fac_formas_pago_id')->references('id')->on('fac_formas_pago');

            $table->unsignedBigInteger('fac_recibo_id');
            $table->foreign('fac_recibo_id')->references('id')->on('fac_recibos_caja');

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
        Schema::dropIfExists('fac_pivot_forma_recibo');
    }
}
