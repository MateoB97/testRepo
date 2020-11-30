<?php

namespace App\Http\Controllers;

use App\Exports\MovimientosExport;
use Maatwebsite\Excel\Facades\Excel;

use Illuminate\Http\Request;
use App\User;
use App\FacMovimiento;
use App\FacPivotMovProducto;
use App\FacPivotMovSalmercancia;
use App\FacPivotFormaRecibo;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use App\FacMovRelacionado;
use App\FacPivotMovFormapago;
use App\FacTipoDoc;
use App\FacTipoRecCaja;
use App\Producto;
use App\SalMercancia;
use App\GenImpresora;
use App\GenMunicipio;
use App\GenDepartamento;
use App\FacDataFE;
use App\GenIva;
use App\GenVendedor;
use App\FacFormaPago;
use App\GenUnidades;
use App\FacCruce;
use App\GenEmpresa;
use App\TerceroSucursal;
use App\Tercero;
use App\GenCuadreCaja;
use App\FacPivotMovVendedor;
use App\Inventario;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use App\FacReciboCaja;
use App\FacPivotRecMov;
use App\ProdGrupo;
use App\ReportesGenerados;
use DNS2D;
use App\SoenacRegimen;
use App\SoenacResponsabilidad;
use App\SoenacTipoDocumento;
use App\SoenacTipoOrg;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ReportesGeneradosController extends Controller
{
        // public function movimientosPorFechaCustom($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {

        //     $fecha_inicial = $fecha_inicial == "null" ? null : $fecha_inicial;
        //     $fecha_final = $fecha_final == "null" ? null : $fecha_final;
        //     $tercero_id = $tercero_id == "null" ? null : $tercero_id;
        //     $sucursal_id = $sucursal_id == "null" ? null : $sucursal_id;

        //     $tiposdocs = FacTipoDoc::all();
        //     $tiposrec = FacTipoRecCaja::all();
        //     $movimientosTotal = array();

        //     $detallesventasporfecha = ReportesGenerados::reporteVentasPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);
        //     $detallesdevolporfecha = ReportesGenerados::reporteDevolucionesPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);
        //     $detallesrecibosporfecha = ReportesGenerados::reporteRecibosPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);

        //     $hoy = Carbon::now();
        //     if (($tercero_id != null) && ($sucursal_id == null || $sucursal_id == "null")) {
        //             $tercero = Tercero::find($tercero_id);
        //             $sucursales = collect();
        //             $datos = array();
        //             array_push($datos, $detallesventasporfecha, $detallesdevolporfecha, $detallesrecibosporfecha);
        //             foreach($datos as $dato){
        //                 foreach($dato->unique('sucursal_id')->sortBy('sucursal_id') as $tercero){
        //                     $sucursales->push($tercero);
        //                 }
        //             }

        //             $data = ['fechaIni' => $fecha_inicial,
        //                 'fechaFin' => $fecha_final,
        //                 'detallesventasporfecha' => $detallesventasporfecha,
        //                 'detallesdevolporfecha' => $detallesdevolporfecha,
        //                 'detallesrecibosporfecha' => $detallesrecibosporfecha,
        //                 'hoy' => $hoy,
        //                 'tercero' => $tercero,
        //                 'sucursales' => $sucursales->unique('sucursal_id'),
        //                 'tiposdocs' => $tiposdocs,
        //                 'tiposrec' => $tiposrec
        //             ];

        //             $pdf = PDF::loadView('facturacion.test', $data)->setPaper('a4','landscape');
        //             return $pdf->stream();

        //         } else {
        //             foreach ($tiposdocs as $tipo) {
        //                 $totalTipo = $detallesventasporfecha->where('tipoid', $tipo->id)->sum('total');
        //                 $subtotalTipo = $detallesventasporfecha->where('tipoid', $tipo->id)->sum('subtotal');
        //                 $saldoTipo = $detallesventasporfecha->where('tipoid', $tipo->id)->sum('saldo');
        //                 $aboTipo = ($detallesventasporfecha->where('tipoid', $tipo->id)->sum('total') - $detallesventasporfecha->where('tipoid', $tipo->id)->sum('saldo')  );
        //                 if (count($detallesventasporfecha->where('tipoid', $tipo->id)) > 0) {
        //                     array_push($movimientosTotal, [$tipo->nombre, $detallesventasporfecha->where('tipoid',$tipo->id), $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
        //                 }
        //             }
        //             foreach ($tiposdocs as $tipo) {
        //                 $totalTipo = $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('total');
        //                 $subtotalTipo = $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('subtotal');
        //                 $saldoTipo = $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('saldo');
        //                 $aboTipo = ($detallesdevolporfecha->where('tipoid', $tipo->id)->sum('total') - $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('saldo')  );
        //                 if (count($detallesdevolporfecha->where('tipoid',$tipo->id)) > 0) {
        //                     array_push($movimientosTotal, ['Devolucion '.$tipo->nombre, $detallesdevolporfecha->where('tipoid',$tipo->id), $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
        //                 }
        //             }
        //             foreach ($tiposrec as $tipo) {
        //                 $totalTipo = $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('total');
        //                 $subtotalTipo = $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('subtotal');
        //                 $saldoTipo = $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('saldo');
        //                 $aboTipo = ($detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('total') - $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('saldo')  );
        //                 if (count($detallesrecibosporfecha->where('tipoid',$tipo->id)) > 0) {
        //                     array_push($movimientosTotal, [$tipo->nombre, $detallesrecibosporfecha->where('tipoid',$tipo->id), $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
        //                 }
        //             }
        //             $data = ['movimientosTotal' => $movimientosTotal,
        //             'fechaIni' => $fecha_inicial,
        //             'fechaFin' => $fecha_final,
        //             'hoy' => $hoy ];

        //             // $pdf = PDF::loadView('facturacion.movimientos', $data)->setPaper('a4','landscape');
        //             // return $pdf->stream();

        //             $fp = fopen('D:/TX.csv','w+');

        //             foreach ($movimientosTotal as $movimiento) {

        //                 fwrite($fp, $movimiento[0].PHP_EOL);

        //                 foreach ($movimiento[1] as $mov) {

        //                     $linea = $mov->consecutivo.','.$mov->documento.','.$mov->tercero.','.$mov->subtotal.','.$mov->total.','.$mov->saldo;

        //                     fwrite($fp, $linea.PHP_EOL);
        //                 }
                        
        //             }

        //             fclose($fp);

        //             return 'done';
        //     }
        // }

        public function movimientosPorFechaCustom($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {

            return Excel::download(new MovimientosExport($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id), 'invoices.xlsx');
        }


        public function saldosCarteraCxCCustom($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id){

            $fecha_inicial = $fecha_inicial == "null" ? null : $fecha_inicial;
            $fecha_final = $fecha_final == "null" ? null : $fecha_final;
            $tercero_id = $tercero_id == "null" ? null : $tercero_id;
            $sucursal_id = $sucursal_id == "null" ? null : $sucursal_id;
            $lineas = ReportesGenerados::reporteSaldosEnCartera($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);

            $fecha = Carbon::now();
            $data = ['lineas' => $lineas, 'fecha' => $fecha];
            // ini_set('memory_limit', '-1');
            $pdf = PDF::loadView('facturacion.saldocarteraclientes', $data);

        return $pdf->stream();
        }

        public function movsFechaGrupoTipo(){
            $fecha_inicial = '2020-10-01';
            $fecha_final = '2020-10-05';
            $sucursal_id  = 4;
            $group_id = null;
            $tipodoc = null;
            $datos = ReportesGenerados::reporteVentasPorFechaPorGrupoPorTipoCustom($fecha_inicial, $fecha_final, $sucursal_id, $group_id, $tipodoc);
            return $datos;
        }

        public function getProductGroups(){
            $productGroups = ProdGrupo::all();
            return $productGroups;
        }
    }
