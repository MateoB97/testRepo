<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class ProdSubgrupo extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prod_subgrupos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','prodGrupo_id','activo', 'encabezado_etiqueta'];

    /**
     * ProdSubgrupo belongs to ProdGrupo.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodGrupo()
    {
    	// belongsTo(RelatedModel, foreignKey = prodGrupo_id, keyOnRelatedModel = id)
    	return $this->belongsTo(ProdGrupo::class, 'prodGrupo_id');
    }

    /**
     * ProdSubgrupo has many Productos.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function productos()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = prodSubgrupo_id, localKey = id)
    	return $this->hasMany('App\Producto');
    }

    protected $columns = array('id','nombre','prodGrupo_id','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array())
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }


    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function todosConGrupos(){
    return DB::table('prod_subgrupos')
            ->select('prod_subgrupos.nombre As nombre','prod_subgrupos.id As id','prod_grupos.nombre as grupo', 'prod_subgrupos.activo as activo', 'prod_subgrupos.encabezado_etiqueta')
            ->join('prod_grupos', 'prod_subgrupos.prodGrupo_id', '=', 'prod_grupos.id')
            ->get();
    }
}
