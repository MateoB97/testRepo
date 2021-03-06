<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotEtiquetaInternaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_etiqueta_interna', function (Blueprint $table) {
            $table->bigIncrements('id');
            

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedBigInteger('prog_lotes_id');
            $table->foreign('prog_lotes_id')->references('id')->on('lot_programaciones');

            $table->boolean('reimpresion');

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
        Schema::dropIfExists('lot_etiqueta_interna');
    }
}
