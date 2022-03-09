<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRowSeccionesBasculasOnGenEmpresaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gen_empresa', function (Blueprint $table) {
            $table->integer('secciones_dibal')->default(1);
            $table->integer('secciones_epelsa')->default(1);
            $table->integer('secciones_ishida')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gen_empresa', function (Blueprint $table) {
            $table->dropColumn('secciones_dibal');
            $table->dropColumn('secciones_epelsa');
            $table->dropColumn('secciones_ishida');
        });
    }
}
