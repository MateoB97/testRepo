<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenImpuesto extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'gen_impuestos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','valor_impuesto','genTipoImpuesto_id','activo'];

    public function GenTipoImpuesto()
    {
        // belongsTo(RelatedModel, foreignKey = prodGrupo_id, keyOnRelatedModel = id)
        return $this->belongsTo(GenTipoImpuesto::class, 'genTipoImpuesto_id');
    }

    public function productos()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = genImpuesto_id, localKey = id)
    	return $this->hasMany(Producto::class);
    }

    protected $columns = array('id','nombre','activo','created_at','updated_at'); // add all columns from you table

    public function scopeExclude($query,$value = array()) 
    {
        return $query->select( array_diff( $this->columns,(array) $value) );
    }  

    public static function todosConTipos(){
    return DB::table('gen_impuestos')
            ->select('gen_impuestos.nombre As nombre','gen_impuestos.id As id','gen_tipo_impuestos.nombre as tipo','gen_impuestos.activo as activo')
            ->join('gen_tipo_impuestos', 'gen_impuestos.genTipoImpuesto_id', '=', 'gen_tipo_impuestos.id')
            ->get();
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
