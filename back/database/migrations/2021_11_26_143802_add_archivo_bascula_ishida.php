<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddArchivoBasculaIshida extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gen_empresa', function (Blueprint $table) {
            $table->string('ruta_archivo_txt_ishida', 250)->nullable();
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
            $table->dropColumn('ruta_archivo_txt_ishida');
        });
    }
}
