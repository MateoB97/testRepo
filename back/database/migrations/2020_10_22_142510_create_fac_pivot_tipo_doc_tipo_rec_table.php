<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacPivotTipoDocTipoRecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_pivot_tipo_doc_tipo_rec', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('fac_tipo_doc_id');
            $table->foreign('fac_tipo_doc_id')->references('id')->on('fac_tipo_doc');

            $table->unsignedBigInteger('fac_tipo_rec_id');
            $table->foreign('fac_tipo_rec_id')->references('id')->on('fac_tipo_rec_caja');

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
        Schema::dropIfExists('fac_pivot_tipo_doc_tipo_rec');
    }
}
