<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\GenMunicipio;
use App\GenDepartamento;
use Illuminate\Support\Facades\DB;

class GenEtiqueta extends Model
{


    public static function imprimirEtiqueta($impresora, $item, $marinado, $etiquetaInterna = false) {

        $almace ='';
        $empresa = GenEmpresa::find(1);
        $empresa->municipio = GenMunicipio::find($empresa->gen_municipios_id)->nombre;
        $empresa->departamento = GenDepartamento::find(GenMunicipio::find($empresa->gen_municipios_id)->departamento_id)->nombre;

        if (strtoupper($impresora) != 'MANUAL') {

            $nombre_impresora = str_replace('SMB', 'smb', strtoupper($impresora));
            $connector = new WindowsPrintConnector($nombre_impresora);
            $printer = new Printer($connector);
            $printer->setJustification(Printer::JUSTIFY_CENTER);

        }

        if ($etiquetaInterna) {
            $dataProducto = self::GetDataProductoEtiquetaInterna($item)->first();
            $dataLote = self::GetDataLoteEtiquetaInterna($item)->first();

            $data = (object) array_merge((array) $dataProducto, (array) $dataLote);

            $data->fecha_empaque = Carbon::now()->toDateString();
            $data->almacenamiento = ProdAlmacenamiento::find($item->prodAlmacenamiento_id);

        } else {
            $data = Inventario::GetDataEtiqueta($item->id)->first();
            $data->peso = number_format($data->peso, 3, '.', '');
        }
        
        $data->fecha_vencimiento = Carbon::parse($data->fecha_sacrificio)->addDays($data->dias_vencimiento)->toDateString();
        $data->fecha_empaque = Carbon::parse($data->fecha_empaque)->toDateString();

        $almaRefrigerado = strrpos($data->almacenamiento, "Refrigerado");
        $almaCongelado = strrpos($data->almacenamiento, "Congelado");

        if ($almaRefrigerado !== false) { $almace = "MANTENGASE REFRIGERADO DE 0\F8C A 4C\F"; }
        if ($almaCongelado !== false) { $almace = "MANTENGASE CONGELADO A -18\F8C"; }

        if ($data->grupo !== 'Res') { $porcMarinado = "10%"; }
        if ($data->grupo !== 'Cerdo') { $porcMarinado = "12%"; }
        // $titulo = "CARNE DE ".strtoupper($data->grupo);

        $titulo =  self::validarTitulo($data->encabezado_etiqueta, $data->grupo, $marinado);

        $proceso = self::validarProceso($empresa->razon_social, $marinado, $data->producto_empacado, $data->encabezado_etiqueta);        

        $etiqueta = "
                ^XA
                ^CF0,80
                ^CI28
                ".$proceso."
                ^FT140,590^ARN,24,24^FH\^CI28^FD".$empresa->municipio." ".$empresa->direccion." Tel ".$empresa->telefono."^FS^^CI28
                ^FPH,1^FT20,80^ARN,53,53^FH\^FD".strtoupper(eliminar_acentos($data->producto))."^FS^CI28
                ^FT35,295^A0N,20,20^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                ^FPH,1^FT30,126^A0N,43,43^FD".$titulo."^FS^CI28"
                .self::logoEtiqueta();

        if ($etiquetaInterna) {
            $etiqueta .= self::headersEtiquetas($data, false, true, true, false);

        } else {
            $etiqueta .= self::headersEtiquetas($data);
            $etiqueta .= "^BY3,3,80^FT250,480^BCN,,Y,N
                            ^FH\^FD>:".$item->id."^FS";
        }

        if($data->producto_aprobado == true){
            $etiqueta .= "^FT610,230^A0N,24,24^FH\^CI28^FD - Aprobado^FS^CI28";
        }

        if(!$data->producto_empacado){ //LOTES JH

            if ($data->encabezado_etiqueta > 0) {
                $etiqueta .= self::fechasEtiqueta($data);
            } else {
                $etiqueta .= self::fechasEtiqueta($data, true, false, false, true);
            }

        }else{

        	$etiqueta .= "
                    ^FT30,220^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT270,220^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28";
        }

        if ($marinado) {

        	$etiqueta .= self::marinadoEtiquetas($porcMarinado)."
                ^FPH,1^FT30,155^ARN,27,27^FH\^FDReg. RSA  ".$data->registro_sanitario."^FS^CI28";
        }

        $etiqueta .="
            ^XZ";

        if (strtoupper($impresora) != 'MANUAL') {
            $printer->text($etiqueta);
            $printer->close();

            return 'doneNoRestore';
        } else {
            return $etiqueta;
        }

        
    }

    public static function validarTitulo($encabezado, $grupo, $marinado){
        if($encabezado > 0 ) {
            if(!$marinado){
                return  "CARNE DE ".strtoupper($grupo);
            }else{
                return  "CARNE DE ".strtoupper($grupo)." MARINADA";
            }
        }else{
            return  'PRODUCTO CARNICO COMESTIBLE';
        }
    }

    public static function validarProceso($nombreEmpresa, $marinado, $productoEmpacado, $encabezadoEtiqueta){

        $proceso = "^FT140,550^ARN,40^FH\^CI28^FDDESPOSTADO POR: ".strtoupper($nombreEmpresa)."^FS^CI28";

        if ($productoEmpacado || $encabezadoEtiqueta == 0) {
            $proceso = "^FT130,550^A0N,30^FH\^CI28^FDDISTRIBUIDO POR: ".strtoupper($nombreEmpresa)."^FS^CI28";

        } elseif ($marinado && $productoEmpacado && $encabezadoEtiqueta >0) {
            $proceso = "^FT50,550^A0N,30^FH\^CI28^FDPROCESADO Y DISTRIBUIDO POR: ".strtoupper($nombreEmpresa)."^FS^CI28";
        }

        return $proceso;
    }

