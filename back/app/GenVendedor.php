<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class GenVendedor extends Model
{
   	protected $table = 'gen_vendedores';

	protected $fillable = ['nombre','codigo_unico'];

	public function getDateFormat()
	{
	    return Tools::dateTimeSql();
	}

	public static function listadoVendedoresMarques(){
        return DB::table('gen_vendedores')
            ->select('gen_vendedores.nombre as nome','gen_vendedores.codigo_unico as numero')
            ->get();
    }
}
