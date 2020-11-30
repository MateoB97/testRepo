<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEgreEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('egre_egresos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('descripcion');
            $table->bigInteger('valor');
            $table->string('forma_pago');
            $table->bigInteger('consecutivo');

            $table->unsignedBigInteger('gen_cuadre_caja_id');
            $table->foreign('gen_cuadre_caja_id')->references('id')->on('gen_cuadre_caja');

            $table->unsignedBigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('tercero_sucursales');

            $table->unsignedBigInteger('egre_tipo_egreso_id');
            $table->foreign('egre_tipo_egreso_id')->references('id')->on('egre_tipo_egreso');

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
        Schema::dropIfExists('egre_egresos');
    }
}
