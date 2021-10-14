<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenBascula;
use App\FacPivotMovProducto;
use App\GenEmpresa;
use App\GenImpresora;
use App\Producto;
use App\GenVendedor;
use App\GenUnidades;
use Carbon\Carbon;
use PDF;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Illuminate\Support\Facades\Auth;
use App\Tools;

class GenBasculasController extends Controller
{
    public function index()
    {
        $index= GenBascula::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function activos()
    {
        $index= GenBascula::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new GenBascula($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = GenBascula::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = GenBascula::find($id);

         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = GenBascula::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = GenBascula::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function readTiqueteDibal($tiquete, $puestoTiquete)
    {
        $arrayTotal = array();
        $arrayItem = array();
        $tiquete = intval($tiquete);

        $ruta = GenEmpresa::find(1)->ruta_archivo_tiquetes_dibal;

        $dia = date('d');

        $mes = date('n');

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }


        $date = Carbon::now();
        $fechaIni = $date->format('d/m/Y');
        $fechaFin = $date->addDay()->format('d/m/Y');

        // dd($fechaIni , $fechaFin);

        $list = FacPivotMovProducto::where('num_tiquete', $tiquete)->where('puesto_tiquete', $puestoTiquete)->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

        $lineasFacturadas = array();

        foreach ($list as $item) {
            array_push($lineasFacturadas, $item->num_linea_tiquete);
        }

        if (substr($tiquete, 0, 1) == '2' && strlen($tiquete) > 6){
            $seccion = '001';
            $tiquete = intval(substr($tiquete,6,6));
        } else {
            $seccion = '000';
        }

        $val = $ruta.'/BL'.$seccion.$dia.$mes.'.TOT';

        $handle = @fopen($val, "r");

        if ($handle) {
            while (($buffer = fgets($handle)) !== false) {

                $arrayItem = array( intval(substr($buffer, 10, 6)), // cdigo producto
                                    intval(substr($buffer, 16, 6)), // cantidad
                                    intval(substr($buffer, 22, 9)),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 2, 5)), // numero tiquete
                                    intval(substr($buffer, 7, 3)), // linea tiquete
                                    intval(substr($buffer, 31, 2)));// vendedo

                if (( (intval(substr($buffer, 2, 5)) == $tiquete)) &&
                     (!in_array(intval(substr($buffer, 7, 3)), $lineasFacturadas))
                    ){
                    array_push($arrayTotal, $arrayItem);
                }
            }

            return $arrayTotal;

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }

    public function readTiqueteEpelsa($tiquete)
    {
        $arrayTotal = array();
        $arrayItem = array();
        $lineasFacturadas = array();
        $tiquete = intval($tiquete);

        $ruta = GenEmpresa::find(1)->ruta_archivo_tiquetes_epelsa;

        $dia = date('d');

        $mes = date('n');

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        $val = $ruta.'/tqgen'.$dia.$mes;

        $date = Carbon::now();
        $fechaIni = $date->format('d/m/Y');
        $fechaFin = $date->addDay()->format('d/m/Y');

        $list = FacPivotMovProducto::where('num_tiquete', $tiquete)->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

        foreach ($list as $item) {
            array_push($lineasFacturadas, $item->num_linea_tiquete);
        }

        $handle = @fopen($val, "r");

        if ($handle) {
            while (($buffer = fgets($handle)) !== false) {

                $arrayItem = array( intval(substr($buffer, 30, 4)), // cdigo producto
                                    intval(str_replace('.','',substr($buffer, 54, 7))), // cantidad
                                    intval(str_replace('.','',substr($buffer, 61, 8))),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 0, 4)), // numero tiquete
                                    intval(substr($buffer, 4, 3)), // linea tiquete
                                    intval(substr($buffer, 11, 4)));// vendedor

                if (( (intval(substr($buffer, 0, 4)) == $tiquete)) && (!in_array(intval(substr($buffer, 4, 3)), $lineasFacturadas)) ){

                    if ((strpos($buffer, '-') === false )){
                        array_push($arrayTotal, $arrayItem);
                    } else {
                        array_pop($arrayTotal);
                    }
                }
            }

            return $arrayTotal;

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }


