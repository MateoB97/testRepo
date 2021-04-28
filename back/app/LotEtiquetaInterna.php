<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LotEtiquetaInterna extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_etiqueta_interna';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['prog_lotes_id','producto_id','reimpresion','cantidad'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function getEtiquetasImpresas($producto_id, $prog_lotes_id){
        return DB::select(
            "
            select
                sum(lot_etiqueta_interna.cantidad) as cantidad
            from lot_etiqueta_interna
            where producto_id = '$producto_id'
                and prog_lotes_id = '$prog_lotes_id'
            "
        );
    }
}
