<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacTipoDoc extends Model
{
    protected $table = 'fac_tipo_doc';

    protected $fillable = [
    	'nombre',
    	'nombre_alt',
    	'naturaleza',
    	'ini_num_fac',
    	'fin_num_fac',
    	'resolucion',
    	'fec_resolucion',
    	'prefijo',
    	'nota',
    	'formato_impresion',
    	'consec_inicio',
        'traslado',
        'soenac_tipo_doc_api_id',
        'resolucion_soenac_id',
        'habilitacion_fe',
        'fac_tipo_estado',
        'fac_tipo_oficial'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function facTipoDocPorEstado($estado){
        return DB::table('fac_tipo_doc')
                ->select(
                    'id',
                    'nombre',
                    'nombre_alt',
                    'naturaleza',
                    'ini_num_fac',
                    'fin_num_fac',
                    'resolucion',
                    'fec_resolucion',
                    'prefijo',
                    'nota',
                    'formato_impresion',
                    'traslado',
                    'consec_inicio',
                    'created_at',
                    'updated_at',
                    'soenac_tipo_doc_api_id',
                    'resolucion_soenac_id',
                    'habilitacion_fe',
                    'fac_tipo_estado',
                    'fac_tipo_oficial'
                    )
                ->where('fac_tipo_doc.fac_tipo_estado','=', $estado)
                ->get();
        }

}
