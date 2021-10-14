<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class SoenacRegimen extends Model
{
    protected $table = 'soenac_regimenes';

    protected $fillable = [
    	'nombre',
    	'soenac_id',
    	'codigo'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
