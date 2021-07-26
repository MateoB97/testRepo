<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalPivotInventSalida extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sal_pivot_invent_salidas';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['inventario_id','salMercancia_id','precio'];

    /**
     * SalPivotInventSalida belongs to Inventario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function inventario()
    {
    	// belongsTo(RelatedModel, foreignKey = inventario_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Inventario::class);
    }

    /**
     * SalPivotInventSalida belongs to SalMercancia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function salMercancia()
    {
    	// belongsTo(RelatedModel, foreignKey = salMercancia_id, keyOnRelatedModel = id)
    	return $this->belongsTo(SalMercancia::class);
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function GetDataCertificado($id){
    return DB::table('sal_pivot_invent_salidas')
            ->select(
                'productos.nombre As producto',
                'productos.id as producto_id',
                'inventarios.cantidad as peso',
                'inventarios.id as inventario_id',
                'lotes.id As lote',
                'producto_terminados.created_at as fecha_empaque',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'prod_grupos.nombre as grupo',
                'producto_terminados.almacenamiento as almacenamiento',
                'producto_terminados.dias_vencimiento as vencimiento',
                'sal_mercancias.created_at as fecha_sal_mercancia',
                'sal_mercancias.consecutivo as consecutivo',
                'producto_terminados.fecha_vencimiento as prod_term_fecha_vencimiento',
                'lotes.producto_empacado',
                'lotes.fecha_empaque_lote_tercero',
                'lotes.consecutivo As consecutivo',
            )
            ->join('inventarios', 'inventarios.id', '=', 'sal_pivot_invent_salidas.inventario_id')
            ->join('sal_mercancias', 'sal_mercancias.id', '=', 'sal_pivot_invent_salidas.salMercancia_id')
            ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
            ->join('prod_subgrupos', 'productos.prod_subgrupo_id', '=', 'prod_subgrupos.id')
            ->join('prod_grupos', 'prod_subgrupos.prodGrupo_id', '=', 'prod_grupos.id')
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones', 'producto_terminados.prog_lotes_id', '=', 'lot_programaciones.id')
            ->join('lotes', 'lot_programaciones.lote_id', '=', 'lotes.id')
            ->where('sal_pivot_invent_salidas.salMercancia_id','=', $id)
            ->orderBy('productos.nombre')
            ->get();
    }

    public static function GetDataCertificadoByConsec($consec){
        return DB::table('sal_pivot_invent_salidas')
                ->select(
                    'productos.id as producto_id',
                    'productos.codigo As codigo',
                    'inventarios.cantidad as peso',
                    'prod_pivot_lista_productos.precio',
                    'sal_mercancias.consecutivo'
                )
                ->join('inventarios', 'inventarios.id', '=', 'sal_pivot_invent_salidas.inventario_id')
                ->join('sal_mercancias', 'sal_mercancias.id', '=', 'sal_pivot_invent_salidas.salMercancia_id')
                ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
                ->join('prod_pivot_lista_productos', 'prod_pivot_lista_productos.producto_id', '=', 'productos.id')
                ->join('prod_subgrupos', 'productos.prod_subgrupo_id', '=', 'prod_subgrupos.id')
                ->join('prod_grupos', 'prod_subgrupos.prodGrupo_id', '=', 'prod_grupos.id')
                ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
                ->join('lot_programaciones', 'producto_terminados.prog_lotes_id', '=', 'lot_programaciones.id')
                ->join('lotes', 'lot_programaciones.lote_id', '=', 'lotes.id')
                ->where('sal_mercancias.consecutivo','=', $consec)
                ->orderBy('productos.nombre')
                ->get();
        }

    public static function despachoParaPesoDespacho($id){
    return DB::table('sal_pivot_invent_salidas')
            ->select(
                'productos.id as producto_id',
                'productos.codigo As codigo',
                'inventarios.cantidad as peso',
            )
            ->join('inventarios', 'inventarios.id', '=', 'sal_pivot_invent_salidas.inventario_id')
            ->join('sal_mercancias', 'sal_mercancias.id', '=', 'sal_pivot_invent_salidas.salMercancia_id')
            ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
            ->where('sal_pivot_invent_salidas.salMercancia_id','=', $id)
            ->get();
    }

}
