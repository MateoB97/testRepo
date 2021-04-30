<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenCuadreCaja extends Model
{
    protected $table = 'gen_cuadre_caja';

    protected $fillable = ['monto_apertura','monto_cierre','estado', 'total_egresos','total_ingresos','usuario_id'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function todosConUsuario(){
    return DB::table('gen_cuadre_caja')
            ->select(
            	'gen_cuadre_caja.monto_apertura',
            	'gen_cuadre_caja.id as cuadre_id',
            	'gen_cuadre_caja.monto_cierre',
            	'users.name as usuario',
                'gen_cuadre_caja.created_at as fecha_apertura'
			)
            ->join('users', 'users.id', '=', 'gen_cuadre_caja.usuario_id')
            ->orderBy('gen_cuadre_caja.id', 'desc')
            ->get();
    }

    public static function porId($id){
    return DB::table('gen_cuadre_caja')
            ->select(
                'gen_cuadre_caja.monto_apertura',
                'gen_cuadre_caja.total_ingresos',
                'gen_cuadre_caja.id as id',
                'gen_cuadre_caja.monto_cierre',
                'gen_cuadre_caja.created_at',
                'gen_cuadre_caja.updated_at',
                'gen_cuadre_caja.total_egresos',
                'gen_cuadre_caja.usuario_id',
                'gen_cuadre_caja.estado'
            )
            ->where('gen_cuadre_caja.id', $id)
            ->get()
            ->first();
    }
}
