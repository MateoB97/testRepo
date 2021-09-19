<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSoenacPivotCorrectionFacMovConcepts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('soenac_pivot_correction_fac_mov_concepts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('correction_id')->unsigned();
            $table->foreign('correction_id')->references('correction_soenac_id')->on('soenac_correction_concepts');
            $table->bigInteger('fac_mov_id')->unsigned();
            $table->foreign('fac_mov_id')->references('id')->on('fac_movimientos');
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
        Schema::dropIfExists('soenac_pivot_correction_fac_mov_concepts');
    }
}
