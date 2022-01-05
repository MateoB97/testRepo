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
use App\OrdOrden;
use App\OrdPivotFormasPago;
use App\ComComproEgreso;
use App\ComTipoComproEgreso;
use App\ComPivotCompraEgreso;
use App\OrdTipoOrdenes;

class ComComprasController extends Controller
{
    public function index()
    {
        $index= ComCompra::todosConTipoSucursalGrupoTipo();
        return $index;
    }

    public function comprasPorAutorizacion($tipo_id, $auth_id){
        $index = ComCompra::comprasPorTipoAuth($tipo_id, $auth_id);
        return $index;
    }

    public function comprasPorTipo($tipo_id)
    {
        $index = ComCompra::comprasPorTipoCompra($tipo_id);
        return $index;
    }

    public function comprasPendientesPorSucursalYTipo($sucursal_id, $tipodoc_id)
    {
        $index = ComCompra::comprasPendientesPorSucursalYTipo($sucursal_id, $tipodoc_id);
        return $index;
    }

    public function comprasPendientesPorSucursalYTipoAuth($sucursal_id, $tipodoc_id)
    {
        $index = ComCompra::comprasPendientesPorSucursalYTipoAuth($sucursal_id, $tipodoc_id);
        return $index;
    }

    public function store(Request $request)
    {

        $nuevoItem = new ComCompra($request->all());
        $nuevoItem->autorizacion = 2;
        $nuevoItem->usuario_id = Auth::user()->id;

        $tipoCompra = ComTipoCompra::find($nuevoItem->com_tipo_compras_id);

        // self::abonoAComprobanteEgre($request->orden, $nuevoItem->id, $request->com_tipo_compras_id, $request->proveedor_id, $request->fecha_compra);

        if ( count(ComCompra::where('com_tipo_compras_id', $nuevoItem->com_tipo_compras_id)->get()) > 0 ){
            $consecutivo = ComCompra::where('com_tipo_compras_id', $nuevoItem->com_tipo_compras_id)->get()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1;
        }else{
            $nuevoItem->consecutivo = $tipoCompra->consec_inicio;
        }

        // si es una devolucion cambia el estado del movimiento
        if ($tipoCompra->naturaleza == 0) {

            $docRelacionado = ComCompra::find($request->docReferencia['id']);
            self::descargarDevolucionInventario($docRelacionado->id, 0);
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

            $producto = Producto::find($linea['producto_id']);

            if (!is_null($producto->cod_prod_padre)) {
                $linea['producto_id'] = Producto::where('codigo', $producto->cod_prod_padre)->get()->first()->id;
            }

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

        if ($request->orden != '') {
            $tipoOrden = OrdTipoOrdenes::where('com_tipo_compra_id', $request->com_tipo_compras_id)->get()->first();
            // $orden = OrdOrden::where('consecutivo', $request->orden)->first(); // Orden de compra
            $orden = OrdOrden::where('ord_tipo_orden_id', $tipoOrden->id)->where('consecutivo', $request->orden)->get()->first();
            $detalles =  OrdPivotFormasPago::where('ord_orden_id', $orden->id)->get(); // Detalles Abonos de la orden
            $total = 0;
            // if ($detalles) {
                $total = $detalles->sum('valor_abonado');
                $comEgre = ComTipoComproEgreso::conDocRelacionadoPorId($request->com_tipo_compras_id)->first(); // ID Comprobante Egreso + Nombre
                $comComproEgreso = new ComComproEgreso();
                if ( count(ComComproEgreso::where('com_tipo_compro_egresos_id', $comEgre->id)->get()) > 0 ){
                    $consecutivo = ComComproEgreso::where('com_tipo_compro_egresos_id', $comEgre->id)->get()->last();
                    $comComproEgreso->consecutivo = $consecutivo->consecutivo + 1;
                }else{
                    $comComproEgreso->consecutivo = $comEgre->consec_inicio;
                }
                $comComproEgreso->usuario_id = Auth::user()->id;
                $comComproEgreso->com_tipo_compro_egresos_id = $comEgre->id;
                $comComproEgreso->proveedor_id = $request->proveedor_id;
                $comComproEgreso->abono = $total;
                $comComproEgreso->total = $total;
                $comComproEgreso->ajuste_observacion = 'Sin observaciones';
                $comComproEgreso->fecha_comprobante = str_replace('/', '-', $request->fecha_compra);
                $comComproEgreso->ajuste = 0;
                $comComproEgreso->save();

                if ($total > 0) {
                    if($total >= $nuevoItem->saldo) {
                        $cruce = new ComPivotCompraEgreso();
                        $cruce->com_compras_id = $nuevoItem->id;
                        $cruce->com_compro_egresos_id = $comComproEgreso->id;
                        $cruce->valor = $nuevoItem->saldo;
                        $cruce->save();

                        $total = $total - $nuevoItem->saldo;

                        $compraActual = ComCompra::find($nuevoItem->id);
                        $compraActual->saldo = 0;
                        $compraActual->estado = 0;
                        $compraActual->save();
                    } else {
                        $cruce = new ComPivotCompraEgreso();
                        $cruce->com_compras_id = $nuevoItem->id;
                        $cruce->com_compro_egresos_id = $comComproEgreso->id;
                        $cruce->valor = $total;
                        $cruce->save();

                        $compraActual = ComCompra::find($nuevoItem->id);
                        $compraActual->saldo = intval($compraActual->saldo) - intval($total);
                        $compraActual->estado = 1;
                        $compraActual->save();

                        $total = 0;
                    }
                    return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];
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

    public static function  descargarDevolucionInventario($id, $tipo_operacion){
        $lineas = ComPivotCompraProducto::porCompra($id);
        foreach($lineas as $linea){
            self::afectarInventario($linea->producto_id, $linea->cantidad, $tipo_operacion);
        }
    }

    public static function afectarInventario ($producto_id, $cantidad, $tipo_operacion) {

        if ($tipo_operacion == 1){//Devolucion
            $cantidad = floatval($cantidad);
        } else {
            $cantidad = -floatval($cantidad);//Venta
        }

        $producto = Producto::find($producto_id);

        if (!is_null($producto->cod_prod_padre)) {
            $producto_id = Producto::where('codigo', $producto->cod_prod_padre)->get()->first()->id;
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

    public static function cambiarAutorizacion ($com_id, $auth_id) {
        $com_compra = ComCompra::where('id', $com_id)->get()->first();
        $com_compra->autorizacion = $auth_id;
        $com_compra->save();
        return 'done';
    }

}
