<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFacMovimientosNotasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fac_movimientos', function (Blueprint $table) {
            $table->text('nota')->nullable();

            $table->unsignedBigInteger('prod_grupo_id')->nullable();
            $table->foreign('prod_grupo_id')->references('id')->on('prod_grupos');
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
