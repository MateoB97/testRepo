<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacPivotMovProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_pivot_mov_productos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->bigInteger('num_tiquete')->nullable();
            $table->bigInteger('puesto_tiquete')->nullable();
            $table->bigInteger('num_linea_tiquete')->nullable();

            $table->double('descporcentaje', 8, 2);
            $table->bigInteger('iva');
            $table->bigInteger('precio');
            $table->double('cantidad', 8, 2);

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedBigInteger('fac_mov_id');
            $table->foreign('fac_mov_id')->references('id')->on('fac_movimientos');
            
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
        Schema::dropIfExists('fac_pivot_mov_productos');
    }
}
