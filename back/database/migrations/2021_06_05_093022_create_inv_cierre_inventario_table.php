<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvCierreInventarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inv_cierre_inventario', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('fecha_cierre');

            $table->double('total_diferencia_kilos', 15, 3);
            $table->bigInteger('total_diferencia_dinero');

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
        Schema::dropIfExists('inv_cierre_inventario');
    }
}
