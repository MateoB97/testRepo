<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\GenMunicipio;
use App\GenDepartamento;

class GenEtiqueta extends Model
{


    public static function imprimirEtiqueta($impresora, $item, $marinado) {

        $almace ='';
        $empresa = GenEmpresa::find(1);
        $empresa->municipio = GenMunicipio::find($empresa->gen_municipios_id)->nombre;
        $empresa->departamento = GenDepartamento::find(GenMunicipio::find($empresa->gen_municipios_id)->departamento_id)->nombre;

        $nombre_impresora = str_replace('SMB', 'smb', strtoupper($impresora));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $data = Inventario::GetDataEtiqueta($item->id)->first();
        $lote = Lote::find($data->lote);
        $producto = Producto::where('id','=',$item->producto_id)->get();
        $prodTerminado = ProductoTerminado::where('invent_id',$item->id)->get()->first();
        $data->fecha_vencimiento = Carbon::parse($data->fecha_sacrificio)->addDays($prodTerminado->dias_vencimiento)->toDateString();
        $data->fecha_empaque = Carbon::parse($data->fecha_empaque)->toDateString();
        $data->peso = number_format($data->peso, 3, '.', '');
        $almaRefrigerado = strrpos($prodTerminado->almacenamiento, "Refrigerado");
        $almaCongelado = strrpos($prodTerminado->almacenamiento, "Congelado");
        if ($almaRefrigerado !== false) { $almace = "MANTENGASE REFRIGERADO DE 0\F8C A 4\F"; }
        if ($almaCongelado !== false) { $almace = "MANTENGASE CONGELADO A -18\F8C"; }
        if ($data->grupo !== 'Res') { $porcMarinado = "10%"; }
        if ($data->grupo !== 'Cerdo') { $porcMarinado = "12%"; }
        // $titulo = "CARNE DE ".strtoupper($data->grupo);
        $titulo =  self::validarTitulo($data->encabezado_etiqueta, $data->grupo, $marinado);
        $proceso = "^FT140,550^ARN,40^FH\^CI28^FDDESPOSTADO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";
        if ($lote->producto_empacado || $data->encabezado_etiqueta == 0) {
            $proceso = "^FT130,550^A0N,30^FH\^CI28^FDDISTRIBUIDO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";
        } elseif ($marinado && $lote->producto_empacado && $data->encabezado_etiqueta >0) {//($lote->producto_empacado || $data->encabezado_etiqueta == 0) {
            $proceso = "^FT50,550^A0N,30^FH\^CI28^FDPROCESADO Y DISTRIBUIDO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";//$proceso = "^FT130,550^A0N,30^FH\^CI28^FDDISTRIBUIDO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";
        }
        $etiqueta = "
                ^XA
                ^CF0,80
                ^CI28
                ".$proceso."
                ^FT140,590^ARN,24,24^FH\^CI28^FD".$empresa->municipio." ".$empresa->direccion." Tel ".$empresa->telefono."^FS^^CI28
                ^FPH,1^FT20,80^ARN,53,53^FH\^FD".strtoupper(eliminar_acentos($data->producto))."^FS^CI28"
                .self::headersEtiquetas($data)."
                ^FT35,295^A0N,20,20^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                ^FPH,1^FT30,126^A0N,43,43^FD".$titulo."^FS^CI28"
                .self::logoEtiqueta()."
                ^BY3,3,80^FT250,480^BCN,,Y,N
                ^FH\^FD>:".$item->id."^FS";
        if($data->producto_aprobado != null ){
            $etiqueta .= "^FT610,230^A0N,24,24^FH\^CI28^FD - Aprobado^FS^CI28";
        }

        if(!$lote->producto_empacado){ //LOTES JH

            $etiqueta .= self::fechasEtiqueta($data);

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

        $printer->text($etiqueta);
        $printer->close();

        return $etiqueta;
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

    public static function logoEtiqueta () {
        return '';
    }

    public static function fechasEtiqueta ($data) {

        return "^FT30,185^ARN,1^FH\^CI28^FDFecha Sacrificio:^FS^CI28
                    ^FT30,210^ARN,5,5^FH\^CI28^FDFecha Desposte:^FS^CI28
                    ^FT30,235^ARN,5,5^FH\^CI28^FDFecha Empaque:^FS^CI28
                    ^FT30,260^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT235,185^A0N,24,24^FH\^CI28^FD".$data->fecha_sacrificio."^FS^CI28
                    ^FT235,210^A0N,24,24^FH\^CI28^FD".$data->fecha_desposte."^FS^CI28
                    ^FT235,235^A0N,24,24^FH\^CI28^FD".$data->fecha_empaque."^FS^CI28
                    ^FT270,260^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28";

    }

    public static function marinadoEtiquetas ($porcMarinado) {

        return "^FT300,320^A0N,28,28^FH\^CI28^FDINGREDIENTES:^FS^CI28
                    ^FT25,340^A0N,23,23^FH\^CI28^FDCarne marinada al " .$porcMarinado." por inyecci\A2n, agua, salmuera (Sal), tripolifosfato de sodio E451^FS^CI28
                    ^FT25,360^A0N,23,23^FH\^CI28^FD(Emulsificador), fosfato de sodio 450 (Estabilizante), fosfato tricalcico E341 ^FS^CI28
                    ^FT25,380^A0N,23,23^FH\^CI28^FD(Estabilizante) menor al 1%.^FS^CI28";

    }

    public static function headersEtiquetas ($data) {

        return "^FT470,170^ARN,1^FH\^CI28^FDPeso:^FS^CI28
                    ^FT470,200^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                    ^FT470,230'^ARN,1^FH\^CI28^FDLote:^FS^CI28
                    ^FT470,260'^ARN,1^FH\^CI28^FDPiezas:^FS^CI28
                    ^FT570,170^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28
                    ^FT570,200^A0N,22,22^FH\^CI28^FD".$data->marca."^FS^CI28
                    ^FT540,230^A0N,24,24^FH\^CI28^FD".$data->lote."^FS^CI28
                    ^FT570,260^A0N,24,24^FH\^CI28^FD".$data->num_piezas."^FS^CI28";

    }
}
