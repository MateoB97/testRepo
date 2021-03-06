<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacPivotMovSalmercancia extends Model
{
    protected $table = 'fac_pivot_mov_salmercancia';

    protected $fillable = ['sal_mercancias_id','fac_mov_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
