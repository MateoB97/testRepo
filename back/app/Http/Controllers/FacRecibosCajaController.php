<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacReciboCaja;
use App\FacPivotFormaRecibo;
use App\GenEmpresa;
use App\FacMovimiento;
use App\FacPivotRecMov;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use App\GenImpresora;
use App\GenMunicipio;
use App\GenDepartamento;
use App\FacTipoRecCaja;
use App\GenCuadreCaja;
use Illuminate\Support\Facades\Auth;
use PDF;
use App\TerceroSucursal;
use App\ReportesT80;
use App\User;

class FacRecibosCajaController extends Controller
{

    public function index()
    {
        $index= FacReciboCaja::todosConTipoSucursal();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new FacReciboCaja($request->all());

        $nuevoItem->gen_cuadre_caja_id = GenCuadreCaja::where('estado', 1)->where('usuario_id', Auth::user()->id)->get()->first()->id;

        $tipoRecCaja = FacTipoRecCaja::find($request->fac_tipo_rec_caja_id);

        if ( count(FacReciboCaja::where('fac_tipo_rec_caja_id', $nuevoItem->fac_tipo_rec_caja_id)->get()) > 0 ){
            $consecutivo = FacReciboCaja::where('fac_tipo_rec_caja_id', $nuevoItem->fac_tipo_rec_caja_id)->get()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1;
        }else{
            $nuevoItem->consecutivo = $tipoRecCaja->consec_inicio;
        }

        $nuevoItem->save();

        $total = $request->total;

        // se guardan las formas de pago referenciadas al recibo
        foreach ($request->pagos as $pago) {
            $nuevoPago = new FacPivotFormaRecibo($pago);
            $nuevoPago->fac_recibo_id = $nuevoItem->id;
            $nuevoPago->save();
        }

        // se realiza la el descuento a las facturas y el cruce con los recibos de caja
        foreach ($request->facturas as $factura) {
            if ($total > 0) {

                if($total >= $factura['saldo']) {
                    $cruce = new FacPivotRecMov();
                    $cruce->fac_mov_id = $factura['id'];
                    $cruce->fac_recibo_id = $nuevoItem->id;
                    $cruce->valor = $factura['saldo'];
                    $cruce->save();

                    $total = $total - $factura['saldo'];

                    $facturaActual = FacMovimiento::find($factura['id']);
                    $facturaActual->saldo = 0;
                    $facturaActual->estado = 0;
                    $facturaActual->save();
                } else {
                    $cruce = new FacPivotRecMov();
                    $cruce->fac_mov_id = $factura['id'];
                    $cruce->fac_recibo_id = $nuevoItem->id;
                    $cruce->valor = $total;
                    $cruce->save();

                    $facturaActual = FacMovimiento::find($factura['id']);
                    $facturaActual->saldo = intval($facturaActual->saldo) - intval($total);
                    $facturaActual->estado = 1;
                    $facturaActual->save();

                    $total = 0;
                }
            }
        }

        return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];
    }

    public function anular ($id) {

        $recibo = FacReciboCaja::find($id);
        $recibo->estado = 0;
        $recibo->save();

        $pivotRecMov = FacPivotRecMov::where('fac_recibo_id', $id)->get();

        foreach ($pivotRecMov as $item) {
            $mov = FacMovimiento::find($item->fac_mov_id);
            $mov->saldo += intval($item->valor);
            $mov->estado = 1;
            $mov->save();
        }

        return 'done';

    }

    public function generatePDF($id)
    {
        $empresa = GenEmpresa::find(1);

        $recibo = FacReciboCaja::find($id);

        $tipoRecCaja = $recibo->tipoRecCaja;

        $lineas = FacPivotRecMov::getDataLineasPDF($id);
        $pagos = FacPivotFormaRecibo::getDataLineasPDF($id);

        $sucursal = $recibo->sucursal;
        $tercero = $sucursal->tercero;

        $data = ['recibo' => $recibo, 'tipoRecCaja' => $tipoRecCaja, 'sucursal' => $sucursal, 'tercero' => $tercero, 'lineas' => $lineas, 'pagos' => $pagos, 'empresa' => $empresa ];

        $pdf = PDF::loadView('facturacion.recibocaja', $data);

        return $pdf->stream();
    }

    public function printPOS($id)
    {
        $recibo = FacReciboCaja::find($id);

        $tipoRecCaja = $recibo->tipoRecCaja;
        $sucursal = TerceroSucursal::find($recibo->tercero_sucursal_id);
        $tercero = $sucursal->Tercero;
        $lineas = FacPivotRecMov::getDataLineasPDF($id);
        $pagos = FacPivotFormaRecibo::getDataLineasPDF($id);

        $user = User::find(2);

        // $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $t80 = new ReportesT80();
        $str = '';

        $str .= $t80->posLineaBlanco(' ');
        $str .=$t80->printLogoT80($printer);
        $str .= $t80->posLineaBlanco('-');
        $str .= $t80->posHeaderEmpresa();
        $str .= $t80->posLineaBlanco();
        $str .= $t80->posLineaCentro($tipoRecCaja->nombre);
        $str .= $t80->posLineaBlanco(' ');

        $str .= $t80->posLineaCentro('No. '. $recibo->consecutivo);
        $str .= $t80->posLineaCentro('fecha documento: '. $recibo->fecha_recibo);
        $str .= $t80->posLineaGuion();

        // DATOS DEL CLIENTE
        $str .= $t80->posLineaDerecha($tercero->nombre);

        if ($tercero->digito_verificacion) {
            $str .= $t80->posLineaDerecha("doc: ".$tercero->nombre.' - '.$tercero->digito_verificacion. ' - TEL: '.$sucursal->telefono);
        } else {
            $str .= $t80->posLineaDerecha("doc: ".$tercero->nombre. ' - TEL: '.$sucursal->telefono);
        }
        $str .= $t80->posLineaDerecha("direccion: ".$sucursal->direccion);
        $str .= $t80->posLineaGuion();

        $str.= $t80->posLineaDerecha('No. Fac   Valor Factura    Valor Abono     Saldo');
        $str .= $t80->posLineaGuion();

        foreach ($lineas as $linea) {
            $str .= str_pad($linea->consec_mov, 9, " ", STR_PAD_RIGHT);
            $str .= str_pad('$ '.number_format(intval($linea->valor_factura), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
            $str .= str_pad('$ '.number_format(intval($linea->valor), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
            $str .= str_pad('$ '.number_format(intval($linea->saldo_mov), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
        }

        $str .= $t80->posLineaGuion();
        $str .= $t80->posLineaBlanco();

        $totalPagos = 0;

        $str .= $t80->posLineaCentro('pagos', '-');

        foreach ($pagos as $pago) {
            if ($pago->valor > 0) {
                $str.= $t80->posDosItemsExtremos($pago->forma_nombre, '$ '.$t80->toNumber( $pago->valor));
                $totalPagos = intval($totalPagos) + intval($pago->valor);
            }
        }

        $str .= $t80->posLineaIzquierda('----------');
        $str.= $t80->posDosItemsExtremos('TOTAL PAGADO', '$ '.$t80->toNumber( $totalPagos));
        $str .= $t80->posLineaGuion();
        $str.= $t80->posFooterSgc();

        $printer->text($str);
        $printer->feed(1);
        $printer->cut();
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
