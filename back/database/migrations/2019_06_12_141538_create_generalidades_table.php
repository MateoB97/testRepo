<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGeneralidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('generalidades', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->unsignedBigInteger('prod_cuarto_dela');
            $table->foreign('prod_cuarto_dela')->references('id')->on('productos');

            $table->unsignedBigInteger('prod_cuarto_tras');
            $table->foreign('prod_cuarto_tras')->references('id')->on('productos');

            $table->unsignedBigInteger('prod_canal');
            $table->foreign('prod_canal')->references('id')->on('productos');

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
        Schema::dropIfExists('generalidades');
    }
}
