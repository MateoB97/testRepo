<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class LotPivotLiquiProducto extends Model
{
    protected $table = 'lot_pivot_liquid_productos';

    protected $fillable = ['precio_venta','cantidad','vacio','tipo_producto','lot_liquidaciones_id','producto_id'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
