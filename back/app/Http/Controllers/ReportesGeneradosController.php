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
use Hamcrest\Type\IsString;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PHPJasper\PHPJasper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ReportesGeneradosController extends Controller
{

        public static function executeJasper ($input,$params ){

            $jdbc_dir = base_path().'\resources\jasper\sqldriver';
            $jdbc_url = null;
            $host = env('DB_HOST','.');
            $instance = explode('\\', $host);

            if (count($instance) > 1) {
                $jdbc_url = 'jdbc:jtds:sqlserver://localhost/'.env('DB_DATABASE','sgc').';instance='.$instance[1];
            } else {
                $jdbc_url = 'jdbc:jtds:sqlserver://localhost/'.env('DB_DATABASE','sgc');
            }

            $options = [
                'format' => ['pdf'],
                'locale' => 'es',
                'params' => $params,
                'db_connection' => [
                    'driver' => 'generic',
                    'host' => env('DB_HOST','.'),
                    'port' => '1433',
                    'database' => env('DB_DATABASE','sgc'),
                    'username' => env('DB_USERNAME','sa'),
                    'password' => env('DB_PASSWORD','sa'),
                    'jdbc_driver' => "net.sourceforge.jtds.jdbc.Driver",
                    'jdbc_url' => $jdbc_url,
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

        public function movimientosPorFechaCustomExcel($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {

            return Excel::download(new MovimientosExport($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id), 'invoices.xlsx');
        }

        public function getProductGroups(){
            $productGroups = ProdGrupo::all();
            return $productGroups;
        }

        public function compileJrXml(){
            $input = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples\MovimientosPorFecha.jrxml';
            $jasper = new PHPJasper;
            $jasper->compile($input)->execute();
        }

        public function reporteTiqueteFactura(){

            $params = $_GET;
            $input = 'TicketInvoice';
            self::executeJasper($input, $params);

        }

        public function saldosCartera(){

            $params = $_GET;
            $input = 'reportcxc';
            self::executeJasper($input, $params);

        }

        public function movimientosPorFecha(){
            // dd($_GET);
            $params = $_GET;
            // if((isset($params['fecha_inicial']) && isset($params['fecha_final']) && isset($params['tercero_id'])) && !isset($params['sucursal_id']) ){
            //     $input = 'MovimientosPorFechaTercero';
            // }else{
            $input = 'MovimientosPorFecha';
            // }
            self::executeJasper($input, $params);
        }

        public function movimientosPorFechaGrupo(){
            // dd($_GET);
            $params = $_GET;
            $input = 'DetallesMovimientosPorFecha';
            self::executeJasper($input, $params);
        }
    }
