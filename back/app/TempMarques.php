<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class TempMarques extends Model
{
    protected $table = 'temp_marques';

    protected $fillable = [
    	'bascula',
    	'tiquete',
    	'vendedor',
    	'linea_tiquete',
    	'codigo',
        'producto',
        'total',
        'cantidad',
    	'unidades',
    	'precio'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

}
