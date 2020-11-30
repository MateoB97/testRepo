<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenCuadreCajaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gen_cuadre_caja', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('monto_apertura');
            $table->bigInteger('monto_cierre')->nullable();
            $table->bigInteger('total_egresos')->nullable();
            $table->integer('estado');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users');

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
        Schema::dropIfExists('gen_cuadre_caja');
    }
}
