<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Lote extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lotes';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['fecha_sacrificio','fecha_peso_pie','marinado','estado','num_animales','ProdGrupo_id','transportador_id','producto_empacado','pcc','ppe','marca','com_compras_id','lote_tercero', 'producto_aprobado', 'fecha_empaque_lote_tercero', 'consecutivo'];

    public function TerceroSucursal()
    {
        return $this->belongsTo('App\TerceroSucursal', 'transportador_id');
    }

    public function lotDecomiso()
    {
    	return $this->hasMany(LotDecomiso::class);
    }

    public function lotProgramacion()
    {
    	return $this->hasMany('App\LotProgramacion');
    }

    public function lotPesoPlanta()
    {
        return $this->hasMany('App\LotPesoPlanta');
    }

    public function LotPivotEntprodLote()
    {
        return $this->hasMany('App\LotPivotEntprodLote');
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function todosConMarca(){
    return DB::table('lotes')
            ->select('lotes.id As id','lotes.num_animales As num_animales','lotes.marca as marca','lotes.producto_empacado as producto_empacado','prod_grupos.nombre as grupo', 'prod_grupos.id as grupo_id', 'lotes.estado as estado')
            ->join('prod_grupos','lotes.ProdGrupo_id', '=', 'prod_grupos.id')
            ->orderBy('id','desc')
            ->get();
    }
}
