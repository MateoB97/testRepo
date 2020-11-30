<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LotEtiquetaInterna extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_etiqueta_interna';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['prog_lotes_id','producto_id','reimpresion'];

    public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }
}
