<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeProdAlmacenamientoTerceroReprocesado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prod_almacenamientos', function (Blueprint $table) {
            $table->boolean('reprocesado')->nullable()->default();
            $table->integer('empaque')->nullable()->default();
            $table->integer('almacenamiento')->nullable()->default();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
