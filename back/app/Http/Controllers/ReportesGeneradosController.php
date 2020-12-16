<?php

namespace App\Http\Controllers;

require '../vendor/autoload.php';

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
use PHPJasper\PHPJasper;

class ReportesGeneradosController extends Controller
{

        public static function executeJasper ($input,$params ){
            
            $jdbc_dir = base_path().'\resources\jasper\sqldriver';

            $host = env('DB_HOST','.');
            $instance = explode('\\', $host);

            if (count($instance) > 1) {
                $jdbc_url = 'jdbc:jtds:sqlserver://localhost/'.env('DB_DATABASE','sgc').';instance='.$instance[1];
            } else {
                $jdbc_url = 'jdbc:jtds:sqlserver://localhost/'.env('DB_DATABASE','sgc');
            }

            $options = [
                'format' => ['pdf'],
                'locale' => 'en',
                'params' => $params,
                'db_connection' => [
                    'driver' => 'generic',
                    'host' => env('DB_HOST','.'),
                    'port' => '1433',
                    'database' => env('DB_DATABASE','sgc'),
                    'username' => env('DB_USERNAME','sa'),
                    'password' => env('DB_PASSWORD','sa'),
                    'jdbc_driver' => "net.sourceforge.jtds.jdbc.Driver",
                    'jdbc_url' => $jdbc_url;
                    'jdbc_dir' => $jdbc_dir
                ]
            ];

            $input = base_path().'\resources\jasper\templates\dist\\'.$input.'.jasper';
            $output = base_path().'\resources\jasper\templates\dist\\'.microtime();
            
            $jasper = new PHPJasper;
            $jasper->process(
                $input,
                $output,
                $options
            )->execute();

            header("Content-type: application/pdf");
            $od = readfile($output.'.'.$options['format'][0]);
            unlink($output.'.'.$options['format'][0]);
            dd($od);
        }

        // public static function buildJasperOptions ($keys,$paramsGet){

        //     $params = [];

        //     foreach ($paramsGet as $index => $value) {
        //         if ($value == 'null') {
        //             $value = null;
        //         }

        //         if ($value != null) {
        //             $params[$keys[$index]] = $value;
        //         }
        //     }
            
        //     $jdbc_dir = base_path().'\resources\jasper\sqldriver';

        //     $host = env('DB_HOST','.');
        //     $instance = explode('\\', $host)[1];

        //     $jasperOptions = [
        //         'format' => ['pdf'],
        //         'locale' => 'en',
        //         'params' => $params,
        //         'db_connection' => [
        //             'driver' => 'generic',
        //             'host' => env('DB_HOST','.'),
        //             'port' => '1433',
        //             'database' => env('DB_DATABASE','sgc'),
        //             'username' => env('DB_USERNAME','sa'),
        //             'password' => env('DB_PASSWORD','sa'),
        //             'jdbc_driver' => "net.sourceforge.jtds.jdbc.Driver",
        //             'jdbc_url' => 'jdbc:jtds:sqlserver://localhost/'.env('DB_DATABASE','sgc').';instance='.$instance,
        //             'jdbc_dir' => $jdbc_dir
        //         ]
        //     ];

        //     return $jasperOptions;
        // }

