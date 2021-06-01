<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacPivotAlmacenamientoLoteTercero extends Model
{
    protected $table = 'fac_pivot_almacenamiento_lote_tercero';

    protected $fillable = ['nombre','lote_id','fecha_vencimiento'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
