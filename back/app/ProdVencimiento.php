<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class ProdVencimiento extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prod_vencimientos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['dias_vencimiento','producto_id','prodAlmacenamiento_id'];

    /**
     * ProdVencimiento belongs to ProdAlmacenamiento.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodAlmacenamiento()
    {
    	// belongsTo(RelatedModel, foreignKey = prodAlmacenamiento_id, keyOnRelatedModel = id)
    	return $this->belongsTo(ProdAlmacenamiento::class, 'prodAlmacenamiento_id');
    }

    /**
     * ProdVencimiento belongs to Producto.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function producto()
    {
    	// belongsTo(RelatedModel, foreignKey = producto_id, keyOnRelatedModel = id)
    	return $this->belongsTo(Producto::class);
    }

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }


}
