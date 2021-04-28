<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LotDecomiso extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'lot_decomisos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['cantidad','producto_id','lote_id'];

    /**
     * LotDecomiso belongs to Producto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
    	// belongsTo(RelatedModel, foreignKey = producto_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Producto::class);
    }

    /**
     * LotDecomiso belongs to Lote.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function lote()
    {
    	// belongsTo(RelatedModel, foreignKey = lote_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Lote::class);
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }
}
