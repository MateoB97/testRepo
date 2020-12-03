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

        public function compileJrXml(){
            $input = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples\TicketInvoice.jrxml';
            $jasper = new PHPJasper;
            $jasper->compile($input)->execute();
        }

        public function reporteTiqueteFactura($fecha_inicial){
            // dd(input);
            // $fecha_inicial = '2020-09-08';
            $input = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples\TicketInvoice.jasper';
            $output = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples';
            $jdbc_dir = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\bin\jasperstarter\jdbc';
            $options = [
                'format' => ['pdf'],
                'locale' => 'en',
                'params' => ['fecha_inicial' =>$fecha_inicial],
                'db_connection' => [
                    'driver' => 'generic',
                    'host' => 'localhost',
                    'port' => '1433',
                    'database' => 'SgcSevilla',
                    'username' => 'sa',
                    'password' => 'Admin1036',
                    'jdbc_driver' => "net.sourceforge.jtds.jdbc.Driver",
                    'jdbc_url' => 'jdbc:jtds:sqlserver://localhost/SgcSevilla',
                    'jdbc_dir' => $jdbc_dir
                ]
            ];
            $jasper = new PHPJasper;
            $jasper->process(
                $input,
                $output,
                $options
            )->execute();
            // sleep(1);
            shell_exec('start "" /max "C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples\TicketInvoice.pdf"');
        }
    }
