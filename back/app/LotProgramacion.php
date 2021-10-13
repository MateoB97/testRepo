<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class LotProgramacion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_programaciones';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['num_animales','lote_id','fecha_desposte','producto_canal','terceroSucursal_id','estado'];

    /**
     * LotProgramacion belongs to Lote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lote()
    {
    	// belongsTo(RelatedModel, foreignKey = lote_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Lote::class, 'lote_id');
    }

    public function TerceroSucursal()
    {
        return $this->belongsTo('App\TerceroSucursal', 'terceroSucursal_id');
    }

    /**
     * LotProgramacion has many Inventario.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function inventario()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = lotProgramacion_id, localKey = id)
    	return $this->hasMany(Inventario::class);
    }

    public static function todosConLoteAbierto($producto_empacado){
    return DB::table('lot_programaciones')
            ->select(
                'lot_programaciones.fecha_desposte As fecha_desposte',
                'lot_programaciones.id As programacion_id',
                'lot_programaciones.num_animales as num_animales_programacion',
                'lotes.id as lote_id',
                'lotes.marca as marca',
                'lotes.prodGrupo_id as grupo_id',
                'lotes.consecutivo as consecutivo',
                'prod_grupos.nombre as grupo',
                'terceros.nombre as tercero',
                'tercero_sucursales.id as tercero_sucursal_id',
                'tercero_sucursales.nombre as sucursal',
                'lotes.lote_tercero as lote_tercero'
            )
            ->leftJoin('tercero_sucursales', 'tercero_sucursales.id', '=', 'lot_programaciones.terceroSucursal_id')
            ->leftJoin('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('lotes', 'lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('prod_grupos', 'lotes.prodGrupo_id', '=', 'prod_grupos.id')
            ->where('lotes.estado','=', 1)
            ->where('lot_programaciones.estado','!=', 2)
            ->whereIn('lotes.producto_empacado', $producto_empacado)
            // ->where('lotes.producto_empacado','=', $producto_empacado)
            // ->when($producto_empacado, function ($query, $producto_empacado) {
            //     return $query->where('lotes.producto_empacado', '=', $producto_empacado);
            // })
            ->orderBy('programacion_id','desc')
            ->limit(200)
            ->get();
    }

    public static function todosConLoteAbiertoPorGrupo($grupoId, $producto_empacado){
    return DB::table('lot_programaciones')
            ->select(
                'lot_programaciones.fecha_desposte As fecha_desposte',
                'lot_programaciones.id As programacion_id',
                'lot_programaciones.num_animales as num_animales_programacion',
                'lotes.id as lote_id',
                'lotes.marca as marca',
                'lotes.prodGrupo_id as grupo_id',
                'prod_grupos.nombre as grupo',
                'terceros.nombre as tercero',
                'tercero_sucursales.id as tercero_sucursal_id',
                'tercero_sucursales.nombre as sucursal',
                'lotes.lote_tercero as lote_tercero'
            )
            ->join('lotes', 'lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('prod_grupos', 'lotes.prodGrupo_id', '=', 'prod_grupos.id')
            ->leftJoin('tercero_sucursales', 'tercero_sucursales.id', '=', 'lot_programaciones.terceroSucursal_id')
            ->leftJoin('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->where('lotes.estado','=', 1)
            ->where('lot_programaciones.estado','!=', 2)
            ->where('lotes.prodGrupo_id', $grupoId)
            ->where('lotes.producto_empacado', $producto_empacado)
            ->orderBy('programacion_id','desc')
            ->limit(200)
            ->get();
    }

    public static function conLoteAbiertoPorId($loteId, $producto_empacado){
    return DB::table('lot_programaciones')
            ->select(
                'lot_programaciones.fecha_desposte As fecha_desposte',
                'lot_programaciones.id As programacion_id',
                'lot_programaciones.num_animales as num_animales_programacion',
                'lotes.id as lote_id',
                'lotes.marca as marca',
                'lotes.prodGrupo_id as grupo_id',
                'lotes.consecutivo as consecutivo',
                'prod_grupos.nombre as grupo',
                'terceros.nombre as tercero',
                'tercero_sucursales.id as tercero_sucursal_id',
                'tercero_sucursales.nombre as sucursal',
                'lot_programaciones.estado as estado',
                'lotes.lote_tercero as lote_tercero'
            )
            ->join('lotes', 'lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('prod_grupos', 'lotes.prodGrupo_id', '=', 'prod_grupos.id')
            ->leftJoin('tercero_sucursales', 'tercero_sucursales.id', '=', 'lot_programaciones.terceroSucursal_id')
            ->leftJoin('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->where('lotes.estado','=', 1)
            // ->where('lotes.id', $loteId)
            ->where('lotes.consecutivo', $loteId)
            ->where('lotes.producto_empacado', $producto_empacado)
            ->orderBy('programacion_id','desc')
            ->get();
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
