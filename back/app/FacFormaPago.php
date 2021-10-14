<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class FacFormaPago extends Model
{
    protected $table = 'fac_formas_pago';

    protected $fillable = ['nombre'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
