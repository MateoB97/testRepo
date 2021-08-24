<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeLotesTercerosReprocesado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lotes', function (Blueprint $table) {
            $table->boolean('tercero_reprocesado')->nullable()->default();
            $table->date('fecha_venc_refrigerado_granel')->nullable()->default();
            $table->date('fecha_venc_congelado_granel')->nullable()->default();
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
