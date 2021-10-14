<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class UserPermisosCategorias extends Model
{
    protected $table = 'user_permisos_categorias';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
