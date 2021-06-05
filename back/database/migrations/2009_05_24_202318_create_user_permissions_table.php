<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_permisos', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('nombre');

            $table->unsignedBigInteger('permisos_categoria_id');
            $table->foreign('permisos_categoria_id')->references('id')->on('user_permisos_categorias');

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
        Schema::dropIfExists('user_permissions');
    }
}
