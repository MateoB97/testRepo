<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFacTipoDocumentoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fac_tipo_doc', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nombre');
            $table->string('nombre_alt');
            $table->integer('naturaleza');
            // (0 para devolucion, 1 para factura, 2 para nota credito, 3 para nota debito, 4 para POS)
            $table->integer('ini_num_fac')->nullable();
            $table->integer('fin_num_fac')->nullable();
            $table->bigInteger('resolucion')->nullable();
            $table->date('fec_resolucion')->nullable();
            $table->string('prefijo')->nullable();
            $table->text('nota')->nullable();
            $table->integer('formato_impresion');
            // (1 para factura, 2 cuenta de cobro)
            $table->boolean('traslado');
            $table->integer('consec_inicio');
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
        Schema::dropIfExists('fac_tipo_doc');
    }
}
