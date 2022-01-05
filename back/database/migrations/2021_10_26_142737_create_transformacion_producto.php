<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransformacionProducto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transformacion_producto', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('producto_id_out');
            $table->integer('cantidad_out');
            $table->integer('producto_id_in');
            $table->integer('cantidad_in');
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
        Schema::dropIfExists('transformacion_producto');
    }
}
