<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacRecibosCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_recibos_caja', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_tipo_rec_caja_id');
            $table->foreign('fac_tipo_rec_caja_id')->references('id')->on('fac_tipo_rec_caja');

            $table->unsignedBigInteger('tercero_sucursal_id');
            $table->foreign('tercero_sucursal_id')->references('id')->on('tercero_sucursales');

            $table->unsignedBigInteger('gen_cuadre_caja_id');
            $table->foreign('gen_cuadre_caja_id')->references('id')->on('gen_cuadre_caja');

            $table->bigInteger('abono');
            $table->bigInteger('ajuste');
            $table->bigInteger('total');

            $table->text('ajuste_observacion');
            $table->bigInteger('consecutivo');
            $table->date('fecha_recibo');

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
        Schema::dropIfExists('fac_recibos_caja');
    }
}