    public static function logoEtiqueta () {
        return '';
    }

    public static function fechasEtiqueta ($data, $boolSacrificio = true, $boolDesposte = true, $boolEmpaque = true, $boolVencimiento = true) {

        $fechas = '';

        if ($boolSacrificio){
            $fechas .= "^FT30,185^ARN,1^FH\^CI28^FDFecha Sacrificio:^FS^CI28";
            $fechas .= "^FT235,185^A0N,24,24^FH\^CI28^FD".$data->fecha_sacrificio."^FS^CI28";
        }

        if ($boolDesposte){
            $fechas .= "^FT30,210^ARN,5,5^FH\^CI28^FDFecha Desposte:^FS^CI28";
            $fechas .= "^FT235,210^A0N,24,24^FH\^CI28^FD".$data->fecha_desposte."^FS^CI28";
        }

        if ($boolEmpaque){
            $fechas .= "^FT30,235^ARN,5,5^FH\^CI28^FDFecha Empaque:^FS^CI28";
            $fechas .= "^FT235,235^A0N,24,24^FH\^CI28^FD".$data->fecha_empaque."^FS^CI28";
        }

        if ($boolVencimiento){
            $fechas .= "^FT30,260^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28";
            $fechas .= "^FT270,260^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28";
        }

        return $fechas;
    }

    public static function marinadoEtiquetas ($porcMarinado) {

        return "^FT300,320^A0N,28,28^FH\^CI28^FDINGREDIENTES:^FS^CI28
                    ^FT25,340^A0N,23,23^FH\^CI28^FDCarne marinada al " .$porcMarinado." por inyecci\A2n, agua, salmuera (Sal), tripolifosfato de sodio E451^FS^CI28
                    ^FT25,360^A0N,23,23^FH\^CI28^FD(Emulsificador), fosfato de sodio 450 (Estabilizante), fosfato tricalcico E341 ^FS^CI28
                    ^FT25,380^A0N,23,23^FH\^CI28^FD(Estabilizante) menor al 1%.^FS^CI28";

    }

    public static function headersEtiquetas ($data, $boolPeso = true, $boolMarca = true, $boolLote = true, $boolNumPiezas = true) {

        $headers = '';

        if ($boolPeso){
            $headers .= "^FT470,170^ARN,1^FH\^CI28^FDPeso:^FS^CI28";
            $headers .= "^FT570,170^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28";
        }

        if ($boolMarca){
            $headers .= "^FT470,200^ARN,1^FH\^CI28^FDMarca:^FS^CI28";
            $headers .= "^FT570,200^A0N,22,22^FH\^CI28^FD".$data->marca."^FS^CI28";
        }

        if ($boolLote){
            $headers .= "^FT470,230'^ARN,1^FH\^CI28^FDLote:^FS^CI28";
            $headers .= "^FT540,230^A0N,24,24^FH\^CI28^FD".$data->lote."^FS^CI28";
        }

        if ($boolNumPiezas){
            $headers .= "^FT470,260'^ARN,1^FH\^CI28^FDPiezas:^FS^CI28";
            $headers .= "^FT570,260^A0N,24,24^FH\^CI28^FD".$data->num_piezas."^FS^CI28";
        }

        return $headers;

        // return "^FT470,170^ARN,1^FH\^CI28^FDPeso:^FS^CI28
        //             ^FT470,200^ARN,1^FH\^CI28^FDMarca:^FS^CI28
        //             ^FT470,230'^ARN,1^FH\^CI28^FDLote:^FS^CI28
        //             ^FT470,260'^ARN,1^FH\^CI28^FDPiezas:^FS^CI28
        //             ^FT570,170^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28
        //             ^FT570,200^A0N,22,22^FH\^CI28^FD".$data->marca."^FS^CI28
        //             ^FT540,230^A0N,24,24^FH\^CI28^FD".$data->lote."^FS^CI28
        //             ^FT570,260^A0N,24,24^FH\^CI28^FD".$data->num_piezas."^FS^CI28";

    }

    public static function GetDataLoteEtiquetaInterna($data){
        return DB::table('lot_programaciones')
            ->select(
                'lotes.id as lote',
                'lotes.marca as marca',
                'lotes.fecha_sacrificio as fecha_sacrificio',
                'lot_programaciones.fecha_desposte as fecha_desposte',
                'lotes.producto_aprobado',
                'lotes.producto_empacado',
            )
            ->join('lotes','lotes.id', '=', 'lot_programaciones.lote_id')
            ->where('lot_programaciones.id','=', $data->prog_lotes_id)
            ->get();
    }

    public static function GetDataProductoEtiquetaInterna($data){
        return DB::table('productos')
            ->select(
                'productos.nombre As producto',
                'prod_grupos.registro_sanitario as registro_sanitario',
                'prod_grupos.nombre as grupo',
                'prod_subgrupos.nombre as subgrupo_nombre',
                'prod_subgrupos.encabezado_etiqueta',
                'prod_vencimientos.dias_vencimiento as dias_vencimiento',
            )
            ->join('prod_vencimientos','prod_vencimientos.producto_id', '=', 'productos.id')
            ->join('prod_subgrupos','prod_subgrupos.id', '=', 'productos.prod_subgrupo_id')
            ->join('prod_grupos','prod_grupos.id', '=', 'prod_subgrupos.prodGrupo_id')
            ->where('productos.id','=', $data->producto_id)
            ->where('prod_vencimientos.prodAlmacenamiento_id','=', $data->prodAlmacenamiento_id)
            ->get();
    }
}
