<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacDataFE extends Model
{
    protected $connection = 'datosfe';

    protected $table = 'datosfe';

    protected $fillable = [
    	'fac_mov_id',
    	'cufe',
    	'zip_key',
    	'zip_name',
    	'url_acceptance',
    	'url_rejection',
    	'pdf_base64_bytes',
    	'dian_response_base64_bytes',
    	'application_response_base64_bytes'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
