<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LotLiquidacion;
use App\LotPivotLiquiProducto;
use App\LotProgramacion;
use App\Producto;
use App\Lote;
use App\ComCompra;
use PDF;
use App\LotPesosProgramacion;
use App\GenEmpresa;
use App\GenMunicipio;
use App\GenDepartamento;

class LotLiquidacionesController extends Controller
{
    public function store(Request $request)
    {
		$nuevoItem = new LotLiquidacion($request->all());
		$nuevoItem->prog_lotes_id = $request->programacion['id'];
        $nuevoItem->ppe = $request->pesosCompraTotal['ppe'];
        $nuevoItem->pcc = $request->pesosCompraTotal['pcc'];
        $nuevoItem->pcr = $request->pesosCompraTotal['pcr'];
        $nuevoItem->save();

        foreach ($request->productosSumatoria as $producto) {
        	$nuevoProducto = new LotPivotLiquiProducto($producto);
        	$nuevoProducto->cantidad = $producto['peso'];
        	$nuevoProducto->precio_venta = $producto['precio'];
        	$nuevoProducto->lot_liquidaciones_id = $nuevoItem->id;
        	$nuevoProducto->tipo_producto = 1;
        	if ( strrpos($producto['almacenamiento'], "vacio")) {
        		$nuevoProducto->vacio = 1;	
        	} else {
               $nuevoProducto->vacio = 0;    
            }

        	$nuevoProducto->save();
        }

        foreach ($request->productosRecuperaciones as $producto) {
        	$nuevoProducto = new LotPivotLiquiProducto($producto);
        	$nuevoProducto->cantidad = $producto['peso'];
        	$nuevoProducto->precio_venta = $producto['precio'];
        	$nuevoProducto->lot_liquidaciones_id = $nuevoItem->id;
        	$nuevoProducto->tipo_producto = 2;
        	$nuevoProducto->vacio = 0;

        	$nuevoProducto->save();
        }

        $programacion = LotProgramacion::find($request->programacion['id']);

        $programacion->estado = 2;

        $programacion->save();

        return ['done', $nuevoItem->id];
    }

    public function printLiquidacion($id)
    {
        $empresa = GenEmpresa::find(1);
        $empresa->municipio = GenMunicipio::find($empresa->gen_municipios_id)->nombre;
        $empresa->departamento = GenDepartamento::find(GenMunicipio::find($empresa->gen_municipios_id)->departamento_id)->nombre;
    	
    	$programacion = LotProgramacion::find($id);

        $lote = Lote::find($programacion->lote_id);

        $procedencia = ComCompra::proveedorPorProgramacion($id)->first();

    	$liquidacion = LotLiquidacion::where('prog_lotes_id', $programacion->id)->get()->first();

    	$pesosCompra = LotPesosProgramacion::where('lotProgramacion_id', $programacion->id)->get();

        $pesosCompraTotal = ['ppe' => $liquidacion->ppe, 'pcc' => $liquidacion->pcc, 'pcr' => $liquidacion->pcr];

    	$productos = LotPivotLiquiProducto::where('lot_liquidaciones_id', $liquidacion->id)->where('tipo_producto', 1)->get();
    	$recuperaciones = LotPivotLiquiProducto::where('lot_liquidaciones_id', $liquidacion->id)->where('tipo_producto', 2)->get();

    	$kilosVacio = 0;
    	$kilosCarne = 0;
    	$ventaTotal = 0;

    	foreach ($productos as $producto) {
    		if ($producto->vacio) {
    			$kilosVacio = $producto->cantidad + $kilosVacio;
    		}
    		$kilosCarne = $producto->cantidad + $kilosCarne;
    		$producto->nombre = Producto::find($producto->producto_id)->nombre;
    		$ventaTotal = ($producto->cantidad * $producto->precio_venta) + $ventaTotal;
    	}

    	foreach ($recuperaciones as $producto) {
    		$producto->nombre = Producto::find($producto->producto_id)->nombre;
    		$ventaTotal = ($producto->cantidad * $producto->precio_venta) + $ventaTotal;
    	}

    	$kilosCarneProm = $kilosCarne / $programacion->num_animales;

    	$costoTotal = ($liquidacion->costoPrecio*$liquidacion->ppe) + ($liquidacion->costoSacrificio*$programacion->num_animales) + ($liquidacion->costoDesposte*$programacion->num_animales) + ($liquidacion->costoTransporte*$programacion->num_animales) + ($liquidacion->costoEmpaque*$kilosVacio);

        $data = [
        	'programacion' => $programacion,
        	'liquidacion' => $liquidacion,
        	'costoTotal' => $costoTotal, 
        	'ventaTotal' => $ventaTotal,
        	'productos' => $productos,
        	'recuperaciones' => $recuperaciones,
        	'kilosVacio' => $kilosVacio,
        	'ppe' => $liquidacion->ppe,
        	'pcc' => $liquidacion->pcc,
        	'pcr' => $liquidacion->pcr,
        	'kilosCarneProm' => $kilosCarneProm,
            'procedencia' => $procedencia,
            'lote' => $lote,
            'empresa' => $empresa
        ];
        
        $pdf = PDF::loadView('informes.liquidacion', $data);
        return $pdf->stream();
    }

    public function eliminarLiquidacion($id)
    {
    	$programacion = LotProgramacion::find($id);

    	$liquidacion = LotLiquidacion::where('prog_lotes_id', $programacion->id)->get()->first();

    	$pivot = LotPivotLiquiProducto::where('lot_liquidaciones_id', $liquidacion->id)->get();

    	foreach ($pivot as $item) {
    		$item->delete();
    	}

    	$liquidacion->delete();

    	$programacion->estado = 1;

    	$programacion->save();

    	return 'done';
    }

}
