<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class ProdListaPrecio extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prod_lista_precios';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','activo'];

    /**
     * ProdListaPrecio has many Terceros.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function terceros()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = prodListaPrecio_id, localKey = id)
    	return $this->hasMany(Tercero::class);
    }

    /**
     * ProdListaPrecio has many ProdPivotListaProducto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prodPivotListaProducto()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = prodListaPrecio_id, localKey = id)
        return $this->hasMany(ProdPivotListaProducto::class);
    }

    protected $columns = array('id','nombre','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array())
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }


    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

}
