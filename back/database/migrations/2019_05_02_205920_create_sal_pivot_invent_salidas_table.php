<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSalPivotInventSalidasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sal_pivot_invent_salidas', function (Blueprint $table) {
            $table->bigIncrements('id');
            
            $table->unsignedBigInteger('salMercancia_id');
            $table->foreign('salMercancia_id')->references('id')->on('sal_mercancias');

            $table->unsignedBigInteger('inventario_id');
            $table->foreign('inventario_id')->references('id')->on('inventarios');

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
        Schema::dropIfExists('sal_pivot_invent_salidas');
    }
}
