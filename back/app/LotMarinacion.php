<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Tools;

class LotMarinacion extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_marinaciones';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['cantidad','producto_id','lotProgramacion_id'];

    /**
     * LotMarinacion belongs to Producto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
    	// belongsTo(RelatedModel, foreignKey = producto_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Producto::class);
    }

    /**
     * LotMarinacion belongs to LotProgramacion.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lotProgramacion()
    {
    	// belongsTo(RelatedModel, foreignKey = lotProgramacion_id, keyOnRelatedModel = id)
    	return $this->belongsTo(LotProgramacion::class);
    }

    public static function productosPorProgramacion($programacionId){
    return DB::table('lot_marinaciones')
            ->select('lot_marinaciones.id As id',
                    'productos.nombre As producto',
                    'productos.id As producto_id',
                    'productos.codigo As producto_codigo',
                    'lot_marinaciones.cantidad As cantidad')
            ->join('productos', 'productos.id', '=', 'lot_marinaciones.producto_id')
            ->where('lot_marinaciones.lotProgramacion_id', '=', $programacionId)
            ->get();
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }
}
