<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tercero extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'terceros';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['documento','nombre','proveedor','cliente','empleado','activo','habilitado_traslados','digito_verificacion','soenac_regim_id', 'soenac_responsab_id','soenac_tipo_org_id','soenac_tipo_documento_id','registro_mercantil', 'plazo_facturacion'];

    /**
     * Tercero has many TerceroSucursal.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function terceroSucursal()
    {
    	// hasMany(RelatedModel, foreignKeyOnRelatedModel = tercero_id, localKey = id)
    	return $this->hasMany('App\TerceroSucursal');
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function validarFacturasTerceros($tercero_id){
        return
        DB::select("
        select
            fac_movimientos.consecutivo
        from fac_movimientos
        inner join tercero_sucursales on tercero_sucursales.id = fac_movimientos.cliente_id
        inner join terceros on terceros.id = tercero_sucursales.tercero_id
        where terceros.id = '$tercero_id'
        AND estado = 1
        AND fecha_vencimiento <= CAST(GETDATE() as date)");
    }

}
