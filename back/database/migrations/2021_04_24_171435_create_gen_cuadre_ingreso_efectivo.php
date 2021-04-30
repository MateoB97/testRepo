<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenCuadreIngresoEfectivo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gen_cuadre_ingreso_efectivo', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('descripcion');
            $table->bigInteger('valor');
            $table->bigInteger('consecutivo');

            $table->unsignedBigInteger('gen_cuadre_caja_id');
            $table->foreign('gen_cuadre_caja_id')->references('id')->on('gen_cuadre_caja');

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
        Schema::dropIfExists('gen_cuadre_ingreso_efectivo');
    }
}
