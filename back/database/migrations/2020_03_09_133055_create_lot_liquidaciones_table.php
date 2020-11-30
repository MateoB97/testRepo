<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotLiquidacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_liquidaciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->float('costoPrecio');
            $table->float('costoSacrificio');
            $table->float('costoDesposte');
            $table->float('costoTransporte');
            $table->float('costoEmpaque');
            $table->float('ppe');
            $table->float('pcc');
            $table->float('pcr');

            $table->string('costoTransportePlantaPunto');

            $table->unsignedBigInteger('prog_lotes_id')->unique();
            $table->foreign('prog_lotes_id')->references('id')->on('lot_programaciones');

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
        Schema::dropIfExists('lot_liquidaciones');
    }
}
