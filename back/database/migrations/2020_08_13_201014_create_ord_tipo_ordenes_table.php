<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdTipoOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_tipo_ordenes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');

            $table->unsignedBigInteger('fac_tipo_doc_id')->nullable()->unique();
            $table->foreign('fac_tipo_doc_id')->references('id')->on('fac_tipo_doc');

            $table->unsignedBigInteger('com_tipo_compra_id')->nullable()->unique();
            $table->foreign('com_tipo_compra_id')->references('id')->on('com_tipo_compras');

            $table->integer('consec_inicio');

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
        Schema::dropIfExists('ord_tipo_ordenes');
    }
}
