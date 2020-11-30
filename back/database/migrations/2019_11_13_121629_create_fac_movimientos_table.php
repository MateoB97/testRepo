2<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacMovimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_movimientos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subtotal');
            $table->bigInteger('descuento');
            $table->bigInteger('ivatotal');
            $table->bigInteger('total');
            $table->bigInteger('saldo');
            $table->integer('estado');
            // 0 pagado, 1 con saldo en contra, 2 con saldo a favor, 3 devuelto
            $table->bigInteger('consecutivo');

            $table->date('fecha_vencimiento');
            $table->date('fecha_facturacion');

            $table->unsignedBigInteger('fac_tipo_doc_id');
            $table->foreign('fac_tipo_doc_id')->references('id')->on('fac_tipo_doc');

            $table->unsignedBigInteger('cliente_id');
            $table->foreign('cliente_id')->references('id')->on('tercero_sucursales');

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
        Schema::dropIfExists('fac_movimientos');
    }
}
