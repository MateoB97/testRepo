<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTercerosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('terceros', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('documento')->unique();
            $table->string('nombre');
            $table->boolean('proveedor');
            $table->boolean('cliente');
            $table->boolean('empleado');
            $table->boolean('activo')->default(1);
            $table->boolean('habilitado_traslados');
            $table->integer('digito_verificacion')->nullable();

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
        Schema::dropIfExists('terceros');
    }
}
