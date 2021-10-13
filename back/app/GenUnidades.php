<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class GenUnidades extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gen_unidades';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','abrev_pos','activo','soenac_unid_api_id'];

    /**
     * GenUnidades has many Productos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productos()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = genUnidades_id, localKey = id)
    	return $this->hasMany(Producto::class);
    }

    protected $columns = array('id','abrev_pos','nombre','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array())
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
