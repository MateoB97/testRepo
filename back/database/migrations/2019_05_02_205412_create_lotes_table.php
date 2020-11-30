<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lotes', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('fecha_sacrificio');
            $table->date('fecha_peso_pie')->nullable();
            $table->boolean('marinado')->nullable();
            $table->boolean('genero')->nullable();
            $table->boolean('producto_empacado');
            $table->boolean('estado');
            $table->string('lote_proveedor')->nullable();
            $table->bigInteger('num_animales')->nullable();

            $table->unsignedBigInteger('com_compras_id');
            $table->foreign('com_compras_id')->references('id')->on('com_compras');

            $table->float('pcc');
            $table->float('ppe');

            $table->unsignedBigInteger('prodGrupo_id');
            $table->foreign('prodGrupo_id')->references('id')->on('prod_grupos');

            $table->string('marca');

            $table->unsignedBigInteger('transportador_id');
            $table->foreign('transportador_id')->references('id')->on('tercero_sucursales');

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
        Schema::dropIfExists('lotes');
    }
}
