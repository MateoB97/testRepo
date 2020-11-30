<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEgreTipoEgresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('egre_tipo_egreso', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nombre');
            $table->integer('naturaleza');
            // 1 para gasto, 2 para entrega de efectivo

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
        Schema::dropIfExists('egre_tipo_egreso');
    }
}
