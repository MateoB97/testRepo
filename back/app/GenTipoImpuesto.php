<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenTipoImpuesto extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gen_tipo_impuestos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','activo'];

    /**
     * ProdGrupo has many ProdSubgrupos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */

    protected $columns = array('id','nombre','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array()) 
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }  

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
