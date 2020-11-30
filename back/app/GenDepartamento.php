<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GenDepartamento extends Model
{
    protected $table = 'gen_departamentos';

    protected $fillable = ['nombre'];

    public function municipios()
    {
    	return $this->hasMany('App\GenMunicipios');
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
