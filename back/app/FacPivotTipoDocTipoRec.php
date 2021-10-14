<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class FacPivotTipoDocTipoRec extends Model
{
    protected $table = 'fac_pivot_tipo_doc_tipo_rec';

    protected $fillable = [
    	'fac_tipo_doc_id',
    	'fac_tipo_rec_id'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
