<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvCierreInventario extends Model
{
    protected $table = 'inv_cierre_inventario';

    protected $fillable = [
    	'fecha_cierre',
    	'total_diferencia_kilos',
    	'total_diferencia_dinero'
    ];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
