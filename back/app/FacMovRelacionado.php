<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FacMovRelacionado extends Model
{
    protected $table = 'fac_mov_relacionados';

    protected $fillable = ['fac_tipo_doc_prim_id','fac_tipo_doc_sec_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
