<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EgreTipoEgreso extends Model
{
    protected $table = 'egre_tipo_egreso';

    protected $fillable = ['nombre','naturaleza'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
