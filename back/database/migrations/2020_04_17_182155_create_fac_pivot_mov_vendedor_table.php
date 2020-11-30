<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacPivotMovVendedorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_pivot_mov_vendedor', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_mov_id');
            $table->foreign('fac_mov_id')->references('id')->on('fac_movimientos');

            $table->unsignedBigInteger('gen_vendedor_id');
            $table->foreign('gen_vendedor_id')->references('id')->on('gen_vendedores');

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
        Schema::dropIfExists('fac_pivot_mov_vendedor');
    }
}
