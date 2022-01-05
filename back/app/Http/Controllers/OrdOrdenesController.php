<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use App\Producto;
use App\TerceroSucursal;
use App\OrdTipoOrden;
use App\OrdOrden;
use App\GenEmpresa;
use App\OrdPivotOrdenProducto;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use App\OrdPivotFormasPago;


class OrdOrdenesController extends Controller
{
    public function index()
    {
        $index= OrdOrden::todos();
        return $index;
    }

    public function ordenesPorAutorizacion($auth)
    {
        $index= OrdOrden::ordenesPorAutorizacion($auth);
        return $index;
    }

    public function ordenesPorTipoOrden($tipo_id)//select Tipos de ordenes.
    {
        $index= OrdOrden::ordenesPorTipoOrden($tipo_id);
        return $index;
    }

    public function store(Request $request)
    {

        $nuevoItem = new OrdOrden($request->all());

        $nuevoItem->usuario_id = Auth::user()->id;
        $nuevoItem->autorizacion = 2;
        $tipoOrden = OrdTipoOrden::find($nuevoItem->ord_tipo_orden_id);

        if ( count(OrdOrden::where('ord_tipo_orden_id', $nuevoItem->ord_tipo_orden_id)->get()) > 0 ){
            $consecutivo = OrdOrden::where('ord_tipo_orden_id', $nuevoItem->ord_tipo_orden_id)->get()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1;
        }else{
            $nuevoItem->consecutivo = $tipoOrden->consec_inicio;
        }

       $nuevoItem->estado = 1;
       $nuevoItem->saldo = $nuevoItem->total;

       $nuevoItem->save();

        foreach ($request->lineas as $linea) {
            $nuevoPivot = new OrdPivotOrdenProducto($linea);
            $nuevoPivot->ord_orden_id = $nuevoItem->id;
            $nuevoPivot->precio = intval($linea['precio']);
            $nuevoPivot->save();
        }

        foreach ($request->pagos as $pago) {

            if ($pago['valor'] > 0) {
                $nuevoPago = new OrdPivotFormasPago();
                $nuevoPago->ord_orden_id = $nuevoItem->id;
                $nuevoPago->ord_formas_pago_id = $pago['id'];
                $nuevoPago->valor_abonado = $pago['valor'];
                $nuevoPago->save();
            }
        }

        return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];

    }

    public function generatePDF($id)
    {

        $orden = OrdOrden::find($id);

        $tipoOrden = OrdTipoOrden::find($orden->ord_tipo_orden_id);

        $empresa = GenEmpresa::find(1);

        $lineas = OrdPivotOrdenProducto::where('ord_orden_id', $id)->get();

        foreach ($lineas as $linea) {
            $producto = Producto::find($linea->producto_id);
            $linea->producto = $producto->nombre;
        }

        $sucursal = TerceroSucursal::find($orden->tercero_sucursal_id);
        $tercero = $sucursal->tercero;

        $data = ['orden' => $orden, 'lineas' => $lineas, 'tercero' => $tercero, 'sucursal' => $sucursal, 'tipoOrden' => $tipoOrden, 'empresa' => $empresa];


        $pdf = PDF::loadView('ordenes.orden', $data);

        return $pdf->stream();
    }

    public function ordenParaFactura ($consec_orden, $tipodoc_id) {

        $tipoOrden = OrdTipoOrden::where('fac_tipo_doc_id', $tipodoc_id)->get()->first();

        $orden = OrdOrden::where('ord_tipo_orden_id', $tipoOrden->id)->where('consecutivo', $consec_orden)->get()->first();

        $lineas = OrdPivotOrdenProducto::where('ord_orden_id', $orden->id)->get();

        return $lineas;

    }

    public function ordenParaCompra ($consec_orden, $tipodoc_id) {

        $tipoOrden = OrdTipoOrden::where('com_tipo_compra_id', $tipodoc_id)->get()->first();

        $orden = OrdOrden::where('ord_tipo_orden_id', $tipoOrden->id)->where('consecutivo', $consec_orden)->get()->first();
        if ($orden->autorizacion == 1) {
            $lineas = OrdPivotOrdenProducto::where('ord_orden_id', $orden->id)->get();
        } else {
            return 'noauth';
        }
        return $lineas;
    }

    public static function cambiarAutorizacion ($ord_id, $auth_id) {
        $ord_orden = OrdOrden::where('id', $ord_id)->get()->first();
        $ord_orden->autorizacion = $auth_id;
        $ord_orden->save();
        return 'done';
    }
}
