<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProdAlmacenamiento extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prod_almacenamientos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','activo'];

    /**
     * ProdAlmacenamiento has many ProdVencimiento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prodVencimiento()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = prodAlmacenamiento_id, localKey = id)
    	return $this->hasMany(ProdVencimiento::class);
    }

    protected $columns = array('id','nombre','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array()) 
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }

    public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }
}
