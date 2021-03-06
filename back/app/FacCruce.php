<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacCruce extends Model
{
    protected $table = 'fac_cruce';

    protected $fillable = ['fac_mov_principal','fac_mov_secundario'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
