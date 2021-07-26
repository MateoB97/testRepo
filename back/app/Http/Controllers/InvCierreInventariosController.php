<?php

namespace App\Http\Controllers;

use App\Exports\CierreInventarioPesadas;
use Illuminate\Http\Request;
use App\Inventario;
use App\InvCierreInventario;
use App\InvPivotCierreProductos;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CierreInventarioVariacion;

class InvCierreInventariosController extends Controller
{

    public function index()
    {
        $index= InvCierreInventario::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function getDataCierre(){
        $index= InvCierreInventario::getDataCierreInventario();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new InvCierreInventario($request->all());
        $nuevoItem->save();

        foreach ($request->lineas as $producto) {

        	$productoInventario = Inventario::where('producto_id', $producto['producto_id'])->where('tipo_invent', 1)->get()->first();
        	$productoInventario->cantidad = $producto['cantidad_cierre'];
        	$productoInventario->save();

        	$productoCierre = new InvPivotCierreProductos();
        	$productoCierre->cantidad_stock = $producto['cantidad'];
        	$productoCierre->cantidad_cierre = $producto['cantidad_cierre'];
        	$productoCierre->precio_al_cierre = $producto['precio'];
        	$productoCierre->inv_cierre_id = $nuevoItem->id;
        	$productoCierre->producto_id = $producto['producto_id'];

        	$productoCierre->save();
        }

        return 'done';
    }

    public function show($id)
    {
        $model = UserPermisosCategorias::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = UserPermisosCategorias::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = UserPermisosCategorias::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public static function cierreInventarioVariacion() {
        $params = $_GET;
        $fecha_inicial = str_replace('/', '-', $params['fecha_inicial']);
        $fecha_final = str_replace('/', '-', $params['fecha_final']);
        return Excel::download(new CierreInventarioVariacion($fecha_inicial, $fecha_final), 'Cierre Inventario Variación-'.date('Y-m-d').'.xlsx');
    }

    public static function cierreInventarioPesadas() {
        $params = $_GET;
        $fecha_inicial = str_replace('/', '-', $params['fecha_inicial']);
        return Excel::download(new CierreInventarioPesadas($fecha_inicial), 'Cierre Inventario Pesadas -'.date('Y-m-d').'.xlsx');
    }

}
