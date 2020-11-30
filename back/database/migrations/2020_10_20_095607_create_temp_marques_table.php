<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTempMarquesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_marques', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->integer('bascula');
            $table->integer('tiquete');
            $table->integer('vendedor');
            $table->integer('linea_tiquete');
            $table->integer('codigo');
            $table->string('producto');
            $table->bigInteger('total');
            $table->double('cantidad', 8, 2);
            $table->string('unidades');
            $table->bigInteger('precio');
            
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
        Schema::dropIfExists('temp_marques');
    }
}
