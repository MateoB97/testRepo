<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalMercanciasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sal_mercancias', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->float('temperatura', 3, 2)->default(4);
            $table->string('vehiculo')->default('Sin datos.');

            $table->unsignedBigInteger('terceroSucursal_id');
            $table->foreign('terceroSucursal_id')->references('id')->on('tercero_sucursales');

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
        Schema::dropIfExists('sal_mercancias');
    }
}
