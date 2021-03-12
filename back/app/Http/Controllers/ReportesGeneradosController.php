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
use App\GenPivotCuadreFormapago;
use App\GenPivotCuadreTiposdoc;
use App\SalPivotInventSalida;
use App\SalPivotSalProducto;
use App\LotProgramacion;

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
            $input = 'C:\xampp\htdocs\sgc\back\vendor\geekcom\phpjasper-laravel\examples\reportcxctraslado.jrxml';
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

        public function saldosCarteraTR(){

            $params = $_GET;
            $input = 'reportcxctraslado';
            self::executeJasper($input, $params);
        }

        public function movimientosPorFecha(){
            $params = $_GET;
            $input = 'MovimientosPorFecha';
            self::executeJasper($input, $params);
        }

        public function movimientosPorFechaPorDia(){
            $params = $_GET;
            $input = 'MovimientosPorFechaPorDia';
            self::executeJasper($input, $params);
        }

        public function movimientosPorFechaGrupo(){
            // dd($_GET);
            $params = $_GET;
            $input = 'DetallesMovimientosPorFecha';
            self::executeJasper($input, $params);
        }

        public function movimientosPorProducto(){
            // dd($_GET);
            $params = $_GET;
            $input = 'MovimientosPorProducto';
            self::executeJasper($input, $params);
        }

        public function vistaInterfazContadoras ($fecha_ini, $fecha_fin) {

            $fecha_ini = '01/11/2020';
            $fecha_fin = '30/11/2020';
            $fac_tipo_doc_id = 14;

            $data = ReportesGenerados::reporteFacturasIva($fecha_ini,$fecha_fin,$fac_tipo_doc_id);

            $dataFormated = array();

            $lineFormated = array();

            $consecAnterior = 0;
            $totalConsec = 0;

            $ivaConsec = array();

            $fp = fopen('C:\tiquetes\reporte.csv', 'wb');

            foreach ($data as $k => $line) {

                if ($consecAnterior != $line->consecutivo && $k != 0)  {

                    foreach ($ivaConsec as $j => $iva) {

                        $lineFormated['cuenta'] = $j;
                        $lineFormated['comprobante'] = '';
                        $lineFormated['fecha'] = $data[$k-1]->fecha_facturacion;
                        $lineFormated['documento'] = '00000001';
                        $lineFormated['documento_relacionado'] =  $data[$k-1]->prefijo.$data[$k-1]->consecutivo;
                        $lineFormated['nit'] = $data[$k-1]->nit;
                        $lineFormated['detalle'] = $iva['nombre'];
                        $lineFormated['tipo'] = 2;
                        $lineFormated['valor'] = intval($iva['valor']);
                        $lineFormated['base'] = intval($iva['base']);
                        $lineFormated['centro_costos'] = '';
                        $lineFormated['trans_e'] = '';
                        $lineFormated['plazo'] = '0';

                        fputcsv($fp, $lineFormated);
                    }

                    $ivaConsec = array();

                    $lineFormated['cuenta'] = '11050505';
                    $lineFormated['comprobante'] = '';
                    $lineFormated['fecha'] = $data[$k-1]->fecha_facturacion;
                    $lineFormated['documento'] = '00000001';
                    $lineFormated['documento_relacionado'] = $data[$k-1]->prefijo.$data[$k-1]->consecutivo;
                    $lineFormated['nit'] = $data[$k-1]->nit;
                    $lineFormated['detalle'] = 'CAJA';
                    $lineFormated['tipo'] = 1;
                    $lineFormated['valor'] = intval($totalConsec);
                    $lineFormated['base'] = '0';
                    $lineFormated['centro_costos'] = '';
                    $lineFormated['trans_e'] = '';
                    $lineFormated['plazo'] = '0';

                    fputcsv($fp, $lineFormated);

                    $totalConsec = 0;


                }

                $consecAnterior = $line->consecutivo;

                $lineFormated['cuenta'] = $line->cuenta_contable_venta;
                $lineFormated['comprobante'] = '';
                $lineFormated['fecha'] = $line->fecha_facturacion;
                $lineFormated['documento'] = '00000001';
                $lineFormated['documento_relacionado'] = $line->prefijo.$line->consecutivo;
                $lineFormated['nit'] = $line->nit;
                $lineFormated['detalle'] = $line->nombre_contable_venta;
                $lineFormated['tipo'] = 2;
                $lineFormated['valor'] = intval($line->sub);
                $lineFormated['base'] = 0;
                $lineFormated['centro_costos'] = '';
                $lineFormated['trans_e'] = '';
                $lineFormated['plazo'] = '0';

                if ($line->cuenta_contable_iva){

                    if (isset($ivaConsec[$line->cuenta_contable_iva])) {
                        $ivaConsec[$line->cuenta_contable_iva]['valor'] += intval($line->iva);
                        $ivaConsec[$line->cuenta_contable_iva]['base'] += intval($line->sub);
                    } else {
                        $ivaConsec[$line->cuenta_contable_iva]['valor'] = intval($line->iva);
                        $ivaConsec[$line->cuenta_contable_iva]['nombre'] = $line->nombre_contable_iva;
                        $ivaConsec[$line->cuenta_contable_iva]['base'] = intval($line->sub);
                    }

                }

                $totalConsec += $line->total;

                fputcsv($fp, $lineFormated);

            }

            fclose($fp);
            dd($dataFormated);
        }

    }
