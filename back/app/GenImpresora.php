<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenImpresora extends Model
{

    protected $table = 'gen_impresoras';

    protected $fillable = ['nombre','ruta','activo'];  

    public function getDateFormat()
    {
       return dateTimeSql();
    }
}
