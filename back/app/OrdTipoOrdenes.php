<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class OrdTipoOrdenes extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'ord_tipo_ordenes';

    protected $fillable = ['nombre','fac_tipo_doc_id','com_tipo_compra_id', 'consec_inicio'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

}
