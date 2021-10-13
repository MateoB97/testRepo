<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class OrdTipoOrden extends Model
{
    protected $table = 'ord_tipo_ordenes';

    protected $fillable = ['nombre','consec_inicio','fac_tipo_doc_id','com_tipo_compra_id'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
