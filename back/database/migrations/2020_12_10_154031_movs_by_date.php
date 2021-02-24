<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MovsByDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("CREATE view MovsByDate
        as
        select * from ViewVentasPorFecha
        UNION
        select * from ViewDevolucionesPorFecha
        UNION
        select * from ViewRecibosPorFecha");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW MovsByDate");
    }
}
