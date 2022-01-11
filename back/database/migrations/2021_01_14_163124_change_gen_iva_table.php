<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeGenIvaTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gen_iva', function (Blueprint $table) {

            $table->unsignedBigInteger('cuenta_contable_iva_id')->nullable();
            $table->foreign('cuenta_contable_iva_id')->references('id')->on('gen_puc');

            $table->unsignedBigInteger('cuenta_contable_venta_id')->nullable();
            $table->foreign('cuenta_contable_venta_id')->references('id')->on('gen_puc');
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