    public function tiquetesNoFacturadosMarques($fecha)
    {
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $empresa = GenEmpresa::find(1);
        $fechaIni = date('d/m/Y', strtotime($fecha));

        $etiqueta = str_pad("", 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->razon_social), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->nombre), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("NIT: ".$empresa->nit, 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->tipo_regimen), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad(strtoupper($empresa->direccion), 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("TEL: ".$empresa->telefono, 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
        $etiqueta .= str_pad('TIQUETES NO FACTURADOS', 48, " ", STR_PAD_BOTH);
        $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
        $etiqueta .= str_pad('Fecha: '.$fechaIni, 48, " ", STR_PAD_RIGHT);
        $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);

        $fechaArray = explode('-', $fecha);

        $fecha = $fechaArray[2].'-'.$fechaArray[1].'-'.$fechaArray[0];

        $arrayTotal = array();
        $arrayItem = array();
        $arrayLines = array();
        $totalGnal = 0;
        $totalTiquete = 0;
        $tiqueteAnterior ='';

        $marquesData = GenEmpresa::find(1)->ruta_ip_marques;

        $basculas = explode('&', $marquesData);

        foreach ($basculas as $bascula) {
            $ip = explode('-', $bascula)[0];
            $puesto = explode('-', $bascula)[1];

            $tiquetesFacturados = $this->tiquetesDiaBascula($puesto, $fecha);

            $primerTiquete = $tiquetesFacturados->first()->num_tiquete;

            $primerTiquete = intval($primerTiquete) - 20;

            for ($i=0; $i < 5; $i++) {

                $primer = $primerTiquete + ($i * 100);

                $url = 'http://' .$ip. '/year/documentos?seek={"tipo_doc":1,"posto":'.$puesto. ',"numero":' .$primer. '}&limit=100';

                $tiquetesBascula = Tools::http_get($url);

                foreach ($tiquetesBascula as $tiqueteBasc) {

                    if (($tiqueteBasc['d_doc'] == $fecha) && ($tiqueteBasc['posto'] == $puesto)) {


                        $url = 'http://'.$ip.'/year/documentos_lnh?seek={"tipo_doc":1,"posto":'.$puesto.',"numero":'.$tiqueteBasc['numero'].',"linha_f":0}&limit='.$tiqueteBasc['nr_parcelas'];

                        $lineasTiquete = Tools::http_get($url);

                        foreach ($lineasTiquete as $key => $linea) {

                            if ($tiqueteBasc['numero'] == $linea['numero']) {

                                $lineaFacturada = $tiquetesFacturados->where('num_tiquete', $linea['numero'])->where('num_linea_tiquete', $linea['linha_f'])->all();

                                if (count($lineaFacturada) < 1) {

                                    if ($v = GenVendedor::where('codigo_unico', intval($tiqueteBasc['num_vendedor']))->get()->first()) {
                                        $vendedor = $v->nombre;
                                    } else {
                                        $vendedor = $tiqueteBasc['num_vendedor'];
                                    }

                                    $producto = Producto::where('codigo', intval($linea['codigo']))->get()->first();

                                    $total = intval($linea['valor']);

                                    // linea 1
                                    if ($producto) {

                                        if (intval($tiqueteAnterior) != intval($tiqueteBasc['numero'])) {
                                            $etiqueta .= str_pad(' Tiq. # : '.$tiqueteBasc['numero'].' - '.eliminar_acentos(str_replace('ñ', 'N',substr($vendedor,0, 30))).' ', 48, "-", STR_PAD_BOTH);
                                            $tiqueteAnterior = $tiqueteBasc['numero'];
                                        }

                                        $total = intval($linea['valor']);
                                        $totalGnal = $totalGnal + $total;

                                        $etiqueta .= str_pad($producto->codigo, 3, "0", STR_PAD_LEFT);
                                        $etiqueta .= ' ';
                                        $nombre = strtoupper($producto->nombre);
                                        $nombre = str_replace('ñ', 'N', $nombre);
                                        $etiqueta .= str_pad(substr($nombre, 0, 28), 29, " ", STR_PAD_RIGHT);
                                        $etiqueta .= ' ';
                                        $etiqueta .= str_pad(number_format($total, 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                                        $etiqueta .= ' |';
                                        $etiqueta .= str_pad('', 2, " ", STR_PAD_LEFT);
                                        // linea 2
                                        $etiqueta .= '    ';
                                        $etiqueta .= str_pad(number_format($linea['quantidade'], 3, ',', '.'), 6, " ", STR_PAD_LEFT);
                                        $etiqueta .= ' ';
                                        $etiqueta .= GenUnidades::find($producto->gen_unidades_id)->abrev_pos;
                                        $etiqueta .= ' ';
                                        $etiqueta .= 'X';
                                        $etiqueta .= ' ';
                                        $etiqueta .= '$';
                                        $etiqueta.= str_pad(number_format( $linea['preco_unit'], 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                                        $etiqueta .= ' ';    //22
                                        $etiqueta .= '                   ';

                                        array_push($arrayLines, array(
                                            'tiquete' => $tiqueteBasc['numero'],
                                            'vendedor' => $vendedor,
                                            'linea_tiquete'=> $key + 1,
                                            'codigo' => $producto->codigo,
                                            'producto' => strtoupper($producto->nombre),
                                            'total' => $total,
                                            'cantidad' => $linea['quantidade'],
                                            'unidades' => GenUnidades::find($producto->gen_unidades_id)->abrev_pos,
                                            'precio' => $linea['preco_unit']
                                        ));

                                    } else {
                                        $etiqueta .= str_pad('El codigo '. $linea['codigo']. 'no existe', 48, " ", STR_PAD_RIGHT);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
        $etiqueta .= str_pad('', 48, "-", STR_PAD_LEFT);
        $etiqueta .= 'Total No Facturado:';
        $etiqueta .= str_pad(number_format($totalGnal, 0, ',', '.'), 29, " ", STR_PAD_LEFT);
        $etiqueta .= str_pad("Fecha Impresion " . date('Y-m-d H:i:s'), 48, " ", STR_PAD_RIGHT);


        $printer->text($etiqueta);
        $printer->feed(2);
        $printer->cut();
        $printer->pulse();
        $printer->close();

        return 'done';
    }

    public function tiquetesNoFacturados($fecha)
    {
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $empresa = GenEmpresa::find(1);

        $fechaIni = date('d/m/Y', strtotime($fecha));
        $fechaFin = date('d/m/Y', strtotime($fecha . ' + 1 day'));
        $arrayTotal = array();
        $arrayItem = array();
        $totalGnal = 0;
        $totalTiquete = 0;

        $dia = substr($fecha, 0,2);

        $mes = intval(substr($fecha, 3,2));

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        if ($empresa->tipo_escaner == 1) {
            $val = $empresa->ruta_archivo_tiquetes_dibal.'/BL000'.$dia.$mes.'.TOT';
        } else if ($empresa->tipo_escaner == 4) {
            $val = $empresa->ruta_archivo_tiquetes_epelsa.'/tqgen'.$dia.$mes;
        }


        $tiqueteAnterior = 0;

        $handle = @fopen($val, "r");

        if ($handle) {

            $etiqueta = str_pad("", 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->razon_social), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->nombre), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("NIT: ".$empresa->nit, 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->tipo_regimen), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->direccion), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("TEL: ".$empresa->telefono, 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('TIQUETES NO FACTURADOS', 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('Fecha: '.$fechaIni, 48, " ", STR_PAD_RIGHT);
            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);

            while (($buffer = fgets($handle)) !== false) {

                if ($empresa->tipo_escaner == 1) {
                    $arrayItem = array( intval(substr($buffer, 10, 6)), // cdigo producto
                                    intval(substr($buffer, 16, 6)), // cantidad
                                    intval(substr($buffer, 22, 9)),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 3, 4)), // numero tiquete
                                    intval(substr($buffer, 7, 3)), // linea tiquete
                                    intval(substr($buffer, 31, 2)));// vendedor
                } else if ($empresa->tipo_escaner == 4) {
                    $arrayItem = array( intval(substr($buffer, 30, 4)), // cdigo producto
                                    intval(str_replace('.','',substr($buffer, 54, 7))), // cantidad
                                    intval(str_replace('.','',substr($buffer, 61, 8))),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 0, 4)), // numero tiquete
                                    intval(substr($buffer, 4, 3)), // linea tiquete
                                    intval(substr($buffer, 11, 4)));// vendedor
                }

                $list = FacPivotMovProducto::where('num_tiquete', $arrayItem[3])
                                            ->where('num_linea_tiquete', $arrayItem[4])
                                            ->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

                if ( count($list) < 1) {

                    if (intval($tiqueteAnterior) != intval($arrayItem[3])) {
                        if ($v = GenVendedor::where('codigo_unico', intval($arrayItem[5]))->get()->first()) {
                            $vendedor = $v->nombre;
                        } else {
                            $vendedor = $arrayItem[5];
                        }
                        $etiqueta .= str_pad(' Tiq. # : '.$arrayItem[3].' - '.eliminar_acentos(str_replace('ñ', 'N',substr($vendedor,0, 30))).' ', 48, "-", STR_PAD_BOTH);
                        $tiqueteAnterior = $arrayItem[3];
                    }

                    $producto = Producto::where('codigo', $arrayItem[0])->get()->first();
                    if ($arrayItem[1] < 1) {
                        $arrayItem[1] = 1;
                    }
                    if ($arrayItem[1] < 100) {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1]));
                    } else {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1])/1000);
                    }

                    // linea 1
                    if ($producto) {
                        $etiqueta .= str_pad($producto->codigo, 3, "0", STR_PAD_LEFT);
                        $etiqueta .= ' ';
                        $nombre = strtoupper($producto->nombre);
                        $nombre = str_replace('ñ', 'N', $nombre);
                        $etiqueta .= str_pad(substr($nombre, 0, 28), 29, " ", STR_PAD_RIGHT);
                        $etiqueta .= ' ';
                        $total = intval($arrayItem[2]);
                        $totalGnal = $totalGnal + $total;
                        $total =  number_format($total, 0, ',', '.');
                        $etiqueta .= str_pad($total, 10, " ", STR_PAD_LEFT);
                        $etiqueta .= ' |';
                        $etiqueta .= str_pad('', 2, " ", STR_PAD_LEFT);
                        // linea 2
                        $etiqueta .= '    ';
                        if ($arrayItem[1] < 100) {
                            $etiqueta .= str_pad(number_format($arrayItem[1], 3, ',', '.'), 6, " ", STR_PAD_LEFT);
                        } else {
                            $etiqueta .= str_pad(number_format($arrayItem[1]/1000, 3, ',', '.'), 6, " ", STR_PAD_LEFT);
                        }
                        $etiqueta .= ' ';
                        $etiqueta .= GenUnidades::find($producto->gen_unidades_id)->abrev_pos;
                        $etiqueta .= ' ';
                        $etiqueta .= 'X';
                        $etiqueta .= ' ';
                        $etiqueta .= '$';
                        $etiqueta.= str_pad(number_format($precioUnit, 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                        $etiqueta .= ' ';    //22
                        $etiqueta .= '                   ';
                    } else {
                        $etiqueta .= str_pad('El codigo '. $arrayItem[0]. 'no existe', 48, " ", STR_PAD_RIGHT);
                    }
                }

            }

            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('', 48, "-", STR_PAD_LEFT);
            $etiqueta .= 'Total No Facturado:';
            $etiqueta .= str_pad(number_format($totalGnal, 0, ',', '.'), 29, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad("Fecha Impresion " . date('Y-m-d H:i:s'), 48, " ", STR_PAD_RIGHT);

            $printer->text($etiqueta);
            $printer->feed(2);
            $printer->cut();
            $printer->pulse();
            $printer->close();

            return 'done';

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }

    public function tiquetesNoFacturadosPDF($fecha)
    {
        $empresa = GenEmpresa::find(1);

        if ($empresa->tipo_escaner == 2) {

            $arrayLines = $this->marquesPDF($fecha);

        } else  {
            $arrayLines = $this->epelsaDibalPDF($fecha);
        }
        $data = ['etiqueta' => $arrayLines];
        $pdf = PDF::loadView('facturacion.tiquetesnofacturados', $data);

        return $pdf->stream();
    }

    public function marquesPDF($fecha)
    {

        $fechaArray = explode('-', $fecha);

        $fecha = $fechaArray[2].'-'.$fechaArray[1].'-'.$fechaArray[0];

        $arrayTotal = array();
        $arrayItem = array();
        $arrayLines = array();
        $totalGnal = 0;
        $totalTiquete = 0;

        $marquesData = GenEmpresa::find(1)->ruta_ip_marques;

        $basculas = explode('&', $marquesData);

        foreach ($basculas as $bascula) {
            $ip = explode('-', $bascula)[0];
            $puesto = explode('-', $bascula)[1];

            $tiquetesFacturados = $this->tiquetesDiaBascula($puesto, $fecha);
            $test = substr($tiquetesFacturados, 0, 5);

            if(substr($tiquetesFacturados, 0, 5) != "Error"){

                $primerTiquete = $tiquetesFacturados->first()->num_tiquete;
                $primerTiquete = intval($primerTiquete) - 20;

                for ($i=0; $i < 5; $i++) {

                    $primer = $primerTiquete + ($i * 100);
                    $url = 'http://' .$ip. '/year/documentos?seek={"tipo_doc":1,"posto":'.$puesto. ',"numero":' .$primer. '}&limit=100';
                    $tiquetesBascula = http_get($url);

                    if($tiquetesBascula != null){

                        foreach ($tiquetesBascula as $tiqueteBasc) {

                            if (($tiqueteBasc['d_doc'] == $fecha) && ($tiqueteBasc['posto'] == $puesto)) {

                                $url = 'http://'.$ip.'/year/documentos_lnh?seek={"tipo_doc":1,"posto":'.$puesto.',"numero":'.$tiqueteBasc['numero'].',"linha_f":0}&limit='.$tiqueteBasc['nr_parcelas'];
                                $lineasTiquete = Tools::http_get($url);

                                foreach ($lineasTiquete as $key => $linea) {

                                    if ($tiqueteBasc['numero'] == $linea['numero']) {

                                        $lineaFacturada = $tiquetesFacturados->where('num_tiquete', $linea['numero'])->where('num_linea_tiquete', $linea['linha_f'])->all();

                                        if (count($lineaFacturada) < 1) {

                                            if ($v = GenVendedor::where('codigo_unico', intval($tiqueteBasc['num_vendedor']))->get()->first()) {
                                                $vendedor = $v->nombre;
                                            } else {
                                                $vendedor = $tiqueteBasc['num_vendedor'];
                                            }

                                            $producto = Producto::where('codigo', intval($linea['codigo']))->get()->first();

                                            $total = intval($linea['valor']);

                                            // linea 1
                                            if ($producto) {

                                                $total = intval($linea['valor']);
                                                $totalGnal = $totalGnal + $total;

                                                array_push($arrayLines, array(
                                                    'tiquete' => $tiqueteBasc['numero'],
                                                    'vendedor' => $vendedor,
                                                    'linea_tiquete'=> $key + 1,
                                                    'codigo' => $producto->codigo,
                                                    'producto' => strtoupper($producto->nombre),
                                                    'total' => $total,
                                                    'cantidad' => $linea['quantidade'],
                                                    'unidades' => GenUnidades::find($producto->gen_unidades_id)->abrev_pos,
                                                    'precio' => $linea['preco_unit']
                                                ));

                                            } else {
                                                array_push($arrayLines, array(
                                                    'tiquete' => $tiqueteBasc['numero'],
                                                    'vendedor' => $vendedor,
                                                    'linea_tiquete'=> $key + 1,
                                                    'codigo' =>  $linea['codigo'],
                                                    'producto' => 'PRODUCTO NO EXISTENTE',
                                                    'total' => $total,
                                                    'cantidad' => $linea['quantidade'],
                                                    'unidades' => '',
                                                    'precio' => $linea['preco_unit']
                                                ));
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $arrayLines;
    }

    public function epelsaDibalPDF($fecha)
    {
        $empresa = GenEmpresa::find(1);
        $total = 0;
        $fechaIni = date('d/m/Y', strtotime($fecha));
        $fechaFin = date('d/m/Y', strtotime($fecha . ' + 1 day'));
        $arrayTotal = array();
        $arrayItem = array();
        $arrayLines = array();
        $totalGnal = 0;
        $totalTiquete = 0;

        $dia = substr($fecha, 0,2);

        $mes = intval(substr($fecha, 3,2));

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        if ($empresa->tipo_escaner == 1) {
            $val = $empresa->ruta_archivo_tiquetes_dibal.'/BL000'.$dia.$mes.'.TOT';
        } else if ($empresa->tipo_escaner == 4) {
            $val = $empresa->ruta_archivo_tiquetes_epelsa.'/tqgen'.$dia.$mes;
        }

        $tiqueteAnterior = 0;

        $handle = @fopen($val, "r");

        if ($handle) {

            $etiqueta = str_pad("", 48, "-", STR_PAD_BOTH);

            while (($buffer = fgets($handle)) !== false) {

                if ($empresa->tipo_escaner == 1) {
                    $arrayItem = array( intval(substr($buffer, 10, 6)), // cdigo producto
                                    intval(substr($buffer, 16, 6)), // cantidad
                                    intval(substr($buffer, 22, 9)),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 3, 4)), // numero tiquete
                                    intval(substr($buffer, 7, 3)), // linea tiquete
                                    intval(substr($buffer, 31, 2)));// vendedor
                } else if ($empresa->tipo_escaner == 4) {
                    $arrayItem = array( intval(substr($buffer, 30, 4)), // cdigo producto
                                    intval(str_replace('.','',substr($buffer, 54, 7))), // cantidad
                                    intval(str_replace('.','',substr($buffer, 61, 8))),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 0, 4)), // numero tiquete
                                    intval(substr($buffer, 4, 3)), // linea tiquete
                                    intval(substr($buffer, 11, 4)));// vendedor
                }

                $list = FacPivotMovProducto::where('num_tiquete', $arrayItem[3])
                                            ->where('num_linea_tiquete', $arrayItem[4])
                                            ->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

                if ( count($list) < 1) {

                    if ($v = GenVendedor::where('codigo_unico', intval($arrayItem[5]))->get()->first()) {
                        $vendedor = $v->nombre;
                    } else {
                        $vendedor = $arrayItem[5];
                    }

                    $producto = Producto::where('codigo', $arrayItem[0])->get()->first();

                    if ($arrayItem[1] < 1) {
                        $arrayItem[1] = 1;
                    }

                    if ($arrayItem[1] < 100) {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1]));
                    } else {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1])/1000);
                        $arrayItem[1] = $arrayItem[1]/1000;
                    }

                    // linea 1
                    if ($producto) {

                        $total = intval($arrayItem[2]);
                        $totalGnal = $totalGnal + $total;

                        array_push($arrayLines, array(
                            'tiquete' => $arrayItem[3],
                            'vendedor' => $vendedor,
                            'linea_tiquete'=> $arrayItem[4],
                            'codigo' => $producto->codigo,
                            'producto' => strtoupper($producto->nombre),
                            'total' => $total,
                            'cantidad' => $arrayItem[1],
                            'unidades' => GenUnidades::find($producto->gen_unidades_id)->abrev_pos,
                            'precio' => $precioUnit
                        ));

                    } else {
                        array_push($arrayLines, array(
                            'tiquete' => $arrayItem[3],
                            'vendedor' => $vendedor,
                            'linea_tiquete'=> $arrayItem[4],
                            'codigo' =>  $arrayItem[0],
                            'producto' => 'PRODUCTO NO EXISTENTE',
                            'total' => $total,
                            'cantidad' => $arrayItem[1],
                            'unidades' => 0,
                            'precio' => 0
                        ));
                    }
                    if ((strpos($buffer, '-') !== false && $empresa->tipo_escaner == 4)){
                        array_pop($arrayLines);
                        array_pop($arrayLines);
                    }
                }
            }

            return $arrayLines;

        } else {

            return 'El archivo para la fecha especificada no existe';
        }
    }

    public function tiquetesDiaBascula($bascula, $fecha){

        $date= explode('-', $fecha);
        $fecha = $date[2].'/'.$date[1].'/'.$date[0];

        $tiquetes = FacPivotMovProducto::tiquetesDiaBascula($bascula, $fecha);

        if (count($tiquetes)) {
            return $tiquetes;
        } else {
            return 'Error no hay tiquetes el dia '. $fecha . ' con la bascula '. $bascula;
        }
    }

    public function verificarTiqueteMarques($tiquete,$puesto, $fecha)
    {

        $date = Carbon::now()->subHours(5);
        $fechaIni = $date->format('d/m/Y H:i:s');
        $fechaFin = $date->addDay()->format('d/m/Y H:i:s');

        $list = FacPivotMovProducto::where('num_tiquete', $tiquete)->where('puesto_tiquete', $puesto)->whereBetween('created_at', [$fechaIni, $fechaFin])->get();
        return $list;
    }
}
