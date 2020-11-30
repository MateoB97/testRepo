<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
        'habilitacion_fe'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
