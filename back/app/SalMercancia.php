<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalMercancia extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'sal_mercancias';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['terceroSucursal_id','temperatura','vehiculo', 'consecutivo'];

    /**
     * SalMercancia belongs to TerceroSucursal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function terceroSucursal()
    {
    	// belongsTo(RelatedModel, foreignKey = terceroSucursal_id, keyOnRelatedModel = id)
    	return $this->belongsTo('App\TerceroSucursal', 'terceroSucursal_id');
    }

    /**
     * SalMercancia has many SalPivotInventSalida.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salPivotInventSalida()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = salMercancia_id, localKey = id)
        return $this->hasMany('App\SalPivotInventSalida');
    }

    public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }

    public static function todosConSucursales(){
    return DB::table('sal_mercancias')
            ->select(   'sal_mercancias.id As id',
                        'sal_mercancias.consecutivo As consecutivo',
                        'terceros.nombre as tercero',
                        'tercero_sucursales.id as tercero_sucursal_id',
                        'tercero_sucursales.nombre as sucursal',
                        'sal_mercancias.created_at as fecha')
            ->join('tercero_sucursales', 'tercero_sucursales.id', '=', 'sal_mercancias.terceroSucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->orderby('id','desc')
            ->get();
    }

    public static function despachosSinFactura(){
    return DB::table('sal_mercancias')
            ->select(   'sal_mercancias.id As id',
                        'terceros.nombre as tercero',
                        'tercero_sucursales.id as tercero_sucursal_id',
                        'tercero_sucursales.nombre as sucursal',
                        'sal_mercancias.created_at as fecha')
            ->join('tercero_sucursales', 'tercero_sucursales.id', '=', 'sal_mercancias.terceroSucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->whereRaw('sal_mercancias.id NOT IN (select [fac_pivot_mov_salmercancia].sal_mercancias_id from fac_pivot_mov_salmercancia)')
            ->orderby('id','desc')
            ->get();
    }

    public static function despachoSinFacturaParaTraslados(){
    return DB::table('sal_mercancias')
            ->select(   'sal_mercancias.id As id',
                        'terceros.nombre as tercero',
                        'tercero_sucursales.id as tercero_sucursal_id',
                        'tercero_sucursales.nombre as sucursal',
                        'sal_mercancias.created_at as fecha')
            ->join('tercero_sucursales', 'tercero_sucursales.id', '=', 'sal_mercancias.terceroSucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->whereRaw('sal_mercancias.id NOT IN (select [fac_pivot_mov_salmercancia].sal_mercancias_id from fac_pivot_mov_salmercancia)')
            ->where('terceros.habilitado_traslados', true)
            ->orderby('id','desc')
            ->get();
    }

    public static function DateFilter($fecha_inicial,$fecha_final){
    return DB::table('sal_mercancias')
            ->select(   'sal_mercancias.id As id',
                        'terceros.nombre as tercero',
                        'tercero_sucursales.direccion as sucursal',
                        'sal_mercancias.created_at as fecha')
            ->join('tercero_sucursales', 'tercero_sucursales.id', '=', 'sal_mercancias.terceroSucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->whereBetween('sal_mercancias.created_at', [$fecha_inicial, $fecha_final])
            ->get();
    }

    public static function DateSucursalFilter($fecha_inicial,$fecha_final,$sucursal){
    return DB::table('sal_mercancias')
            ->select(   'sal_mercancias.id As id',
                        'terceros.nombre as tercero',
                        'tercero_sucursales.direccion as sucursal',
                        'sal_mercancias.created_at as fecha')
            ->join('tercero_sucursales', 'tercero_sucursales.id', '=', 'sal_mercancias.terceroSucursal_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->whereBetween('sal_mercancias.created_at', [$fecha_inicial, $fecha_final])
            ->where('sal_mercancias.terceroSucursal_id', '=', $sucursal)
            ->get();
    }

    public static function findByCosec($consec){
        return DB::table('sal_mercancias')
        ->select(   'sal_mercancias.id As id',
                    'sal_mercancias.temperatura',
                    'sal_mercancias.vehiculo',
                    'sal_mercancias.terceroSucursal_id',
                    'sal_mercancias.consecutivo',
                    'sal_mercancias.created_at',
                    'sal_mercancias.updated_at')
        ->where('sal_mercancias.consecutivo', '=', $consec)
        ->get();
    }
}
