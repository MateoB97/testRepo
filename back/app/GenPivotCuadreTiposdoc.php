<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenPivotCuadreTiposdoc extends Model
{
    protected $table = 'gen_pivot_cuadre_tiposdoc';

    protected $fillable = [
    	'gen_cuadre_caja_id',
    	'fac_tipo_doc_id',
    	'total',
    	'iva',
    	'subtotal',
    	'descuento',
    	'devolucion_total',
    	'devolucion_iva',
    	'devolucion_subtotal',
    	'devolucion_descuento'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function porCuadreConTipodoc($id){
    return DB::table('gen_pivot_cuadre_tiposdoc')
            ->select(
                'gen_pivot_cuadre_tiposdoc.total',
                'gen_pivot_cuadre_tiposdoc.iva',
                'gen_pivot_cuadre_tiposdoc.subtotal',
                'gen_pivot_cuadre_tiposdoc.descuento',
                'gen_pivot_cuadre_tiposdoc.devolucion_total',
                'gen_pivot_cuadre_tiposdoc.devolucion_iva',
                'gen_pivot_cuadre_tiposdoc.devolucion_subtotal',
                'gen_pivot_cuadre_tiposdoc.devolucion_descuento',
                'fac_tipo_doc.nombre as tipodoc'
            )
            ->join('fac_tipo_doc','fac_tipo_doc.id', '=', 'gen_pivot_cuadre_tiposdoc.fac_tipo_doc_id')
            ->where('gen_pivot_cuadre_tiposdoc.gen_cuadre_caja_id', $id)
            ->where('fac_tipo_doc.traslado', 0)
            ->get();
    }
}
