<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TransformacionProducto extends Model
{
    protected $table = 'transformacion_producto';

    protected $fillable = ['producto_id_out','cantidad_out','producto_id_in', 'cantidad_in'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
