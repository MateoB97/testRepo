<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTerceroSucursalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tercero_sucursales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('direccion');
            $table->bigInteger('telefono');
            $table->string('nombre');
            $table->boolean('activo')->default(1);

            $table->unsignedBigInteger('tercero_id');
            $table->foreign('tercero_id')->references('id')->on('terceros');

            $table->unsignedBigInteger('prodListaPrecio_id');
            $table->foreign('prodListaPrecio_id')->references('id')->on('prod_lista_precios');

            $table->unsignedBigInteger('gen_municipios_id');
            $table->foreign('gen_municipios_id')->references('id')->on('gen_municipios');

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
        Schema::dropIfExists('tercero_sucursales');
    }
}
