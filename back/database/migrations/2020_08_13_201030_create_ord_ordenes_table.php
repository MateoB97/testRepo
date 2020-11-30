<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdOrdenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ord_ordenes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subtotal');
            $table->bigInteger('descuento');
            $table->bigInteger('ivatotal');
            $table->bigInteger('total');
            $table->bigInteger('saldo');
            $table->integer('estado');
            // 0 pagado, 1 con saldo en contra, 2 con saldo a favor, 3 devuelto
            $table->bigInteger('consecutivo');

            $table->date('fecha_orden');

            $table->unsignedBigInteger('ord_tipo_orden_id');
            $table->foreign('ord_tipo_orden_id')->references('id')->on('fac_tipo_doc');

            $table->unsignedBigInteger('tercero_sucursal_id');
            $table->foreign('tercero_sucursal_id')->references('id')->on('tercero_sucursales');

            $table->unsignedBigInteger('usuario_id');
            $table->foreign('usuario_id')->references('id')->on('users');

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
        Schema::dropIfExists('ord_ordenes');
    }
}
