<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class ComTipoComproEgreso extends Model
{
    protected $table = 'com_tipo_compro_egresos';

    protected $fillable = ['com_tipo_compras_id','consec_inicio','nombre'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function conDocRelacionado(){
    return DB::table('com_tipo_compro_egresos')
            ->select(
            	'com_tipo_compro_egresos.com_tipo_compras_id as com_tipo_compras_id',
            	'com_tipo_compro_egresos.id as id',
            	'com_tipo_compro_egresos.consec_inicio as consec_inicio',
            	'com_tipo_compro_egresos.nombre as nombre',
            	'com_tipo_compras.nombre as tipo_compra_nombre'
			)
            ->join('com_tipo_compras', 'com_tipo_compras.id', '=', 'com_tipo_compro_egresos.com_tipo_compras_id')
            ->get();
    }
}
