<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGenEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gen_empresa', function (Blueprint $table) {
            $table->renameColumn('ruta_bascula', 'ruta_archivo_tiquetes_dibal');
            $table->string('ruta_archivo_tx_dival')->nullable();
            // enviar datos a bascula "archivo TX"
            $table->string('ruta_ip_marques')->nullable();
            // direccion ip de la bascula principal marques
            $table->bigInteger('tipo_escaner')->nullable();
            // 1 para dibal, 2 para marques, 3 para codigo de barras productos
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
