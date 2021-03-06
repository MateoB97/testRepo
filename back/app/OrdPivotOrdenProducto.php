<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdPivotOrdenProducto extends Model
{
    protected $table = 'ord_pivot_orden_producto';

    protected $fillable = ['descporcentaje','iva','precio','cantidad','producto_id','ord_orden_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
