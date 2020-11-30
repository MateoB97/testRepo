<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacPivotMovVendedor extends Model
{
    protected $table = 'fac_pivot_mov_vendedor';

    protected $fillable = ['fac_mov_id','gen_vendedor_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
