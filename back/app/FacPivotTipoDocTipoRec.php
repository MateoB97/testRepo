<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacPivotTipoDocTipoRec extends Model
{
    protected $table = 'fac_pivot_tipo_doc_tipo_rec';

    protected $fillable = [
    	'fac_tipo_doc_id',
    	'fac_tipo_rec_id'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
