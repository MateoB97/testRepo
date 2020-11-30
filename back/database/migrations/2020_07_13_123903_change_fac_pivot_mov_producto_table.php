<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFacPivotMovProductoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fac_pivot_mov_productos', function (Blueprint $table) {
            $table->unsignedBigInteger('gen_iva_id')->nullable();
            $table->foreign('gen_iva_id')->references('id')->on('gen_iva');
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
