<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class ProdPivotListaProducto extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'prod_pivot_lista_productos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['precio','producto_id','prodListaPrecio_id'];


    /**
     * ProdPivotListaProducto belongs to ProdListaPrecio.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function prodListaPrecio()
    {
    	// belongsTo(RelatedModel, foreignKey = prodListaPrecio_id, keyOnRelatedModel = id)
    	return $this->belongsTo(ProdListaPrecio::class, 'prodListaPrecio_id');
    }

    /**
     * ProdPivotListaProducto belongs to Producto.
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
