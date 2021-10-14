<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class GenIva extends Model
{
    protected $table = 'gen_iva';

	protected $fillable = ['nombre','valor_porcentaje','soenac_iva_api_id','cuenta_contable_venta_id','cuenta_contable_iva_id'];

	public function getDateFormat()
	{
	    return Tools::dateTimeSql();
	}

}
