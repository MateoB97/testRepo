<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class LotPesosProgramacion extends Model
{
    protected $table = 'lot_prog_pesos_animales';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['num_animal','pcc','pcr','ppe','lotProgramacion_id'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
