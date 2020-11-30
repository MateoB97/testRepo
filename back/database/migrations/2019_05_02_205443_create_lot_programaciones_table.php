<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotProgramacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_programaciones', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('num_animales');
            $table->date('fecha_desposte');
            $table->boolean('producto_canal');

            $table->integer('estado')->nullable();
            // 1 activo, 2 liquidado

            $table->unsignedBigInteger('lote_id');
            $table->foreign('lote_id')->references('id')->on('lotes');

            $table->unsignedBigInteger('terceroSucursal_id');
            $table->foreign('terceroSucursal_id')->references('id')->on('tercero_sucursales');

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
        Schema::dropIfExists('lot_programaciones');
    }
}
