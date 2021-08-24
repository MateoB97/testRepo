<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class GenMunicipio extends Model
{
    protected $table = 'gen_municipios';

    protected $fillable = ['nombre', 'departamento_id'];

    public function departamento()
    {
    	return $this->belongsTo('App\GenDepartamento', 'departamento_id');
    }

    public function inventario()
    {
    	return $this->hasMany('App\Inventario');
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

}
