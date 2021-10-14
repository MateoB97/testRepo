<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class UserPermisos extends Model
{
    protected $table = 'user_permisos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','permisos_categoria_id','consecutivo'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function indexConCategorias(){
    return DB::table('user_permisos')
            ->select(
                'user_permisos.id As id',
                'user_permisos.nombre As nombre',
                'user_permisos.consecutivo As consecutivo',
                'user_permisos_categorias.nombre As categoria',
                'user_permisos.permisos_categoria_id as permisos_categoria_id')
            ->join('user_permisos_categorias', 'user_permisos.permisos_categoria_id', '=', 'user_permisos_categorias.id')
            ->get();
    }

    public static function permisosPorRol($permisos){
    return DB::table('user_permisos')
            ->select(
                'user_permisos.id As id',
                'user_permisos.nombre As nombre',
                'user_permisos.consecutivo As consecutivo',
                'user_permisos_categorias.nombre As categoria')
            ->whereIn('user_permisos.id', $permisos)
            ->get();
    }
}
