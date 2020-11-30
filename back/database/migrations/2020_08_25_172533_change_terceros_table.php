<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTercerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('terceros', function (Blueprint $table) {

            $table->unsignedBigInteger('soenac_regim_id')->nullable()->default(2);;
            $table->foreign('soenac_regim_id')->references('id')->on('soenac_regimenes');

            $table->unsignedBigInteger('soenac_responsab_id')->nullable()->default(29);
            $table->foreign('soenac_responsab_id')->references('id')->on('soenac_responsabilidades');

            $table->unsignedBigInteger('soenac_tipo_org_id')->nullable()->default(2);
            $table->foreign('soenac_tipo_org_id')->references('id')->on('soenac_tipo_organizaciones');

            $table->unsignedBigInteger('soenac_tipo_documento_id')->nullable()->default(2);
            $table->foreign('soenac_tipo_documento_id')->references('id')->on('soenac_tipos_documento');

            $table->string('registro_mercantil')->nullable();

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
