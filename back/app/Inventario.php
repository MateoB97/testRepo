<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventario extends Model
{
    protected $table = 'inventarios';

	protected $fillable = ['cantidad','producto_id','costo_promedio','estado','tipo_invent'];

	public function getDateFormat()
	{
	    return dateTimeSql();
	}

	public static function todosConCodigoProducto(){
    return DB::table('inventarios')
            ->select(
            	'productos.id As id',
            	'productos.nombre As nombre',
            	'prod_subgrupos.nombre As subgrupo',
            	'prod_grupos.nombre As grupo',
            	'inventarios.costo_promedio As costo_promedio',
            	'inventarios.cantidad As cantidad',
            	'productos.codigo As codigo'
            )
            ->join('productos', 'productos.id', '=', 'inventarios.producto_id')
            ->join('prod_subgrupos', 'productos.prod_subgrupo_id', '=', 'prod_subgrupos.id')
            ->join('prod_grupos', 'prod_subgrupos.prodGrupo_id', '=', 'prod_grupos.id')
            ->get();
    }

    public static function todosConDatosFilter($lote,$fecha_inicial,$fecha_final,$producto_id,$estado){
    return DB::table('inventarios')
            ->select(
                'inventarios.id as id',
                'productos.nombre As producto',
                'productos.id as producto_id',
                'inventarios.cantidad as peso',
                'lotes.id as lote',
                'lotes.marca as marca',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'producto_terminados.created_at as fecha_empaque',
                'prod_grupos.nombre as grupo',
                'sal_pivot_invent_salidas.salMercancia_id as despacho',
                'producto_terminados.almacenamiento as empaque',
                'lot_programaciones.id as programacion'
            )
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones','lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->leftJoin('sal_pivot_invent_salidas', 'sal_pivot_invent_salidas.inventario_id', '=', 'inventarios.id')
            ->when($lote, function ($query, $lote) {
                return $query->where('lotes.id', $lote);
            })
            ->when($producto_id, function ($query, $producto_id) {
                return $query->where('productos.id', $producto_id);
            })
            ->when($fecha_inicial, function ($query, $fecha_inicial) {
                return $query->where('inventarios.created_at','>', $fecha_inicial);
            })
            ->when($fecha_final, function ($query, $fecha_final) {
                return $query->where('inventarios.created_at','<', $fecha_final);
            })
            ->when($estado, function ($query, $estado) {
                return $query->whereIn('inventarios.estado', $estado);
            })
            ->orderBy('inventarios.id','desc')
            ->get();
    }

    public static function GetDataEtiqueta($id){
    return DB::table('inventarios')
            ->select(
                'productos.nombre As producto',
                'inventarios.cantidad as peso',
                'lotes.id as lote',
                'lotes.marca as marca',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'producto_terminados.created_at as fecha_empaque',
                'prod_grupos.registro_sanitario as registro_sanitario',
                'prod_grupos.nombre as grupo',
                'prod_subgrupos.nombre as subgrupo_nombre',
                'prod_subgrupos.encabezado_etiqueta',
                'lotes.producto_aprobado'
            )
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones','lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->where('inventarios.id','=', $id)
            ->get();
    }


    public static function GetDataSalMercancia($id){
    return DB::table('inventarios')
            ->select(
                'inventarios.id as id',
                'productos.nombre As producto',
                'inventarios.cantidad as peso',
                'productos.id as producto_id'
            )
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->where('inventarios.id','=', $id)
            ->where('inventarios.tipo_invent','=', 2)
            ->get();
    }

    public static function GetDataExistentes($idproducto, $idprogramacion){
    return DB::table('inventarios')
            ->select(DB::raw('count(*) as existentes_counter'))
            ->join('producto_terminados','producto_terminados.invent_id', '=', 'inventarios.id')
            ->where('inventarios.producto_id','=', $idproducto)
            ->where('producto_terminados.prog_lotes_id','=', $idprogramacion)
            ->get();
    }

    public static function todosConDatos(){
    return DB::table('inventarios')
            ->select(
                'inventarios.id as id',
                'productos.nombre As producto',
                'inventarios.cantidad as peso',
                'lotes.id as lote',
                'lotes.marca as marca',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'producto_terminados.created_at as fecha_empaque',
                'prod_grupos.nombre as grupo'
            )
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones','lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->orderBy('inventarios.id','desc')
            ->take(1000)
            ->get();
    }

    public static function productosPorProgramacion($id){
    return DB::table('inventarios')
            ->select(
                'inventarios.id as id',
                'productos.nombre As producto',
                'productos.id as producto_id',
                'inventarios.cantidad as peso',
                'lotes.id as lote',
                'lot_programaciones.id as programacion',
                'almacenamiento as almacenamiento'
            )
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones','lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->where('lot_programaciones.id', $id)
            ->orderBy('inventarios.id','desc')
            ->get();
    }

    public static function productosPorLote($id){
    return DB::table('inventarios')
            ->select(
                'inventarios.id as id',
                'inventarios.estado as estado',
                'productos.nombre As producto',
                'productos.id as producto_id',
                'inventarios.cantidad as peso',
                'lotes.id as lote',
                'lotes.marca as marca',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'producto_terminados.created_at as fecha_empaque',
                'prod_grupos.nombre as grupo',
                'lot_programaciones.id as programacion'
            )
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones','lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->where('lotes.id', $id)
            ->get();
    }

    public static function productosPorLoteEnPlanta($id){
    return DB::table('inventarios')
            ->select(
                'inventarios.id as id',
                'inventarios.estado as estado',
                'productos.nombre As producto',
                'inventarios.cantidad as peso',
                'lotes.id as lote',
                'lotes.marca as marca',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'producto_terminados.created_at as fecha_empaque',
                'prod_grupos.nombre as grupo'
            )
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones','lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos','productos.id', '=', 'inventarios.producto_id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->where('lotes.id', $id)
            ->whereIn('inventarios.estado', [1, 2])
            ->get();
    }
}
