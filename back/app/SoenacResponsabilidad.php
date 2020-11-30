<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SoenacResponsabilidad extends Model
{
    protected $table = 'soenac_responsabilidades';

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
