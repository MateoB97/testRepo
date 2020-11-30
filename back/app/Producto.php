<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Producto extends Model
{
    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'productos';

    /**
     * Fields that can be mass assigned.
     *
     * @var array
     */
    protected $fillable = ['nombre','codigo', 'prod_subgrupo_id','gen_iva_id','gen_unidades_id','unid_por_animal','activo','cod_ean_13'];


    public function prodSubgrupo()
    {
    	// belongsTo(RelatedModel, foreignKey = prodSubgrupo_id, keyOnRelatedModel = id)
    	return $this->belongsTo('App\ProdSubgrupo', 'prod_subgrupo_id');
    }

    public function genIva()
    {
        // belongsTo(RelatedModel, foreignKey = prodSubgrupo_id, keyOnRelatedModel = id)
        return $this->belongsTo('App\GenIva', 'gen_iva_id');
    }

    public function GenUnidades()
    {
        // belongsTo(RelatedModel, foreignKey = prodSubgrupo_id, keyOnRelatedModel = id)
        return $this->belongsTo('App\GenUnidades', 'gen_unidades_id');
    }

    public function prodVencimiento()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = producto_id, localKey = id)
        return $this->hasMany(ProdVencimiento::class);
    }

    public function ProdPivotListaProducto()
    {
        // hasMany(RelatedModel, foreignKeyOnRelatedModel = producto_id, localKey = id)
        return $this->hasMany('App\ProdPivotListaProducto');
    }

    public function getDateFormat()
    {
        return dateTimeSql();
    }

    public static function todosConGrupos(){
    return DB::table('productos')
            ->select('productos.id As id','productos.nombre As nombre','productos.codigo As codigo','prod_subgrupos.nombre As subgrupo','prod_grupos.nombre as grupo','productos.activo as activo')
            ->join('prod_subgrupos', 'productos.prod_subgrupo_id', '=', 'prod_subgrupos.id')
            ->join('prod_grupos', 'prod_subgrupos.prodGrupo_id', '=', 'prod_grupos.id')
            ->get();
    }

    public static function productosCustom () {
        return DB::table('productos')
        ->select( DB::raw( 'productos.id As id,productos.nombre As nombre,productos.codigo As codigo,prod_subgrupos.nombre As subgrupo, prod_grupos.nombre as grupo, productos.activo as activo, cast(codigo as int) as codigoInt' ))
        ->join('prod_subgrupos', 'productos.prod_subgrupo_id', '=', 'prod_subgrupos.id')
        ->join('prod_grupos', 'prod_subgrupos.prodGrupo_id', '=', 'prod_grupos.id')
        ->get();
    }

    public static function todosConImpuestos(){
    return DB::table('productos')
            ->select('productos.id As id','productos.nombre As nombre','productos.codigo As codigo','gen_iva.valor_porcentaje as impuesto','productos.gen_unidades_id as unidades','gen_iva.id as gen_iva_id','productos.cod_ean_13 as ean13')
            ->join('gen_iva', 'productos.gen_iva_id', '=', 'gen_iva.id')
            ->get();
    }

    public static function ProductosxListaprecio($listaprecio_id){
    return DB::table('productos')
            ->select('productos.codigo','productos.nombre','gen_unidades.nombre as unidades','gen_unidades.id as unidad_id', 'gen_iva.valor_porcentaje as impuesto', 'prod_pivot_lista_productos.precio')
            ->join('gen_iva', 'productos.gen_iva_id', '=', 'gen_iva.id')
            ->join('prod_pivot_lista_productos', 'prod_pivot_lista_productos.producto_id', '=', 'productos.id')
            ->join('gen_unidades', 'gen_unidades.id', '=', 'productos.gen_unidades_id')
            ->where('prod_pivot_lista_productos.prodListaPrecio_id', $listaprecio_id)
            ->orderByRaw('CAST(codigo as int )')
            ->get();
    }

    public static function ProductosxListaprecioMarques($listaprecio_id){
    return DB::table('productos')
            ->select('productos.codigo','productos.nombre as designacao','gen_unidades.abrev_pos as unidades','gen_unidades.id as num_unidades', 'prod_pivot_lista_productos.precio as preco1','prod_grupos.id as familia')
            ->join('gen_iva', 'productos.gen_iva_id', '=', 'gen_iva.id')
            ->join('prod_pivot_lista_productos', 'prod_pivot_lista_productos.producto_id', '=', 'productos.id')
            ->join('gen_unidades', 'gen_unidades.id', '=', 'productos.gen_unidades_id')
            ->join('prod_subgrupos', 'prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos', 'prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->where('prod_pivot_lista_productos.prodListaPrecio_id', $listaprecio_id)
            ->get();
    }

    public static function ListadoProductos(){
    return DB::table('productos')
            ->select('productos.codigo','productos.nombre','gen_unidades.nombre as unidades','gen_iva.valor_porcentaje as impuesto')
            ->join('gen_iva', 'productos.gen_iva_id', '=', 'gen_iva.id')
            ->join('gen_unidades', 'gen_unidades.id', '=', 'productos.gen_unidades_id')
            ->orderByRaw('CAST(codigo as int )')
            ->get();
    }

}
