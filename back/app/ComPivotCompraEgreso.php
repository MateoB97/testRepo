<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class ComPivotCompraEgreso extends Model
{
    protected $table = 'com_pivot_compra_egreso';

    protected $fillable = ['valor','com_compro_egresos_id','com_compras_id'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function getDataLineasPDF($id){
    return DB::table('com_pivot_compra_egreso')
            ->select(
            	'com_compras.consecutivo As consec_mov',
            	'com_tipo_compras.nombre as tipo_mov',
                'com_compras.fecha_compra as fecha_mov',
                'com_compras.saldo as saldo_mov',
            	'com_compras.total as valor_factura',
            	'com_pivot_compra_egreso.valor as valor'
            )
            ->join('com_compras','com_compras.id', '=', 'com_pivot_compra_egreso.com_compras_id')
            ->join('com_tipo_compras','com_tipo_compras.id', '=', 'com_compras.com_tipo_compras_id')
            ->orderBy('com_pivot_compra_egreso.id','desc')
            ->where('com_pivot_compra_egreso.com_compro_egresos_id', $id)
            ->get();
    }
}
