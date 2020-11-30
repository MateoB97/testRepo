<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacPivotMovFormapago extends Model
{
    protected $table = 'fac_pivot_mov_formaspago';

    protected $fillable = ['valor_pagado','valor_recibido','fac_mov_id','fac_formas_pago_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function sumFormapagoCuadre($formapago, $cuadre){
    return DB::table('fac_pivot_mov_formaspago')
            ->select( DB::raw( 'sum(fac_pivot_mov_formaspago.valor_pagado) as valor'))
            ->join('fac_movimientos','fac_movimientos.id', '=', 'fac_pivot_mov_formaspago.fac_mov_id')
            ->where('fac_pivot_mov_formaspago.fac_formas_pago_id', $formapago)
            ->where('fac_movimientos.estado', '!=', 3)
            ->where('fac_movimientos.gen_cuadre_caja_id', $cuadre)
            ->get();
    }

    public static function recaudoPorFecha($formapago, $fechaIni, $fechaFin){
    return DB::table('fac_pivot_mov_formaspago')
            ->select( DB::raw( 'sum(fac_pivot_mov_formaspago.valor_pagado) as valor'))
            ->join('fac_movimientos','fac_movimientos.id', '=', 'fac_pivot_mov_formaspago.fac_mov_id')
            ->where('fac_pivot_mov_formaspago.fac_formas_pago_id', $formapago)
            ->where('fac_movimientos.estado', '!=', 3)
            ->whereBetween('fac_movimientos.fecha_facturacion', [$fechaIni, $fechaFin])
            ->get();
    }

    public static function movimientosConFormaPagoPorFecha($formapago, $fechaIni, $fechaFin){
    return DB::table('fac_pivot_mov_formaspago')
            ->select('fac_pivot_mov_formaspago.valor_pagado as valor','fac_tipo_doc.nombre as tipo','fac_movimientos.consecutivo as consecutivo','fac_movimientos.fecha_facturacion as fecha')
            ->join('fac_movimientos','fac_movimientos.id', '=', 'fac_pivot_mov_formaspago.fac_mov_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->where('fac_pivot_mov_formaspago.fac_formas_pago_id', $formapago)
            ->where('fac_movimientos.estado', '!=', 3)
            ->whereBetween('fac_movimientos.fecha_facturacion', [$fechaIni, $fechaFin])
            ->get();
    }
}
