<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComPivotCompraEgresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_pivot_compra_egreso', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('com_compro_egresos_id');
            $table->foreign('com_compro_egresos_id')->references('id')->on('com_compro_egresos');

            $table->unsignedBigInteger('com_compras_id');
            $table->foreign('com_compras_id')->references('id')->on('com_compras');

            $table->bigInteger('valor');

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
        Schema::dropIfExists('com_pivot_compra_egreso');
    }
}
