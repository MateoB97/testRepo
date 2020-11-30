<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenPivotCuadreFormapago extends Model
{
    protected $table = 'gen_pivot_cuadre_formapago_caja';

    protected $fillable = ['gen_cuadre_caja_id','fac_formas_pago_id','valor','referente'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function sumCuadrePorForma($gen_cuadre_caja_id, $forma){
    return DB::table('gen_pivot_cuadre_formapago_caja')
            ->select( DB::raw( 'sum(gen_pivot_cuadre_formapago_caja.valor) as valor'))
            ->join('fac_formas_pago','fac_formas_pago.id', '=', 'gen_pivot_cuadre_formapago_caja.fac_formas_pago_id')
            ->where('gen_pivot_cuadre_formapago_caja.gen_cuadre_caja_id', $gen_cuadre_caja_id)
            ->where('gen_pivot_cuadre_formapago_caja.fac_formas_pago_id', $forma)
            ->get();
    }

    public static function sumCuadreRecibos($gen_cuadre_caja_id){
    return DB::table('gen_pivot_cuadre_formapago_caja')
            ->select( DB::raw( 'sum(gen_pivot_cuadre_formapago_caja.valor) as valor'))
            ->join('fac_formas_pago','fac_formas_pago.id', '=', 'gen_pivot_cuadre_formapago_caja.fac_formas_pago_id')
            ->where('gen_pivot_cuadre_formapago_caja.gen_cuadre_caja_id', $gen_cuadre_caja_id)
            ->where('gen_pivot_cuadre_formapago_caja.referente', 2)
            ->get();
    }

}
