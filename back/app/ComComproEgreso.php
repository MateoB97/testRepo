<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ComComproEgreso extends Model
{
    protected $table = 'com_compro_egresos';

    protected $fillable = ['com_tipo_compro_egresos_id','proveedor_id','usuario_id','abono','ajuste','total','ajuste_observacion','consecutivo','fecha_comprobante'];

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function todosConTipoSucursal(){
    return DB::table('com_compro_egresos')
            ->select(
                'com_compro_egresos.id As id',
                'com_compro_egresos.consecutivo As consecutivo',
                'com_compro_egresos.total as total',
                'com_compro_egresos.fecha_comprobante as fecha_comprobante',
                'tercero_sucursales.nombre as sucursal',
                'terceros.nombre as tercero',
                'com_tipo_compro_egresos.nombre as tipo',
                'users.name as usuario'
            )
            ->join('tercero_sucursales','tercero_sucursales.id', '=', 'com_compro_egresos.proveedor_id')
            ->join('terceros','terceros.id', '=', 'tercero_sucursales.tercero_id')
            ->join('com_tipo_compro_egresos','com_tipo_compro_egresos.id', '=', 'com_compro_egresos.com_tipo_compro_egresos_id')
            ->join('users','users.id', '=', 'com_compro_egresos.usuario_id')
            ->orderBy('id','desc')
            ->get();
    }

    
}
