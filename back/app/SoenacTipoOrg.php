<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoenacTipoOrg extends Model
{
    protected $table = 'soenac_tipo_organizaciones';

    protected $fillable = [
    	'nombre',
    	'soenac_id',
    	'codigo'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
