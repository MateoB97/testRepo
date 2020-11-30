<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeFacTipoRecTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fac_tipo_rec_caja', function (Blueprint $table) {
            $table->dropForeign('fac_tipo_rec_caja_fac_tipo_doc_id_foreign');
            $table->dropColumn('fac_tipo_doc_id');
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
