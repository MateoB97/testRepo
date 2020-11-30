<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalPivotSalProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sal_pivot_sal_producto', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->double('cantidad', 8, 2);

            $table->unsignedBigInteger('producto_id');
            $table->foreign('producto_id')->references('id')->on('productos');

            $table->unsignedBigInteger('sal_mercancia_id');
            $table->foreign('sal_mercancia_id')->references('id')->on('sal_mercancias');

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
        Schema::dropIfExists('sal_pivot_sal_producto');
    }
}
