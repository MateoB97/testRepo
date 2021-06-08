<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvPivotCierreProductos extends Model
{
    protected $table = 'inv_pivot_cierre_productos';

    protected $fillable = [
    	'inv_cierre_id',
    	'producto_id',
    	'precio_al_cierre',
    	'cantidad_stock',
    	'cantidad_cierre'
    ];

    public $timestamps = false;

}
