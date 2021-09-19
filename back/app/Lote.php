<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

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
    protected $fillable = ['fecha_sacrificio','fecha_peso_pie','marinado','estado','num_animales','ProdGrupo_id','transportador_id','producto_empacado','pcc','ppe','marca','com_compras_id','lote_tercero', 'producto_aprobado', 'fecha_empaque_lote_tercero', 'consecutivo', 'tercero_reprocesado', 'fecha_venc_refrigerado_granel', 'fecha_venc_congelado_granel', 'fecha_venc_refrigerado_vacio', 'fecha_venc_congelado_vacio'];

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
        return Tools::dateTimeSql();
    }

    // public static function todosConMarca(){
    // return DB::table('lotes')
    //         ->select('lotes.id As id',
    //         'lotes.num_animales As num_animales',
    //         'lotes.marca as marca',
    //         'lotes.producto_empacado as producto_empacado',
    //         'prod_grupos.nombre as grupo',
    //         'prod_grupos.id as grupo_id',
    //         'lotes.estado as estado',
    //         'lotes.consecutivo',
    //         'lotes.producto_empacado',
    //         )
    //         ->join('prod_grupos','lotes.ProdGrupo_id', '=', 'prod_grupos.id')
    //         ->orderBy('id','desc')
    //         ->take(5000)
    //         ->get();
    // }
    public static function todosConMarca() {
        return DB::select("
                select
                    lotes.id As id ,
                    lotes.num_animales As num_animales ,
                    lotes.marca as marca ,
                    lotes.producto_empacado as producto_empacado ,
                    prod_grupos.nombre as grupo ,
                    prod_grupos.id as grupo_id,
                    lotes.estado as estado,
                    lotes.consecutivo,
                    lotes.producto_empacado,
                    CASE WHEN lotes.producto_empacado = 0 THEN 'Lote JH'
                    WHEN lotes.producto_empacado = 1 THEN 'Lote Tercero'
                    WHEN lotes.producto_empacado = 2 THEN 'Lote Reprocesado'
                    END as tipo_lote
                from lotes
                inner join prod_grupos on lotes.prodGrupo_id = prod_grupos.id
                order by lotes.id desc
        ");
    }
}
