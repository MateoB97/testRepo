<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacMovRelacionadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_mov_relacionados', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_tipo_doc_prim_id');
            $table->foreign('fac_tipo_doc_prim_id')->references('id')->on('fac_tipo_doc');

            $table->unsignedBigInteger('fac_tipo_doc_sec_id')->unique();
            $table->foreign('fac_tipo_doc_sec_id')->references('id')->on('fac_tipo_doc');

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
        Schema::dropIfExists('fac_mov_relacionados');
    }
}
