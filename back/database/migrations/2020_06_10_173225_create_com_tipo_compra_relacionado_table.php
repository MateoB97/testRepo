<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComTipoCompraRelacionadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_tipo_compra_relacionado', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('com_tipo_compra_prim_id');
            $table->foreign('com_tipo_compra_prim_id')->references('id')->on('com_tipo_compras');

            $table->unsignedBigInteger('com_tipo_compra_sec_id')->unique();
            $table->foreign('com_tipo_compra_sec_id')->references('id')->on('com_tipo_compras');

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
        Schema::dropIfExists('com_tipo_compra_relacionado');
    }
}
