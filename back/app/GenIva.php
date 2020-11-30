<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenIva extends Model
{
    protected $table = 'gen_iva';

	protected $fillable = ['nombre','valor_porcentaje','soenac_iva_api_id'];

	public function getDateFormat()
	{
	    return dateTimeSql();
	}
}
