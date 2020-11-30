<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacPivotRecMovTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_pivot_rec_mov', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_recibo_id');
            $table->foreign('fac_recibo_id')->references('id')->on('fac_recibos_caja');

            $table->unsignedBigInteger('fac_mov_id');
            $table->foreign('fac_mov_id')->references('id')->on('fac_movimientos');

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
        Schema::dropIfExists('fac_pivot_rec_mov');
    }
}
