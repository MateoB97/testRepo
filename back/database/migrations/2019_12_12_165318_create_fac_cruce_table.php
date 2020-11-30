<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacCruceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_cruce', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_mov_principal');
            $table->foreign('fac_mov_principal')->references('id')->on('fac_movimientos');

            $table->unsignedBigInteger('fac_mov_secundario');
            $table->foreign('fac_mov_secundario')->references('id')->on('fac_movimientos');

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
        Schema::dropIfExists('fac_cruce');
    }
}
