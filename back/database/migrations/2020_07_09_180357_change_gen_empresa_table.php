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
            $table->string('token_fac_elect')->nullable()->default('n/a');
            $table->string('test_id_fe')->nullable()->default('n/a');

            $table->unsignedBigInteger('producto_bolsa_id')->nullable()->default(1);
            $table->foreign('producto_bolsa_id')->references('id')->on('productos');

            $table->unsignedBigInteger('gen_iva_excluido_id')->nullable()->default(2);
            $table->foreign('gen_iva_excluido_id')->references('id')->on('gen_iva');
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
