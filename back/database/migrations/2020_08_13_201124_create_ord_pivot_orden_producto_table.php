<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdPivotOrdenProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_pivot_orden_producto', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->double('descporcentaje', 8, 2);
            $table->bigInteger('iva');
            $table->bigInteger('precio');
            $table->double('cantidad', 8, 2);

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedBigInteger('ord_orden_id');
            $table->foreign('ord_orden_id')->references('id')->on('ord_ordenes');

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
        Schema::dropIfExists('ord_pivot_orden_producto');
    }
}
