<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FacTipoRecCaja extends Model
{
    protected $table = 'fac_tipo_rec_caja';

    protected $fillable = ['fac_tipo_doc_id','consec_inicio','nombre'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

}
