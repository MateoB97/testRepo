<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenBascula extends Model
{
    protected $table = 'gen_basculas';

    protected $fillable = ['nombre','ruta','activo'];  

    public function getDateFormat()
    {
       return dateTimeSql();
    }
}
