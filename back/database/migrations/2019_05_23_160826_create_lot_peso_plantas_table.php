<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotPesoPlantasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_peso_plantas', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->float('peso', 8, 3);
            $table->float('temperatura', 8, 2)->nullable();
            $table->boolean('olor');
            $table->boolean('color');
            $table->boolean('sin_sustancias');
            $table->boolean('cumple');
            $table->text('observaciones')->nullable();

            $table->float('ph', 8, 2)->nullable();

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedBigInteger('lote_id');
            $table->foreign('lote_id')->references('id')->on('lotes');


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
        Schema::dropIfExists('lot_peso_plantas');
    }
}
