<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProdGrupo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prod_grupos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','activo','registro_sanitario'];

    /**
     * ProdGrupo has many ProdSubgrupos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function prodSubgrupos()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = prodGrupo_id, localKey = id)
    	return $this->hasMany(ProdSubgrupo::class);
    }

    protected $columns = array('id','nombre','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array()) 
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }  

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function listadoGruposMarques(){
        return DB::table('prod_grupos')
            ->select('prod_grupos.id as codigo','prod_grupos.nombre as descricao')
            ->orderBy('prod_grupos.id','asc')
            ->get();
    }
}
