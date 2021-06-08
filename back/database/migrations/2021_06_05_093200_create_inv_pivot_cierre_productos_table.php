<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvPivotCierreProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_pivot_cierre_productos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('precio_al_cierre');

            $table->double('cantidad_stock', 15, 3);
            $table->double('cantidad_cierre', 15, 3);

            $table->unsignedBigInteger('inv_cierre_id');
            $table->foreign('inv_cierre_id')->references('id')->on('inv_cierre_inventario');

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inv_pivot_cierre_productos');
    }
}
