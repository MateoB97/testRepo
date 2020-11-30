<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLotProgPesosAnimalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lot_prog_pesos_animales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('num_animal');
            $table->float('pcc');
            $table->float('pcr');
            $table->float('ppe');
            
            $table->unsignedBigInteger('lotProgramacion_id');
            $table->foreign('lotProgramacion_id')->references('id')->on('lot_programaciones');

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
        Schema::dropIfExists('lot_prog_pesos_animales');
    }
}
