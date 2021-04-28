<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LotPivotEntprodLote extends Model
{
        /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_pivot_entprod_lotes';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['lote_id','prodent_id'];

    public static function entradasPorLote($id){
    return DB::table('lot_pivot_entprod_lotes')
            ->select('ent_pivot_prod_entradas.precio_unit As precio',
                    'ent_pivot_prod_entradas.impuesto_unit As impuesto',
                    'ent_pivot_prod_entradas.cantidad As cantidad')
            ->join('ent_pivot_prod_entradas', 'ent_pivot_prod_entradas.id', '=', 'lot_pivot_entprod_lotes.prodent_id')
            ->where('lot_pivot_entprod_lotes.lote_id', '=', $id)
            ->get();
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
