<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrdPivotFormasPago extends Model
{
    protected $table = 'ord_pivot_formaspago';

    protected $fillable = ['valor_abonado','ord_orden_id','ord_formas_pago_id'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
