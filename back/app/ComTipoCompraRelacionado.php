<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComTipoCompraRelacionado extends Model
{
    protected $table = 'com_tipo_compra_relacionado';

    protected $fillable = ['com_tipo_compra_prim_id','com_tipo_compra_sec_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
