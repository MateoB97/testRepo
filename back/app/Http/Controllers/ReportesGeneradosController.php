<?php

namespace App\Http\Controllers;

require '../vendor/autoload.php';

use App\Exports\MovimientosExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CierreInventarioVariacion;
use App\Exports\CierreInventarioPesadas;
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
use App\ReportesT80;
use App\LotProgramacion;
use Illuminate\Support\Collection;
use App\LotPesosProgramacion;
use App\ProductoTerminado;
use App\UserPermisos;
use App\GenCuadreIngresoEfectivo;
use App\EgreEgreso;
use App\EgreTipoEgreso;
use App\Lote;
use App\InvCierreInventario;
use App\ComComproEgreso;
use App\ComTipoComproEgreso;
use App\ComPivotCompraEgreso;
use App\ComPivotFormaEgreso;
use App\ComCompra;
use App\SoenacPivotCorrectionFacMovConcepts;
use App\Exports\ReporteFiscal;

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

    public static function movimientosPorFechaCustomExcel($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id) {

        return Excel::download(new MovimientosExport($fecha_inicial, $fecha_final, $tercero_id, $sucursal_id), 'invoices.xlsx');
    }

    public function getProductGroups(){
        $productGroups = ProdGrupo::all();
        return $productGroups;
    }

    public function compileJrXml(){
        $input = 'C:\xampp\htdocs\sgcdev\back\vendor\geekcom\phpjasper-laravel\examples\ComprasPorFechaXSucursal.jrxml';
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

    public function comprasPorFecha(){
        $params = $_GET;
        $input = 'ComprasPorFecha';
        self::executeJasper($input, $params);
    }

    public function comprasDetails(){
        $params = $_GET;
        $input = 'comprasDetails';
        self::executeJasper($input, $params);
    }

    public function comprasFormaPago(){
        $params = $_GET;
        $input = 'comprasFormaPago';
        self::executeJasper($input, $params);
    }
    public function comprasPorFechaPesos(){
        $params = $_GET;
        $input = 'comprasPorFechaPesos';
        self::executeJasper($input, $params);
    }
    public function comprasPorFechaPesosDEVS(){
        $params = $_GET;
        $input = 'comprasPorFechaPesosDEVS';
        self::executeJasper($input, $params);
    }
    public function comprasPorFechaXSucursal(){
        $params = $_GET;
        $input = 'comprasPorFechaXSucursal';
        self::executeJasper($input, $params);
    }
    public function comprasXProducto(){
        $params = $_GET;
        $input = 'comprasXProducto';
        self::executeJasper($input, $params);
    }
    public function InventarioPesosDespacho(){
        $params = $_GET;
        $input = 'InventarioPesosDespacho';
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

    public function movimientosPorFechaPorSucursal(){
        $params = $_GET;
        $input = 'MovimientosPorFechaSucursales';
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

    public function pesoPlantaLote(){
        $params = $_GET;
        $input = 'PesoPlantaxLote';
        self::executeJasper($input, $params);
    }

    public function pesosTotalesProductos(){
        $params = $_GET;
        $input = 'ventasPesosTotales';
        self::executeJasper($input, $params);
    }

    public function pesosTotalesDevolProductos(){
        $params = $_GET;
        $input = 'devolucionesVentasPesosTotales';
        self::executeJasper($input, $params);
    }

    public function movimientoFormaPagoPorFecha(){
        $params = $_GET;
        $input = 'FormasPagoMovsPorFecha';
        self::executeJasper($input, $params);
    }

    public static function vistaInterfazContadoras () {

        $fecha_ini = $_GET['fecha_inicial'];
        $fecha_fin = $_GET['fecha_final'];

        $data = ReportesGenerados::reporteFacturasIva($fecha_ini,$fecha_fin);

        $dataFormated = array();

        $lineFormated = array();

        $consecAnterior = 0;
        $totalConsec = 0;

        $ivaConsec = array();

        $fp = fopen('C:\tiquetes\reporte-'.$fecha_ini.' - '.$fecha_fin.'.csv', 'wb');

        foreach ($data as $k => $line) {
            // cambio de consecutivo
            if ($consecAnterior != $line->consecutivo && $k != 0)  {

                // cuando se encuentra que se va a cambiar de consecutivo
                // se imprimen los ivas del consecutivo anterior
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

                // reiniciar ivas
                $ivaConsec = array();

                // imprimir contraparte de caja
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

            // set consec actual como consec anterior para validacion
            $consecAnterior = $line->consecutivo;

            // print linea de factura
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

            // si cuenta contable
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
        $size = count($data);
        $k = $size -1;
        // imprimir contraparte de caja
        $lineFormated['cuenta'] = '11050505';
        $lineFormated['comprobante'] = '';
        $lineFormated['fecha'] = $data[$size - 1]->fecha_facturacion;
        $lineFormated['documento'] = '00000001';
        $lineFormated['documento_relacionado'] = $data[$size - 1]->prefijo.$data[$size - 1]->consecutivo;
        $lineFormated['nit'] = $data[$size - 1]->nit;
        $lineFormated['detalle'] = 'CAJA';
        $lineFormated['tipo'] = 1;
        $lineFormated['valor'] = intval($totalConsec);
        $lineFormated['base'] = '0';
        $lineFormated['centro_costos'] = '';
        $lineFormated['trans_e'] = '';
        $lineFormated['plazo'] = '0';

        fputcsv($fp, $lineFormated);

        fclose($fp);
        dd($dataFormated);
    }

    public function saldosEnCarteraT80()
    {
        // $user = User::find(Auth::user())->first();
        // $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        //Usuario de prueba
        $user = User::find(1);
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(1)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $printer -> setFont(1);
        $printer->feed(1);
        $caracLinea = caracteres_linea_posT80FontOne();

        $empresa = GenEmpresa::find(1);

        $etiqueta  ='';// pad('', 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->razon_social), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->nombre),0,' ');
        $etiqueta .= pad("NIT: ".$empresa->nit, $caracLinea, 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->tipo_regimen), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->direccion), 0, ' ');
        $etiqueta .= pad("TEL: ".$empresa->telefono, 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $etiqueta .= pad('CUENTAS POR COBRAR', 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $hoy = date("m.d.y");
        $time = date('H:i');
        $etiqueta  .= pad('Fecha Impresion:='. $hoy .' - '.$time , 1, ' ');
        $etiqueta  .= pad('', 0, ' ');

        $granTotal = 0;
        $granSaldo = 0;
        $granAbono = 0;
        $row ='';
        $lineas = FacMovimiento::saldosEnCartera();
        $clientes  = $lineas->unique('documento')->sortBy('tercero');
        $sucursales = $lineas->unique('sucursal_id')->sortBy('nombre');
            foreach($clientes as $cliente){//ClientByClient
                $etiqueta .= pad($cliente->documento.'    '.$cliente->tercero .'  Tel: '. $cliente->telefono, 0, ' ');
                    foreach($sucursales->where('documento',$cliente->documento) as $sucursal){//BranchOfficeByBranchOffice
                        $etiqueta .= pad($sucursal->sucursal.'    '.$sucursal->telefono, 1, ' ');
                            foreach($lineas->where('sucursal',$sucursal->sucursal)->where('documento', $sucursal->documento) as $linea){//DetailsByBranchOffice
                                $row = str_pad(strval($linea->consecutivo),9, ' ');
                                $row .= str_pad($linea->fecha_facturacion,12, ' ');
                                $row .= str_pad('$'.strval(number_format($linea->total, 0, ',', '.')),15, ' ');
                                $row .= str_pad('$'.strval(number_format(($linea->total - $linea->saldo), 0, ',', '.')),14, ' ');
                                $row .= str_pad('$'.strval(number_format($linea->saldo, 0, ',', '.')),15, ' ');
                                // $etiqueta .= pad ($linea->consecutivo   .'   '.$linea->fecha_facturacion  .'   $ '.number_format($linea->total, 0, ',', '.'). '   $ '. number_format(($linea->total - $linea->saldo), 0, ',', '.'). '   $ '. number_format($linea->saldo, 0, ',', '.'), 1, ' ');
                                $etiqueta .= pad ($row, 1, ' ');
                                $row = '';
                                // $etiqueta .= pad('Total: $ '.number_format($linea->total, 0, ',', '.'). '      Abono: $ '. number_format(($linea->total - $linea->saldo), 0, ',', '.'). '     Saldo: $ '. number_format($linea->saldo, 0, ',', '.'), 1, ' ');
                            }
                        $sucursalTotal = $lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('total') - ($lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('total') - $lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('saldo'));
                        $etiqueta .= pad('Total Sucursal: $ ' . number_format($sucursalTotal, 0, ',', '.'),1,' ');
                        // $etiqueta .= pad('Total Sucursal: ' . number_format($lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('saldo'), 0, ',', '.'),1,' ');
                        $etiqueta  .= pad('', 0, '.');
                    }
                    $totalCliente = $lineas->where('documento',$cliente->documento)->sum('total') - ($lineas->where('documento',$cliente->documento)->sum('total') - $lineas->where('documento',$cliente->documento)->sum('saldo'));
                    $etiqueta .= pad('Total: $ ' . number_format($lineas->where('documento',$cliente->documento)->sum('saldo'), 0, ',', '.'),1,' ');
                    $etiqueta  .= pad('', 0, '-');
            }
        //Totales
        $granTotal = number_format($lineas->sum('total'), 0, ',', '.');
        $granSaldo = number_format($lineas->sum('saldo'), 0, ',', '.');
        $granAbono = number_format($lineas->sum('total') - $lineas->sum('saldo'), 0, ',', '.');
        $etiqueta  .= pad('', 0, '=');
        $etiqueta .= pad("Total Cartera: $ $granTotal", 1, ' ');
        $etiqueta .= pad("Total Abonado: $ $granAbono", 1, ' ');
        $etiqueta .= pad("Total Saldo: $ $granSaldo", 1, ' ');

        $printer->text($etiqueta);
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function movimientosPorFechaT80($fechaIni, $fechaFin){

        $user = User::find(1);
        // $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        // // $fechaIni= date('Y-m-d');
        // // $fechaFin = date('Y-m-d');

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $printer -> setFont(1);
        $printer->feed(1);
        $caracLinea = caracteres_linea_posT80FontOne();
        $empresa = GenEmpresa::find(1);
        $etiqueta  ='';// pad('', 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->razon_social), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->nombre),0,' ');
        $etiqueta .= pad("NIT: ".$empresa->nit, $caracLinea, 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->tipo_regimen), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->direccion), 0, ' ');
        $etiqueta .= pad("TEL: ".$empresa->telefono, 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $etiqueta .= pad('VENTAS NETAS POR FECHA', 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $hoy = date("m.d.y");
        $time = date('H:i');
        $etiqueta  .= pad('Fecha Impresion:='. $hoy .' - '.$time , 1, ' ');
        $etiqueta  .= pad('', 0, ' ');

        $row = '';
        $granTotal = 0;
        $granAbono =0 ;
        $granSaldo = 0;

        $documentTypes = FacTipoDoc::get();
        $salesDetails = FacMovimiento::ventasPorFechaT80($fechaIni, $fechaFin);
        $devolutionDetails = FacMovimiento::DevolucionesPorFechaT80($fechaIni, $fechaFin);
        $receiptTypes = FacTipoRecCaja::all();
        $receiptDetails = FacReciboCaja::RecibosPorFechaT80($fechaIni, $fechaFin);

        $granTotal = $salesDetails->sum('total') + $devolutionDetails->sum('total') +  $receiptDetails->sum('total');
        $granSaldo = $salesDetails->sum('saldo') + $devolutionDetails->sum('saldo') +  $receiptDetails->sum('saldo');
        $granAbono = ($salesDetails->sum('total') - $salesDetails->sum('saldo')) + ($devolutionDetails->sum('total') - $devolutionDetails->sum('saldo')) + ($receiptDetails->sum('total') - $receiptDetails->sum('saldo'));

        foreach($documentTypes as $documentType){//Ventas

            if($salesDetails->where('tipo',$documentType->nombre)->count()>0){

                $etiqueta .= pad(eliminar_acentos(eliminar_acentos($documentType->nombre)) ,0, ' ');
                    foreach($salesDetails->where('tipo',$documentType->nombre) as $saleDetail){

                        $row = str_pad($saleDetail->consecutivo,11,' ');
                        $row .= str_pad($saleDetail->documento,12,' ');
                        $row .= str_pad(substr(eliminar_acentos($saleDetail->tercero),0,strlen($saleDetail->tercero)),41,' ');
                        $etiqueta .= pad($row, 1, ' ');
                        $row = str_pad($saleDetail->fecha,14,' ');
                        $row .=  str_pad('$ '.strval(number_format($saleDetail->total, 0, ',', '.')),12,' ');
                        $row .= str_pad('$ '.strval(number_format(($saleDetail->total - $saleDetail->saldo), 0, ',', '.')),12,' ');
                        $row .=  str_pad('$ '.strval(number_format($saleDetail->saldo, 0, ',', '.')),12,' ');
                        $etiqueta .= pad($row, 1, ' ');
                    }
                $etiqueta .= pad ('', 1, '-');
                $row = str_pad('$ '.strval(number_format($salesDetails->where('tipo',$documentType->nombre)->sum('total'), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format(($salesDetails->where('tipo',$documentType->nombre)->sum('total') - $salesDetails->where('tipo',$documentType->nombre)->sum('saldo')), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format($salesDetails->where('tipo',$documentType->nombre)->sum('saldo'), 0, ',', '.')),20,' ');
                $etiqueta .= pad ($row, 1, ' ');
                $etiqueta .= pad ('', 1, '=');
            }
        }
        foreach($documentTypes as $documentType){//Devoluciones
            if($devolutionDetails->where('tipo',$documentType->nombre)->count()){
                $etiqueta .= pad(eliminar_acentos(eliminar_acentos('Devolucion - '.$documentType->nombre)) ,0, ' ');
                    foreach($devolutionDetails->where('tipo',$documentType->nombre) as $devolutionDetail){
                        $row = str_pad($devolutionDetail->consecutivo,11,' ');
                        $row .= str_pad($devolutionDetail->documento,12,' ');
                        $row .= str_pad(substr(eliminar_acentos($devolutionDetail->tercero),0,strlen($devolutionDetail->tercero)),41,' ');
                        $etiqueta .= pad($row, 1, ' ');
                        $row = str_pad($devolutionDetail->fecha,14,' ');
                        $row .=  str_pad('$ '.strval(number_format($devolutionDetail->total, 0, ',', '.')),12,' ');
                        $row .= str_pad('$ '.strval(number_format(($devolutionDetail->total - $devolutionDetail->saldo), 0, ',', '.')),12,' ');
                        $row .=  str_pad('$ '.strval(number_format($devolutionDetail->saldo, 0, ',', '.')),12,' ');
                        $etiqueta .= pad($row, 1, ' ');
                    }
                $etiqueta .= pad ('', 1, '-');
                $row = str_pad('$ '.strval(number_format($devolutionDetails->where('tipo',$documentType->nombre)->sum('total'), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format(($devolutionDetails->where('tipo',$documentType->nombre)->sum('total') - $devolutionDetails->where('tipo',$documentType->nombre)->sum('saldo')), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format($devolutionDetails->where('tipo',$documentType->nombre)->sum('saldo'), 0, ',', '.')),20,' ');
                $etiqueta .= pad ($row, 1, ' ');
                $etiqueta .= pad ('', 1, '=');
            }
        }

        foreach($receiptTypes as $receiptType){//Recibos
            if($receiptDetails->where('tipo',$receiptType->nombre)->count()){
                $etiqueta .= pad(eliminar_acentos(eliminar_acentos($receiptType->nombre)) ,0, ' ');
                    foreach($receiptDetails->where('tipo',$receiptType->nombre) as $receiptDetail){
                        $row = str_pad($receiptDetail->consecutivo,11,' ');
                        $row .= str_pad($receiptDetail->documento,12,' ');
                        $row .= str_pad(substr(eliminar_acentos($receiptDetail->tercero),0,strlen($receiptDetail->tercero)),41,' ');
                        $etiqueta .= pad($row, 1, ' ');
                        $row = str_pad($receiptDetail->fecha,14,' ');
                        $row .=  str_pad('$ '.strval(number_format($receiptDetail->total, 0, ',', '.')),12,' ');
                        $row .= str_pad('$ '.strval(number_format(($receiptDetail->total - $receiptDetail->saldo), 0, ',', '.')),12,' ');
                        $row .=  str_pad('$ '.strval(number_format($receiptDetail->saldo, 0, ',', '.')),12,' ');
                        $etiqueta .= pad($row, 1, ' ');
                    }
                $etiqueta .= pad ('', 1, '-');
                $row = str_pad('$ '.strval(number_format($receiptDetails->where('tipo',$receiptType->nombre)->sum('total'), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format(($receiptDetails->where('tipo',$receiptType->nombre)->sum('total') - $receiptDetails->where('tipo',$receiptType->nombre)->sum('saldo')), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format($receiptDetails->where('tipo',$receiptType->nombre)->sum('saldo'), 0, ',', '.')),20,' ');
                $etiqueta .= pad ($row, 1, ' ');
                $etiqueta .= pad ('', 1, '=');
            }
        }

        $row = '';
        $row  .=  str_pad('$ '.strval(number_format($granTotal, 0, ',', '.')),20,' ');
        $row .= str_pad('$ '. strval(number_format(($granAbono), 0, ',', '.')),20,' ');
        $row .=  str_pad('$ '. strval(number_format($granSaldo, 0, ',', '.')),20,' ');
        $etiqueta .= pad($row, 1, ' ');
        $printer->text($etiqueta);
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function ventasNetasPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $tiposdoc = FacTipoDoc::whereIn('naturaleza', [1, 4])->get();

        $ventasTotales = array();

        $granSubtotal = 0;
        $granDescuento = 0;
        $granIva = 0;
        $granTotal =0;

        $granDevSubtotal = 0;
        $granDevDescuento = 0;
        $granDevIva = 0;
        $granDevTotal =0;

        foreach ($tiposdoc as $tipo) {
            $ventas = FacMovimiento::ventasNetasPorFecha($tipo->id, $fechaIni, $fechaFin)->first();
            $devoluciones = FacMovimiento::devolucionesVentasNetasPorFecha($tipo->id, $fechaIni, $fechaFin)->first();

            if ($ventas->total > 0 || $devoluciones->total > 0) {
                array_push($ventasTotales, [$tipo->nombre, $ventas, $devoluciones]);
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

        $data = ['ventasTotales' => $ventasTotales,
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

        $pdf = PDF::loadView('facturacion.ventasnetasporfecha', $data);

        return $pdf->stream();
    }

    public function recaudoPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $recaudoTotales = array();

        $formaspago = FacFormaPago::all();

        $totalPOS = 0;
        $totalRecibos = 0;

        foreach ($formaspago as $forma) {
            $sumatoriaRecibos = FacPivotFormaRecibo::recaudoPorFecha($forma->id, $fechaIni, $fechaFin)->first();
            $sumatoriaPOS = FacPivotMovFormapago::recaudoPorFecha($forma->id, $fechaIni, $fechaFin)->first();

            if ($sumatoriaRecibos->valor > 0 || $sumatoriaPOS->valor > 0){
                array_push($recaudoTotales, [$forma->nombre, $sumatoriaRecibos->valor, $sumatoriaPOS->valor]);
                $totalPOS += $sumatoriaPOS->valor;
                $totalRecibos += $sumatoriaRecibos->valor;
            }
        }

        $hoy = Carbon::now();

        $data = ['recaudoTotales' => $recaudoTotales,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'totalPOS' => $totalPOS,
                 'totalRecibos' => $totalRecibos,
                 'hoy' => $hoy, ];

        $pdf = PDF::loadView('facturacion.recaudosporfecha', $data);

        return $pdf->stream();
    }

    public function movimientosConFormaPagoPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $formaspago = FacFormaPago::all();

        $datosTotal = array();

        foreach ($formaspago as $forma) {

            $total = 0;
            $movimientos = FacPivotMovFormapago::movimientosConFormaPagoPorFecha($forma->id, $fechaIni, $fechaFin);
            $recibos = FacPivotFormaRecibo::movimientosConFormaPagoPorFecha($forma->id, $fechaIni, $fechaFin);

            $concatenated = $movimientos->concat($recibos);

            foreach ($concatenated as $movimiento) {
                $total = $total + $movimiento->valor;
            }

            if ($total > 0) {
                array_push($datosTotal, [$forma->nombre, $concatenated, $total]);
            }
        }

        $hoy = Carbon::now();


        $data = ['datosTotal' => $datosTotal,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'hoy' => $hoy ];

        $pdf = PDF::loadView('facturacion.movimientoconformadepagoporfecha', $data);

        return $pdf->stream();
    }

    // public static function pesoPorFechasVentasDevsNotas($fecha_ini, $fecha_fin){
        public static function pesoPorFechasVentasDevsNotas(){
        $params = $_GET;
        $fecha_ini = null;
        $fecha_inicial = null;
       foreach($params as $param){
        if($fecha_ini == null){
            $fecha_ini = $param;
        }else{
            $fecha_fin = $param;
        }
       }
        $empresa = GenEmpresa::find(1);
        $lineas = ReportesGenerados::pesoAcomuladoVentasNotasDevs($fecha_ini, $fecha_fin);
        $data = ['empresa'=>$empresa, 'lineas'=> $lineas, 'fecha_ini'=> $fecha_ini, 'fecha_fin'=> $fecha_fin, 'today'=>date('yyyy/m/d/m:h:s')];
        $pdf = PDF::loadView('facturacion.pesototalventasdevsnotas', $data);
        return $pdf->stream();
    }

    public static function reporteFiscalPos(){

        $fechaIni = $_GET['fecha_inicial'];
        // $fechaIni = '2021-05-17';
        $user = User::find(2);

        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $t80 = new ReportesT80();

        $str = '';

        $str .= $t80->posLineaCentro('COMPROBANTE INFORME DIARIO');

        $str .= $t80->posHeaderEmpresa();
        $str .= $t80->posLineaDerecha('Fecha Impresion: '.Carbon::now());
        $str .= $t80->posLineaDerecha('Fecha Inicial: '. $fechaIni);

        $str .= $t80->posLineaBlanco();

        $str .= $t80->posLineaCentro('OPERACIONES    VALOR   NRO');

        // VENTAS
        $str .= $t80->posLineaDerecha('VENTAS');

        $ventas = ReportesGenerados::ventasContadoCredito($fechaIni);
        $totalVentas = 0;

        foreach ($ventas as $tipoVenta) {
            $str .= $t80->multiItemsFromArray([
                ['- '.$tipoVenta->VentasTipoDoc, 0, ' ', 1],
                [$t80->toNumber($tipoVenta->total), 12, ' ', -1],
                [$tipoVenta->VentasMaxConsecutivo - $tipoVenta->VentasMinConsecutivo + 1, 12, ' ', -1]
            ]);

            $totalVentas += $tipoVenta->total;
        }

        // DEVOLUCIONES
        $devoluciones = ReportesGenerados::devolucionesContadoCredito($fechaIni);

        foreach ($devoluciones as $tipoDevol) {
            $str .= $t80->multiItemsFromArray([
                ['- '.$tipoDevol->DevTipoDoc.' Anuladas', 0, ' ', 1],
                [$t80->toNumber($tipoDevol->total), 12, ' ', -1],
                [0, 12, ' ', -1]
            ]);
        }

        $str .= $t80->multiItemsFromArray([
                ['Total Ventas', 0, ' ', 1],
                [$t80->toNumber($totalVentas), 12, ' ', -1],
                [' ', 12, ' ', -1]
            ]);

        $str .= $t80->posLineaBlanco();

        // MEDIOS DE PAGO
        $str .= $t80->posLineaDerecha('ABONOS CREDITO');

        $abonos = ReportesGenerados::recibosAbonosCreditos($fechaIni);

        foreach ($abonos as $abono) {
            $str .= $t80->multiItemsFromArray([
                ['- '.$abono->nombre, 0, ' ', 1],
                [$t80->toNumber($abono->Valor), 12, ' ', -1],
                [' ', 12, ' ', -1]
            ]);
        }

        $str .= $t80->posLineaBlanco();

        // MEDIOS DE PAGO
        $str .= $t80->posLineaDerecha('MEDIOS DE PAGO');

        $mediosPago = ReportesGenerados::efectivosRecibos($fechaIni);

        foreach ($mediosPago as $medioPago) {
            $str .= $t80->multiItemsFromArray([
                ['- '.$medioPago->nombre, 0, ' ', 1],
                [$t80->toNumber($medioPago->Valor), 12, ' ', -1],
                [' ', 12, ' ', -1]
            ]);
        }

        $str .= $t80->posLineaBlanco();

        // CONSECUTIVOS
        $str .= $t80->posLineaDerecha('CONSECUTIVOS');
        // $str .= $t80->posLineaCentro('INICIAL               FINAL');
        $str .= $t80->multiItemsFromArray([
            ['INICIAL', 0, ' ', 1],
            ['FINAL', 24, ' ', 1]
        ]);

        $str .= $t80->posLineaDerecha('- Facturas');

        foreach ($ventas as $tipoVenta) {
            $str .= $t80->multiItemsFromArray([
                [$tipoVenta->VentasMinConsecutivo, 0, ' ', 1],
                [$tipoVenta->VentasMaxConsecutivo, 24, ' ', 1]
            ]);
        }

        $str .= $t80->posLineaBlanco();


        // IMPUESTOS
        $str .= $t80->posLineaDerecha('DETALLE DE IMPUESTOS');
        $impuestos = ReportesGenerados::impuestoFiscal($fechaIni);

        foreach ($impuestos as $key => $impuesto) {

            if ($key == 0) {
                $str .= $t80->posLineaDerecha('- '.$impuesto->tipo_documento);
            } else if ($impuestos[$key -1]->tipo_documento != $impuesto->tipo_documento) {
                $str .= $t80->posLineaDerecha('- '.$impuesto->tipo_documento);
            }

            $str .= $t80->multiItemsFromArray([
                ['-- Base '.$impuesto->impuesto.'%' , 0, ' ', 1],
                [$t80->toNumber($impuesto->ValorTotalSinIVA), 12, ' ', -1],
                [$t80->toNumber($impuesto->ValorIVA), 12, ' ', -1]
            ]);
        }

        $printer->text($str);
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }





     public static function validarProceso($nombreEmpresa, $marinado, $productoEmpacado, $encabezadoEtiqueta, $tercero_nombre){

        $proceso = '';


        if (($productoEmpacado == 0)) {
            $proceso = "^FT140,550^ARN,40^FH\^CI28^FDDESPOSTADO POR: ".strtoupper($nombreEmpresa)."^FS^CI28";
        }
        if (($productoEmpacado == 1) || ($productoEmpacado == 0 && $encabezadoEtiqueta == 0)) {
            $proceso = "^FT130,550^A0N,30^FH\^CI28^FDDISTRIBUIDO POR: ".strtoupper($nombreEmpresa)."^FS^CI28";
        }
        if ($productoEmpacado == 2) {
            $proceso = "^FT40,550^A0N,26,26^FH\^CI28^FDDESPOSTADO POR: ".strtoupper($tercero_nombre)."-Procesado por ".strtoupper($nombreEmpresa)."^FS^CI28";
        }

        return $proceso;
    }

     public static function toCollect($collect){
        $details = collect($collect)->map(function ($item) {
            return (object) $item;
        });
        return $details;
    }

    public static function printPOSComComproEgreso($id)
    {
        $user = User::find(1);
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $t80 = new ReportesT80();
        $str = '';

        $recibo = ComComproEgreso::find($id);

        $tipoComproEgreso = ComTipoComproEgreso::find($recibo->com_tipo_compro_egresos_id);

        $lineas = ComPivotCompraEgreso::getDataLineasPDF($id);
        $pagos = ComPivotFormaEgreso::getDataLineasPDF($id);

        $sucursal = TerceroSucursal::find($recibo->proveedor_id);
        $tercero = $sucursal->tercero;

        $str .= $t80->posLineaBlanco(' ');
        $str .=$t80->printLogoT80($printer);
        $str .= $t80->posLineaBlanco();
        $str .= $t80->posHeaderEmpresa();
        $str .= $t80->posLineaBlanco();

        $str .= $t80->posLineaCentro($tipoComproEgreso->nombre);
        $str .= $t80->posLineaCentro('No. ' .$recibo->consecutivo);
        $str .= $t80->posLineaCentro('Fecha documento: ' .$recibo->fecha_comprobante);
        $str .= $t80->posLineaGuion();

        // DATOS DEL CLIENTE
        $str .= $t80->posLineaDerecha('Cliente: '.$tercero->nombre);
        if ($tercero->digito_verificacion) {
            $str .= $t80->posLineaDerecha('doc: : '.$tercero->documento.'-'.$tercero->digito_verificacion.' - TEL: '.$sucursal->telefono);
        } else {
            $str .= $t80->posLineaDerecha("DOC: ".$tercero->documento.' - TEL: '.$sucursal->telefono);
        }
        $str .= $t80->posLineaDerecha('direccion: '.$sucursal->direccion);

        $str .= $t80->posLineaGuion();

        $str.= $t80->posLineaDerecha('No. Fac   Valor Factura    Valor Abono     Saldo');
        $str .= $t80->posLineaGuion();

        foreach ($lineas as $linea) {
            $str .= str_pad($linea->consec_mov, 9, " ", STR_PAD_RIGHT);
            $str .= str_pad('$ '.number_format(intval($linea->valor_factura), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
            $str .= str_pad('$ '.number_format(intval($linea->valor), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
            $str .= str_pad('$ '.number_format(intval($linea->saldo_mov), 0, ',', '.'), 13, " ", STR_PAD_LEFT);
        }
        $str .= $t80->posLineaBlanco();
        $str .= $t80->posLineaBlanco();

        $totalPagos = 0;

        $str .= $t80->posLineaCentro('pagos', '-');

        foreach ($pagos as $pago) {
            if ($pago->valor > 0) {
                $str.= $t80->posDosItemsExtremos(strtoupper($pago->forma_nombre), '$ '.$t80->toNumber( $pago->valor));
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

    public static function getHeadersFiscal ($data) {
        $allData = self::toCollect($data);
        $headers = [];
        $docTypes = $allData->unique('naturaleza');
        foreach ($docTypes as $docType) {
            $headers[$docType->naturaleza]['min'] = $allData->where('naturaleza', $docType->naturaleza)->min('consecutivo');
            $headers[$docType->naturaleza]['max'] = $allData->where('naturaleza', $docType->naturaleza)->max('consecutivo');
            $headers[$docType->naturaleza]['total'] = $allData->where('naturaleza', $docType->naturaleza)->sum('total');
        }
        dd($headers);
        return $headers;

    }

    public static function exportReporteFiscalExcel() {
        $params = $_GET;
        $fecha_inicial = str_replace('/', '-', $params['fecha_inicial']);
        $fecha_final = str_replace('/', '-', $params['fecha_final']);
        return Excel::download(new ReporteFiscal($fecha_inicial, $fecha_final), 'Reporte-Fiscal.xlsx');
    }

    //TESTING
    public static function testing() {
        //Naturaleza 1: Venta Credito, 4: POS, 2: NC, 3: ND 20431,5
        $fecha_ini = '2021-08-01';
        $fecha_fin = '2021-08-02';
        $bolsas = ReportesGenerados::impuestoBolsaFiscal($fecha_ini, $fecha_fin);
        dd($bolsas);
        $sumatoria = 0;
        // $data = ReportesGenerados::reporteFiscal($fecha_ini, $fecha_fin);
        // $details = ReportesGenerados::DetailsFiscal($fecha_ini, $fecha_fin);
        // $headers = ReportesGenerados::HeadersFiscal($fecha_ini, $fecha_fin);
        // $example = [];
        foreach($bolsas as $bolsa){
                $sumatoria += $bolsa->valor;
        }
        dd($sumatoria);
        // dd($example);
        // foreach ($total as $k => $v) {
        //     echo "\$a[$k] => $v.\n";
        // }

    }
}
