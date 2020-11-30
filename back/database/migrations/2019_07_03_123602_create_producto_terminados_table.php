<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductoTerminadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('producto_terminados', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('prog_lotes_id');
            $table->foreign('prog_lotes_id')->references('id')->on('lot_programaciones');

            $table->unsignedBigInteger('invent_id');
            $table->foreign('invent_id')->references('id')->on('inventarios');

            $table->string('almacenamiento');
            $table->string('dias_vencimiento');

            $table->timestamps();
            // la fecha de creacion es a fecha de empaque
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('producto_terminados');
    }
}
