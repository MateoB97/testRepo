<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateComComprasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('com_compras', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('subtotal');
            $table->bigInteger('descuento');
            $table->bigInteger('ivatotal');
            $table->bigInteger('total');
            $table->bigInteger('saldo');
            $table->integer('estado');
            // 0 pagado, 3 devuelto, 2 pendiente pago
            $table->bigInteger('consecutivo');
            $table->bigInteger('doc_referencia');

            $table->date('fecha_vencimiento');
            $table->date('fecha_compra');

            $table->unsignedBigInteger('com_tipo_compras_id');
            $table->foreign('com_tipo_compras_id')->references('id')->on('com_tipo_compras');

            $table->unsignedBigInteger('proveedor_id');
            $table->foreign('proveedor_id')->references('id')->on('tercero_sucursales');

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
        Schema::dropIfExists('com_compras');
    }
}
