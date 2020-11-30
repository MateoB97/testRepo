<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFacTipoDocTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fac_tipo_doc', function (Blueprint $table) {
            $table->bigInteger('soenac_tipo_doc_api_id')->nullable()->default(0);
            $table->bigInteger('resolucion_soenac_id')->nullable()->default(0);
            $table->boolean('habilitacion_fe')->nullable()->default(0);
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
