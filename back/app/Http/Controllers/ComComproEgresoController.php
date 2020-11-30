<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComComproEgreso;
use App\ComTipoComproEgreso;
use App\ComPivotCompraEgreso;
use App\GenEmpresa;
use App\TerceroSucursal;
use App\GenMunicipio;
use App\GenDepartamento;
use App\ComPivotFormaEgreso;
use App\GenImpresora;
use App\ComCompra;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;

class ComComproEgresoController extends Controller
{
        public function index()
    {
        $index= ComComproEgreso::todosConTipoSucursal();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new ComComproEgreso($request->all());

        $nuevoItem->usuario_id = Auth::user()->id;

        $tipoComproEgreso = ComTipoComproEgreso::find($request->com_tipo_compro_egresos_id);

        if ( count(ComComproEgreso::where('com_tipo_compro_egresos_id', $nuevoItem->com_tipo_compro_egresos_id)->get()) > 0 ){
            $consecutivo = ComComproEgreso::where('com_tipo_compro_egresos_id', $nuevoItem->com_tipo_compro_egresos_id)->get()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1; 
        }else{
            $nuevoItem->consecutivo = $tipoComproEgreso->consec_inicio;
        }

        $nuevoItem->save();

        $total = $request->total;

        // se guardan las formas de pago referenciadas al recibo
        foreach ($request->pagos as $pago) {
            $nuevoPago = new ComPivotFormaEgreso($pago);
            $nuevoPago->com_compro_egresos_id = $nuevoItem->id;
            $nuevoPago->save();
        }

        // se realiza la el descuento a las facturas y el cruce con los recibos de caja
        foreach ($request->facturas as $factura) {
            if ($total > 0) {

                if($total >= $factura['saldo']) {
                    $cruce = new ComPivotCompraEgreso();
                    $cruce->com_compras_id = $factura['id'];
                    $cruce->com_compro_egresos_id = $nuevoItem->id;
                    $cruce->valor = $factura['saldo'];
                    $cruce->save();

                    $total = $total - $factura['saldo'];

                    $facturaActual = ComCompra::find($factura['id']);
                    $facturaActual->saldo = 0;
                    $facturaActual->estado = 0;
                    $facturaActual->save();
                } else {
                    $cruce = new ComPivotCompraEgreso();
                    $cruce->com_compras_id = $factura['id'];
                    $cruce->com_compro_egresos_id = $nuevoItem->id;
                    $cruce->valor = $total;
                    $cruce->save();

                    $facturaActual = ComCompra::find($factura['id']);
                    $facturaActual->saldo = intval($facturaActual->saldo) - intval($total);
                    $facturaActual->estado = 1;
                    $facturaActual->save();

                    $total = 0;
                }
            }
        }

        return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];
    }

    public function generatePDF($id)
    {
        $empresa = GenEmpresa::find(1);

        $comprobante = ComComproEgreso::find($id);

        $tipoComproEgreso = ComTipoComproEgreso::find($comprobante->com_tipo_compro_egresos_id);

        $lineas = ComPivotCompraEgreso::getDataLineasPDF($id);
        $pagos = ComPivotFormaEgreso::getDataLineasPDF($id);

        $sucursal = TerceroSucursal::find($comprobante->proveedor_id);
        $tercero = $sucursal->tercero;

        $data = ['comprobante' => $comprobante, 'tipoComproEgreso' => $tipoComproEgreso, 'sucursal' => $sucursal, 'tercero' => $tercero, 'lineas' => $lineas, 'pagos' => $pagos, 'empresa' => $empresa ];

        $pdf = PDF::loadView('compras.comproegreso', $data);

        return $pdf->stream();
    }

    public function printPOS($id)
    {
        $empresa = GenEmpresa::find(1);

        $recibo = ComComproEgreso::find($id);

        $tipoComproEgreso = ComTipoComproEgreso::find($recibo->com_tipo_compro_egresos_id);

        $lineas = ComPivotCompraEgreso::getDataLineasPDF($id);
        $pagos = ComPivotFormaEgreso::getDataLineasPDF($id);

        $sucursal = TerceroSucursal::find($recibo->proveedor_id);
        $tercero = $sucursal->tercero;

        $municipio = GenMunicipio::find($empresa->gen_municipios_id);

        $departamento = GenDepartamento::find($municipio->departamento_id);

        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $img = EscposImage::load("../public/images/logo1.png");
        $printer -> graphics($img);

        $etiqueta = str_pad("", 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->razon_social), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->nombre), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("NIT: ".$empresa->nit, 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->tipo_regimen), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->direccion), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($municipio->nombre)." - ".strtoupper($departamento->nombre), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("TEL: ".$empresa->telefono, 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($tipoComproEgreso->nombre), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);

        $etiqueta .= str_pad("No. ".$recibo->consecutivo, 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("FECHA DOCUMENTO: ".$recibo->fecha_comprobante, 48, " ", STR_PAD_BOTH);

        // DATOS DEL CLIENTE
        $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);
        $etiqueta .= tercero_pos($tercero->nombre);
        if ($tercero->digito_verificacion) {
            $etiqueta .= str_pad("DOC: ".$tercero->documento.'-'.$tercero->digito_verificacion.' - TEL: '.$sucursal->telefono, 48, " ", STR_PAD_RIGHT);
        } else {
            $etiqueta .= str_pad("DOC: ".$tercero->documento.' - TEL: '.$sucursal->telefono, 48, " ", STR_PAD_RIGHT);
        }
        $etiqueta .= str_pad("DIRECCION: ".$sucursal->direccion, 48, " ", STR_PAD_RIGHT);

        $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);

        $etiqueta .= str_pad("No. Fac   Valor Factura     Valor Abono    Saldo", 48, "-", STR_PAD_BOTH);

        $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);

        foreach ($lineas as $linea) {
            $etiqueta .= str_pad($linea->consec_mov, 9, " ", STR_PAD_RIGHT);
            $etiqueta .= str_pad('$ '.number_format(intval($linea->valor_factura), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('$ '.number_format(intval($linea->valor), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('$ '.number_format(intval($linea->saldo_mov), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
        }

        $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);
        $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);

        $totalPagos = 0;

        $etiqueta .= str_pad("PAGOS", 48, "-", STR_PAD_BOTH);

        foreach ($pagos as $pago) {
            if ($pago->valor > 0) {
                $etiqueta .= str_pad(eliminar_acentos(strtoupper($pago->forma_nombre)), 36, " ", STR_PAD_RIGHT);
                $etiqueta .= '$';
                $etiqueta .= ' ';
                $etiqueta .= str_pad(number_format($pago->valor, 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                $totalPagos = intval($totalPagos) + intval($pago->valor);
            }
        }

        $etiqueta .= str_pad("----------", 48, " ", STR_PAD_LEFT);
        $etiqueta .= str_pad('TOTAL PAGADO', 36, " ", STR_PAD_RIGHT);
        $etiqueta .= '$';
        $etiqueta .= ' ';
        $etiqueta .= str_pad(number_format($totalPagos, 0, ',', '.'), 10, " ", STR_PAD_LEFT);
        $etiqueta .= str_pad("----------", 48, "-", STR_PAD_LEFT);

        $printer->text($etiqueta);
        $printer->feed(2);
        $printer->cut();
        $printer->pulse();
        $printer->close();
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
