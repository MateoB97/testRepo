<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GenCuadreIngresoEfectivo extends Model
{
    protected $table = 'gen_cuadre_ingreso_efectivo';

    protected $fillable = ['descripcion','valor','consecutivo','gen_cuadre_caja_id'];  

    public function getDateFormat()
    {
       return dateTimeSql();
    }

    public static function sumIngresoEfectivoCuadre($cuadre){
    return DB::table('gen_cuadre_ingreso_efectivo')
            ->select( DB::raw( 'sum(gen_cuadre_ingreso_efectivo.valor) as valor'))
            ->where('gen_cuadre_ingreso_efectivo.gen_cuadre_caja_id', $cuadre)
            ->get();
    }

    public static function todosConUsuario(){
    return DB::table('gen_cuadre_ingreso_efectivo')
            ->select(
            	'gen_cuadre_ingreso_efectivo.id As id',
            	'gen_cuadre_ingreso_efectivo.valor as valor',
            	'gen_cuadre_ingreso_efectivo.descripcion as descripcion',
            	'users.name as usuario',
                'consecutivo'
            )
            ->join('gen_cuadre_caja','gen_cuadre_caja.id', '=', 'gen_cuadre_ingreso_efectivo.gen_cuadre_caja_id')
            ->join('users','users.id', '=', 'gen_cuadre_caja.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }

    public static function porId($id){
    return DB::table('gen_cuadre_ingreso_efectivo')
            ->select(
                '*'
            )
            ->where('gen_cuadre_ingreso_efectivo.id', $id)
            ->get()
            ->first();
    }

}
