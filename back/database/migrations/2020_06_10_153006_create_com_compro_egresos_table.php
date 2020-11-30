<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComComproEgresosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_compro_egresos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('com_tipo_compro_egresos_id');
            $table->foreign('com_tipo_compro_egresos_id')->references('id')->on('com_tipo_compro_egresos');

            $table->unsignedBigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('tercero_sucursales');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users');

            $table->bigInteger('abono');
            $table->bigInteger('ajuste');
            $table->bigInteger('total');

            $table->text('ajuste_observacion');
            $table->bigInteger('consecutivo');
            $table->date('fecha_comprobante');

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
        Schema::dropIfExists('com_compro_egresos');
    }
}
