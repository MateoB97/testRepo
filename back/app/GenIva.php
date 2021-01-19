<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenIva extends Model
{
    protected $table = 'gen_iva';

	protected $fillable = ['nombre','valor_porcentaje','soenac_iva_api_id','cuenta_contable_venta','cuenta_contable_iva'];

	public function getDateFormat()
	{
	    return dateTimeSql();
	}
}
