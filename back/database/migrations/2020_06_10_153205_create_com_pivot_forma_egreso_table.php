<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComPivotFormaEgresoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_pivot_forma_egreso', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_formas_pago_id');
            $table->foreign('fac_formas_pago_id')->references('id')->on('fac_formas_pago');

            $table->unsignedBigInteger('com_compro_egresos_id');
            $table->foreign('com_compro_egresos_id')->references('id')->on('com_compro_egresos');

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
        Schema::dropIfExists('com_pivot_forma_egreso');
    }
}
