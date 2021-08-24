<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class LotLiquidacion extends Model
{
    protected $table = 'lot_liquidaciones';

    protected $fillable = ['costoPrecio','costoSacrificio','costoDesposte','costoTransporte','costoTransportePlantaPunto','costoEmpaque','ppe','pcc','pcr','prog_lotes_id'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
