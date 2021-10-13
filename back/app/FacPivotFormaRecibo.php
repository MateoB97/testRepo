<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class FacPivotFormaRecibo extends Model
{
    protected $table = 'fac_pivot_forma_recibo';

    protected $fillable = ['fac_formas_pago_id','fac_recibo_id','valor'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function getDataLineasPDF($id){
    return DB::table('fac_pivot_forma_recibo')
            ->select(
            	'fac_formas_pago.nombre As forma_nombre',
            	'fac_pivot_forma_recibo.valor as valor',
            )
            ->join('fac_formas_pago','fac_formas_pago.id', '=', 'fac_pivot_forma_recibo.fac_formas_pago_id')
            ->orderBy('fac_pivot_forma_recibo.id','desc')
            ->where('fac_pivot_forma_recibo.fac_recibo_id', $id)
            ->get();
    }

    public static function sumFormapagoCuadre($formapago, $cuadre){
    return DB::table('fac_pivot_forma_recibo')
            ->select( DB::raw( 'sum(fac_pivot_forma_recibo.valor) as valor'))
            ->join('fac_recibos_caja','fac_recibos_caja.id', '=', 'fac_pivot_forma_recibo.fac_recibo_id')
            ->where('fac_pivot_forma_recibo.fac_formas_pago_id', $formapago)
            ->where('fac_recibos_caja.gen_cuadre_caja_id', $cuadre)
            ->where('fac_recibos_caja.estado', '!=', 0)
            ->get();
    }

    public static function recaudoPorFecha($formapago, $fechaIni, $fechaFin){
    return DB::table('fac_pivot_forma_recibo')
            ->select( DB::raw( 'sum(fac_pivot_forma_recibo.valor) as valor'))
            ->join('fac_recibos_caja','fac_recibos_caja.id', '=', 'fac_pivot_forma_recibo.fac_recibo_id')
            ->where('fac_pivot_forma_recibo.fac_formas_pago_id', $formapago)
            ->whereBetween('fac_recibos_caja.fecha_recibo', [$fechaIni, $fechaFin])
            ->get();
    }

    public static function movimientosConFormaPagoPorFecha($formapago, $fechaIni, $fechaFin){
    return DB::table('fac_pivot_forma_recibo')
            ->select('fac_pivot_forma_recibo.valor as valor','fac_tipo_rec_caja.nombre as tipo','fac_recibos_caja.consecutivo as consecutivo','fac_recibos_caja.fecha_recibo as fecha')
            ->join('fac_recibos_caja','fac_recibos_caja.id', '=', 'fac_pivot_forma_recibo.fac_recibo_id')
            ->join('fac_tipo_rec_caja','fac_tipo_rec_caja.id', '=', 'fac_recibos_caja.fac_tipo_rec_caja_id')
            ->where('fac_pivot_forma_recibo.fac_formas_pago_id', $formapago)
            ->whereBetween('fac_recibos_caja.fecha_recibo', [$fechaIni, $fechaFin])
            ->get();
    }
}
