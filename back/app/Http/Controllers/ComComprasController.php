<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComCompra;
use App\ComTipoCompra;
use App\Inventario;
use App\GenEmpresa;
use App\Producto;
use App\TerceroSucursal;
use App\ComPivotCompraProducto;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class ComComprasController extends Controller
{
    public function index()
    {
        $index= ComCompra::todosConTipoSucursalGrupoTipo();
        return $index;
    }


    public function comprasPendientesPorSucursalYTipo($sucursal_id, $tipodoc_id)
    {
        $index = ComCompra::comprasPendientesPorSucursalYTipo($sucursal_id, $tipodoc_id);
        return $index;
    }

    public function store(Request $request)
    {

        $nuevoItem = new ComCompra($request->all());

        $nuevoItem->usuario_id = Auth::user()->id;

        $tipoCompra = ComTipoCompra::find($nuevoItem->com_tipo_compras_id);

        if ( count(ComCompra::where('com_tipo_compras_id', $nuevoItem->com_tipo_compras_id)->get()) > 0 ){
            $consecutivo = ComCompra::where('com_tipo_compras_id', $nuevoItem->com_tipo_compras_id)->get()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1;
        }else{
            $nuevoItem->consecutivo = $tipoCompra->consec_inicio;
        }

        // si es una devolucion cambia el estado del movimiento
        if ($tipoCompra->naturaleza == 0) {

            $docRelacionado = ComCompra::find($request->docReferencia['id']);
            self::descargarDevolucionInventario($docRelacionado->id);
            $docRelacionado->estado = 3;

            $docRelacionado->save();

            return 'done';

         // se valida si es un documento compra
        } elseif ($tipoCompra->naturaleza == 1) {

           $nuevoItem->estado = 1;
           $nuevoItem->saldo = $nuevoItem->total;

           $nuevoItem->save();

        }

        foreach ($request->lineas as $linea) {
            $nuevoPivot = new ComPivotCompraProducto($linea);
            $nuevoPivot->com_compras_id = $nuevoItem->id;
            $nuevoPivot->precio = intval($linea['precio']);
            $nuevoPivot->save();

            $itemInventario = Inventario::where('producto_id', $linea['producto_id'])->where('tipo_invent','!=',2)->get()->first();
            if ($itemInventario) {
                // calculo costo promedio
                $cantidadTotal = $itemInventario->cantidad + floatval($linea['cantidad']);

                if ($cantidadTotal != 0) {
                    $itemInventario->costo_promedio = intval(( ($itemInventario->cantidad/$cantidadTotal) * $itemInventario->costo_promedio ) + ( (floatval($linea['cantidad'])/$cantidadTotal) * intval($linea['precio'])));
                } else {
                    $itemInventario->costo_promedio = intval($linea['precio']);
                }


                $itemInventario->cantidad += floatval($linea['cantidad']);
                $itemInventario->save();
            } else {
                $nuevoInventario = new Inventario($linea);
                $nuevoInventario->costo_promedio = intval($linea['precio']);
                $nuevoInventario->cantidad = floatval($linea['cantidad']);
                $nuevoInventario->tipo_invent = 1;
                $nuevoInventario->save();
            }

        }

        return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];

    }

    public function generatePDF($id)
    {

        $compra = ComCompra::find($id);

        $tipoCompra = ComTipoCompra::find($compra->com_tipo_compras_id);

        $empresa = GenEmpresa::find(1);

        $lineas = ComPivotCompraProducto::where('com_compras_id', $id)->get();

        foreach ($lineas as $linea) {
            $producto = Producto::find($linea->producto_id);
            $linea->producto = $producto->nombre;
        }

        $sucursal = TerceroSucursal::find($compra->proveedor_id);
        $tercero = $sucursal->tercero;

        $data = ['compra' => $compra, 'lineas' => $lineas, 'tercero' => $tercero, 'sucursal' => $sucursal, 'tipoCompra' => $tipoCompra, 'empresa' => $empresa];

        if ($tipoCompra->formato_impresion == 1) {
            $pdf = PDF::loadView('compras.compra', $data);
        } elseif ($tipoCompra->formato_impresion == 2) {
            $pdf = PDF::loadView('compras.remision', $data);
        } elseif ($tipoCompra->formato_impresion == 3) {
            $pdf = PDF::loadView('compras.remision', $data);
        }

        return $pdf->stream();
    }

    public function cuentasPorPagar()
    {

        $lineas = ComCompra::cuentasPorPagar();

        $fecha = Carbon::now();

        $data = ['lineas' => $lineas, 'fecha' => $fecha];

        $pdf = PDF::loadView('compras.cuentasporpagar', $data);

        return $pdf->stream();
    }

    public function cuentasPorPagarxTercero($tercero_id)
    {

        $lineas = ComCompra::cuentasPorPagarxTercero($tercero_id);

        $fecha = Carbon::now();

        $data = ['lineas' => $lineas, 'fecha' => $fecha];

        $pdf = PDF::loadView('compras.cuentasporpagar', $data);

        return $pdf->stream();
    }

    public function comprasNetasPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $tiposcompra = ComTipoCompra::where('naturaleza', 1)->get();

        $comprasTotales = array();

        $granSubtotal = 0;
        $granDescuento = 0;
        $granIva = 0;
        $granTotal =0;

        $granDevSubtotal = 0;
        $granDevDescuento = 0;
        $granDevIva = 0;
        $granDevTotal =0;

        foreach ($tiposcompra as $tipo) {
            $ventas = ComCompra::comprasNetasPorFecha($tipo->id, $fechaIni, $fechaFin)->first();
            $devoluciones = ComCompra::devolucionesComprasNetasPorFecha($tipo->id, $fechaIni, $fechaFin)->first();

            if ($ventas->total > 0 || $devoluciones->total > 0) {
                array_push($comprasTotales, [$tipo->nombre, $ventas, $devoluciones]);
                $granSubtotal += $ventas->subtotal;
                $granDescuento += $ventas->descuento;
                $granIva += $ventas->ivatotal;
                $granTotal += $ventas->total;

                $granDevSubtotal += $devoluciones->subtotal;
                $granDevDescuento += $devoluciones->descuento;
                $granDevIva += $devoluciones->ivatotal;
                $granDevTotal += $devoluciones->total;
            }
        }

        $hoy = Carbon::now();

        $data = ['comprasTotales' => $comprasTotales,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'granSubtotal' => $granSubtotal,
                 'granDescuento' => $granDescuento,
                 'granIva' => $granIva,
                 'granTotal' => $granTotal,
                 'granDevSubtotal' => $granDevSubtotal,
                 'granDevDescuento' => $granDevDescuento,
                 'granDevIva' => $granDevIva,
                 'granDevTotal' => $granDevTotal,
                 'hoy' => $hoy, ];

        $pdf = PDF::loadView('compras.comprasnetasporfecha', $data);

        return $pdf->stream();
    }

    public static function  descargarDevolucionInventario($id){
        $lineas = ComPivotCompraProducto::porCompra($id);
        foreach($lineas as $linea){
            self::afectarInventario($linea->producto_id, $linea->cantidad, 0);
        }
    }

    public static function afectarInventario ($producto_id, $cantidad, $tipo_operacion) {

        if ($tipo_operacion == 1){//Devolucion
            $cantidad = floatval($cantidad);
        } else {
            $cantidad = -floatval($cantidad);//Venta
        }

        $itemInventario = Inventario::where('producto_id', $producto_id)->where('tipo_invent','!=',2)->get()->first();
        if ($itemInventario) {
            $itemInventario->cantidad += $cantidad;
            $itemInventario->save();
        } else {
            $nuevoInventario = new Inventario();
            $nuevoInventario->cantidad = $cantidad;
            $nuevoInventario->producto_id = $producto_id;
            $nuevoInventario->costo_promedio = 0;
            $nuevoInventario->tipo_invent = 1;
            $nuevoInventario->save();
        }
    }

}
