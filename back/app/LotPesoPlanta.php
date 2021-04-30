<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LotPesoPlanta extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_peso_plantas';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['peso','producto_id','lote_id','temperatura','color','olor','observaciones','cumple','sin_sustancias','ph'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function pesosTotalesLote () {
        return DB::table('lot_peso_plantas')
        ->select( DB::raw( 'sum(peso) as pesototal
                          ,lot_peso_plantas.lote_id as lote
                          ,lotes.marca as marca
                          ,lotes.num_animales as num_animales
                          ,prod_grupos.nombre as grupo
                          ,lot_programaciones.id as programacion
                          ,com_compras.fecha_compra as fecha_entrada'))
        ->join('lotes', 'lotes.id', '=', 'lot_peso_plantas.lote_id')
        ->join('lot_programaciones', 'lot_programaciones.lote_id', '=', 'lotes.id')
        ->join('com_compras', 'com_compras.id', '=', 'lotes.com_compras_id')
        ->join('prod_grupos', 'prod_grupos.id', '=', 'lotes.ProdGrupo_id')
        ->orderBy('lot_peso_plantas.lote_id','desc')
        ->groupby('lot_peso_plantas.lote_id')
        ->groupby('lotes.marca')
        ->groupby('lot_programaciones.id')
        ->groupby('com_compras.fecha_compra')
        ->groupby('prod_grupos.nombre')
        ->groupby('lotes.num_animales')
        ->get();
    }
}
