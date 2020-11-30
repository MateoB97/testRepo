<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        return dateTimeSql();
    }
}
