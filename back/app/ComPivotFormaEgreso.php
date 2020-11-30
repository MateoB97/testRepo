<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComPivotFormaEgreso extends Model
{
    protected $table = 'com_pivot_forma_egreso';

    protected $fillable = ['fac_formas_pago_id','com_compro_egresos_id','valor'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function getDataLineasPDF($id){
    return DB::table('com_pivot_forma_egreso')
            ->select(
            	'fac_formas_pago.nombre As forma_nombre',
            	'com_pivot_forma_egreso.valor as valor',
            )
            ->join('fac_formas_pago','fac_formas_pago.id', '=', 'com_pivot_forma_egreso.fac_formas_pago_id')
            ->orderBy('com_pivot_forma_egreso.id','desc')
            ->where('com_pivot_forma_egreso.com_compro_egresos_id', $id)
            ->get();
    }
}
