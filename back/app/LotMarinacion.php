<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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

    public function getDateFormat()
    {
        return 'Y-d-m H:i:s.v';
    }
}
