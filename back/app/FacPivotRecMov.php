<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class FacPivotRecMov extends Model
{
    protected $table = 'fac_pivot_rec_mov';

    protected $fillable = ['fac_recibo_id','fac_mov_id','valor'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function getDataLineasPDF($id){
    return DB::table('fac_pivot_rec_mov')
            ->select(
            	'fac_movimientos.consecutivo As consec_mov',
            	'fac_tipo_doc.nombre as tipo_mov',
                'fac_movimientos.fecha_facturacion as fecha_mov',
                'fac_movimientos.saldo as saldo_mov',
            	'fac_movimientos.total as valor_factura',
            	'fac_pivot_rec_mov.valor as valor'
            )
            ->join('fac_movimientos','fac_movimientos.id', '=', 'fac_pivot_rec_mov.fac_mov_id')
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'fac_movimientos.fac_tipo_doc_id')
            ->orderBy('fac_pivot_rec_mov.id','desc')
            ->where('fac_pivot_rec_mov.fac_recibo_id', $id)
            ->get();
    }
}
