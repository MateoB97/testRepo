<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class TerceroSucursal extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'tercero_sucursales';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','direccion','telefono','tercero_id','prodListaPrecio_id','activo','gen_municipios_id','email'];

    /**
     * TerceroSucursal belongs to Tercero.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function tercero()
    {
    	// belongsTo(RelatedModel, foreignKey = tercero_id, keyOnRelatedModel = id)
    	return $this->belongsTo('App\Tercero', 'tercero_id');
    }

    /**
     * Tercero belongs to ProdListaPrecio.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodListaPrecio()
    {
        // belongsTo(RelatedModel, foreignKey = prodListaPrecio_id, keyOnRelatedModel = id)
        return $this->belongsTo('App\ProdListaPrecio', 'prodListaPrecio_id');
    }

    /**
     * TerceroSucursal has many SalMercancia.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function salMercancia()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = terceroSucursal_id, localKey = id)
        return $this->hasMany('App\SalMercancia');
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function getDataSucursalNoId(){
        return DB::table('tercero_sucursales')
        ->select(DB::raw('terceros.id as tercero_id,
                terceros.nombre as tercero_nombre,
                tercero_sucursales.id as sucursal_id,
                tercero_sucursales.nombre as sucursal_nombre,
                tercero_sucursales.direccion as sucursal_direccion,
                tercero_sucursales.telefono as sucursal_telefono,
                tercero_sucursales.prodListaPrecio_id as prodListaPrecio_id,
                gen_municipios.id as municipio_id,
                gen_municipios.nombre as municipio_nombre,
                gen_departamentos.id as departamento_id,
                gen_departamentos.nombre as departamento_nombre'))
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('gen_municipios','gen_municipios.id', '=', 'tercero_sucursales.gen_municipios_id')
            ->join('gen_departamentos','gen_departamentos.id', '=', 'gen_municipios.departamento_id')
            ->whereIn('terceros.id', [4,5])
            ->orderBy('terceros.id')
            //->whereIn('id', [1, 2, 3])
            ->get();
    }

    public static function getDataSucursal($id){
        return DB::table('tercero_sucursales')
        ->select(DB::raw('terceros.id as tercero_id,
                terceros.nombre as tercero_nombre,
                tercero_sucursales.id as sucursal_id,
                tercero_sucursales.nombre as sucursal_nombre,
                tercero_sucursales.direccion as sucursal_direccion,
                tercero_sucursales.telefono as sucursal_telefono,
                tercero_sucursales.prodListaPrecio_id as prodListaPrecio_id,
                gen_municipios.id as municipio_id,
                gen_municipios.nombre as municipio_nombre,
                gen_departamentos.id as departamento_id,
                gen_departamentos.nombre as departamento_nombre'))
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('gen_municipios','gen_municipios.id', '=', 'tercero_sucursales.gen_municipios_id')
            ->join('gen_departamentos','gen_departamentos.id', '=', 'gen_municipios.departamento_id')
            ->where('tercero_sucursales.id', '=', $id)
            ->get();
    }
}
