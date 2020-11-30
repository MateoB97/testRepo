<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComPivotCompraProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_pivot_compra_producto', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->double('descporcentaje', 8, 2);
            $table->bigInteger('iva');
            $table->bigInteger('precio');
            $table->double('cantidad', 8, 2);

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedBigInteger('com_compras_id');
            $table->foreign('com_compras_id')->references('id')->on('com_compras');

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
        Schema::dropIfExists('com_pivot_compra_producto');
    }
}
