<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductoTerminado extends Model
{
    protected $table = 'producto_terminados';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['prog_lotes_id','invent_id','almacenamiento','dias_vencimiento','num_piezas', 'marinado', 'fecha_vencimiento'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
