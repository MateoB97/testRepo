<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre')->unique();
            $table->string('codigo')->unique();
            
            $table->boolean('activo')->default(1);

            $table->unsignedBigInteger('prod_subgrupo_id');
            $table->foreign('prod_subgrupo_id')->references('id')->on('prod_subgrupos');

            $table->unsignedBigInteger('gen_iva_id');
            $table->foreign('gen_iva_id')->references('id')->on('gen_iva');

            $table->unsignedBigInteger('gen_unidades_id');
            $table->foreign('gen_unidades_id')->references('id')->on('gen_unidades');
            
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
        Schema::dropIfExists('productos');
    }
}
