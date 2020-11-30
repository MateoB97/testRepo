<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGenEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gen_empresa', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nombre');
            $table->string('nit');
            $table->string('direccion');
            $table->string('telefono');
            $table->string('razon_social');
            $table->string('tipo_regimen');
            $table->string('ruta_archivo_tiquetes_dibal');
            // lectura de tiquetes
            $table->string('ruta_archivo_tx_dival');
            // enviar datos a bascula "archivo TX"
            $table->string('ruta_ip_marques');
            // direccion ip de la bascula principal marques
            $table->bigInteger('tipo_escaner');
            // 1 para dibal, 2 para marques, 3 para codigo de barras productos

            $table->unsignedBigInteger('tercero_sucursal_pos_id');
            $table->foreign('tercero_sucursal_pos_id')->references('id')->on('tercero_sucursales');

            $table->unsignedBigInteger('prod_lista_precios_id');
            $table->foreign('prod_lista_precios_id')->references('id')->on('prod_lista_precios');

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
        Schema::dropIfExists('gen_empresa');
    }
}
