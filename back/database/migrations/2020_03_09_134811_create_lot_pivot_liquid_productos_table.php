<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotPivotLiquidProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_pivot_liquid_productos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->float('precio_venta');
            $table->float('cantidad');
            $table->boolean('vacio');
            $table->integer('tipo_producto');

            $table->unsignedBigInteger('lot_liquidaciones_id');
            $table->foreign('lot_liquidaciones_id')->references('id')->on('lot_liquidaciones');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

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
        Schema::dropIfExists('lot_pivot_liquid_productos');
    }
}
