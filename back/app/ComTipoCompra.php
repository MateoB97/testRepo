<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComTipoCompra extends Model
{
    protected $table = 'com_tipo_compras';

    protected $fillable = ['nombre','nombre_alt','consec_inicio','naturaleza','formato_impresion'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
