<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class SoenacTipoDocumento extends Model
{
    protected $table = 'soenac_tipos_documento';

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