        public function movimientosPorFechaCustomExcel($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {

            return Excel::download(new MovimientosExport($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id), 'invoices.xlsx');
        }

        public function movimientosPorFechaCustom($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {

            $fecha_inicial = $fecha_inicial == "null" ? null : $fecha_inicial;
            $fecha_final = $fecha_final == "null" ? null : $fecha_final;
            $tercero_id = $tercero_id == "null" ? null : $tercero_id;
            $sucursal_id = $sucursal_id == "null" ? null : $sucursal_id;

            $tiposdocs = FacTipoDoc::all();
            $tiposrec = FacTipoRecCaja::all();
            $movimientosTotal = array();

            $detallesventasporfecha = ReportesGenerados::reporteVentasPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);
            $detallesdevolporfecha = ReportesGenerados::reporteDevolucionesPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);
            $detallesrecibosporfecha = ReportesGenerados::reporteRecibosPorFecha($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);

            $hoy = Carbon::now();
            if (($tercero_id != null) && ($sucursal_id == null || $sucursal_id == "null")) {
                    $tercero = Tercero::find($tercero_id);
                    $sucursales = collect();
                    $datos = array();
                    array_push($datos, $detallesventasporfecha, $detallesdevolporfecha, $detallesrecibosporfecha);
                    foreach($datos as $dato){
                        foreach($dato->unique('sucursal_id')->sortBy('sucursal_id') as $tercero){
                            $sucursales->push($tercero);
                        }
                    }

                    $data = ['fechaIni' => $fecha_inicial,
                        'fechaFin' => $fecha_final,
                        'detallesventasporfecha' => $detallesventasporfecha,
                        'detallesdevolporfecha' => $detallesdevolporfecha,
                        'detallesrecibosporfecha' => $detallesrecibosporfecha,
                        'hoy' => $hoy,
                        'tercero' => $tercero,
                        'sucursales' => $sucursales->unique('sucursal_id'),
                        'tiposdocs' => $tiposdocs,
                        'tiposrec' => $tiposrec
                    ];

                    $pdf = PDF::loadView('facturacion.movimientosporfechatercero', $data)->setPaper('a4','landscape');
                    return $pdf->stream();

                } else {
                    foreach ($tiposdocs as $tipo) {
                        $totalTipo = $detallesventasporfecha->where('tipoid', $tipo->id)->sum('total');
                        $subtotalTipo = $detallesventasporfecha->where('tipoid', $tipo->id)->sum('subtotal');
                        $saldoTipo = $detallesventasporfecha->where('tipoid', $tipo->id)->sum('saldo');
                        $aboTipo = ($detallesventasporfecha->where('tipoid', $tipo->id)->sum('total') - $detallesventasporfecha->where('tipoid', $tipo->id)->sum('saldo')  );
                        if (count($detallesventasporfecha->where('tipoid', $tipo->id)) > 0) {
                            array_push($movimientosTotal, [$tipo->nombre, $detallesventasporfecha->where('tipoid',$tipo->id), $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
                        }
                    }
                    foreach ($tiposdocs as $tipo) {
                        $totalTipo = $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('total');
                        $subtotalTipo = $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('subtotal');
                        $saldoTipo = $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('saldo');
                        $aboTipo = ($detallesdevolporfecha->where('tipoid', $tipo->id)->sum('total') - $detallesdevolporfecha->where('tipoid', $tipo->id)->sum('saldo')  );
                        if (count($detallesdevolporfecha->where('tipoid',$tipo->id)) > 0) {
                            array_push($movimientosTotal, ['Devolucion '.$tipo->nombre, $detallesdevolporfecha->where('tipoid',$tipo->id), $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
                        }
                    }
                    foreach ($tiposrec as $tipo) {
                        $totalTipo = $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('total');
                        $subtotalTipo = $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('subtotal');
                        $saldoTipo = $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('saldo');
                        $aboTipo = ($detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('total') - $detallesrecibosporfecha->where('tipoid', $tipo->id)->sum('saldo')  );
                        if (count($detallesrecibosporfecha->where('tipoid',$tipo->id)) > 0) {
                            array_push($movimientosTotal, [$tipo->nombre, $detallesrecibosporfecha->where('tipoid',$tipo->id), $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
                        }
                    }
                    $data = ['movimientosTotal' => $movimientosTotal,
                    'fechaIni' => $fecha_inicial,
                    'fechaFin' => $fecha_final,
                    'hoy' => $hoy ];
                    $pdf = PDF::loadView('facturacion.movimientos', $data)->setPaper('a4','landscape');
                    return $pdf->stream();
            }
        }

        // public function saldosCarteraCxCCustom($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id){

        //     $fecha_inicial = $fecha_inicial == "null" ? null : $fecha_inicial;
        //     $fecha_final = $fecha_final == "null" ? null : $fecha_final;
        //     $tercero_id = $tercero_id == "null" ? null : $tercero_id;
        //     $sucursal_id = $sucursal_id == "null" ? null : $sucursal_id;
        //     $lineas = ReportesGenerados::reporteSaldosEnCartera($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id);

        //     $fecha = Carbon::now();
        //     $data = ['lineas' => $lineas, 'fecha' => $fecha];
        //     // ini_set('memory_limit', '-1');
        //     $pdf = PDF::loadView('facturacion.saldocarteraclientes', $data);

        // return $pdf->stream();
        // }

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

        public function compileJrXml(){
            $input = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples\reportcxc.jrxml';
            $jasper = new PHPJasper;
            $jasper->compile($input)->execute();
        }

        public function reporteTiqueteFactura(){

            $params = $_GET;
            $input = 'TicketInvoice';
            self::executeJasper($input, $params);

        }

        public function saldosCarteraCxCCustom(){

            $params = $_GET;
            $input = 'reportcxc';
            self::executeJasper($input, $params);

        }

        public function movimientosPorFechaJasper($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id, $tipo_documento){

            $fileName =  str_replace(':','-', 'MovsPorFecha-'.date("Y-m-d H:i:s"));


            $fecha_inicial = $fecha_inicial == "null" ? null : $fecha_inicial;
            $fecha_final = $fecha_final == "null" ? null : $fecha_final;
            $tercero_id = $tercero_id == "null" ? null : $tercero_id;
            $sucursal_id = $sucursal_id == "null" ? null : $sucursal_id;

            $params = [];
            if($fecha_inicial != null){
                $params['fecha_inicial'] = $fecha_inicial;
            }
            if($fecha_final != null){
                $params['fecha_final'] = $fecha_final;
            }
            if($tercero_id != null){
                $params['tercero_id'] = $tercero_id;
            }
            if($sucursal_id != null){
                $params['sucursal_id'] = $sucursal_id;
            }
            if($tipo_documento != null){
                $params['tipo_documento'] = $tipo_documento;
            }

        }

        public function testArray(){
            $data = array();
            $number = 111;
            $vowel = 'a';
            $data['Numero'] = $number;
            $data['Letra'] = $vowel;
            dd($data);
        }
    }
