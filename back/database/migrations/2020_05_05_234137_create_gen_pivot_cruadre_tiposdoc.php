<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenPivotCruadreTiposdoc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gen_pivot_cuadre_tiposdoc', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('total');
            $table->bigInteger('iva');
            $table->bigInteger('subtotal');
            $table->bigInteger('descuento');
            $table->bigInteger('devolucion_total');
            $table->bigInteger('devolucion_iva');
            $table->bigInteger('devolucion_subtotal');
            $table->bigInteger('devolucion_descuento');

            $table->unsignedBigInteger('fac_tipo_doc_id');
            $table->foreign('fac_tipo_doc_id')->references('id')->on('fac_tipo_doc');

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
        Schema::dropIfExists('gen_pivot_cuadre_tiposdoc');
    }
}
