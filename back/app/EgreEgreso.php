<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class EgreEgreso extends Model
{
    protected $table = 'egre_egresos';

    protected $fillable = ['descripcion','valor','forma_pago','gen_cuadre_caja_id','proveedor_id','egre_tipo_egreso_id','consecutivo'];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public static function todosConSucursalUsuarioTercero(){
    return DB::table('egre_egresos')
            ->select(
            	'egre_egresos.id As id',
            	'egre_egresos.valor as valor',
            	'egre_egresos.forma_pago as forma_pago',
            	'egre_tipo_egreso.nombre as tipo_egreso',
            	'tercero_sucursales.nombre as sucursal',
            	'terceros.nombre as tercero',
            	'users.name as usuario',
                'consecutivo'
            )
            ->join('egre_tipo_egreso','egre_tipo_egreso.id', '=', 'egre_egresos.egre_tipo_egreso_id')
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'egre_egresos.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('gen_cuadre_caja','gen_cuadre_caja.id', '=', 'egre_egresos.gen_cuadre_caja_id')
            ->join('users','users.id', '=', 'gen_cuadre_caja.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }

    public static function sumEfectivoCuadre($cuadre){
    return DB::table('egre_egresos')
            ->select( DB::raw( 'sum(egre_egresos.valor) as valor'))
            ->where('egre_egresos.forma_pago', 'Efectivo')
            ->where('egre_egresos.gen_cuadre_caja_id', $cuadre)
            ->get();
    }

    public static function PorFechaConSucursalUsuarioTercero(){
    return DB::table('egre_egresos')
            ->select(
                'egre_egresos.id As id',
                'egre_egresos.valor as valor',
                'egre_egresos.forma_pago as forma_pago',
                'egre_tipo_egreso.nombre as tipo_egreso',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'users.name as usuario'
            )
            ->join('egre_tipo_egreso','egre_tipo_egreso.id', '=', 'egre_egresos.egre_tipo_egreso_id')
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'egre_egresos.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('gen_cuadre_caja','gen_cuadre_caja.id', '=', 'egre_egresos.gen_cuadre_caja_id')
            ->join('users','users.id', '=', 'gen_cuadre_caja.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }

    public static function porId($id){
    return DB::table('egre_egresos')
            ->select(
                '*'
            )
            ->where('egre_egresos.id', $id)
            ->get()
            ->first();
    }
}
