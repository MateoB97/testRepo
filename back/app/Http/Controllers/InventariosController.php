<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Inventario;
use App\ProductoTerminado;
use Carbon\Carbon;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use App\SalPivotInventSalida;
use App\ProdVencimiento;
use App\Producto;
use App\LotProgramacion;
use App\ProdAlmacenamiento;
use App\Lote;
use App\LotEtiquetaInterna;
use PDF;
use App\GenEmpresa;
use App\GenEtiqueta;
use App\GenMunicipio;
use App\GenDepartamento;

class InventariosController extends Controller
{
    public function index()
    {
        $list = Inventario::todosConCodigoProducto();

        return $list;

    }

    public function inventarioProduccion()
    {
        $index= Inventario::todosConDatos();
        return $index;
    }

    public function inventarioVentas()
    {
        $list = Inventario::todosConCodigoProducto();

        return $list;

    }

    //     public function index()
    // {
    //     $index= Inventario::todosConDatos();
    //     return $index;
    // }

    public function indexFilter($lote,$fecha_ini,$fecha_fin,$producto_id,$estado)
    {
        $lote = ($lote == 'null') ? false : $lote;
        $producto_id = ($producto_id == 'null') ? false : $producto_id;
        if ($fecha_ini != 'null') {
            $date= explode('-', $fecha_ini);
            $fecha_ini = $date[2].'/'.$date[1].'/'.$date[0];
        } else {
            $fecha_ini = false;
        }
        if ($fecha_fin != 'null') {
            $date = explode('-', $fecha_fin);
            $fecha_fin = $date[2].'/'.$date[1].'/'.$date[0];
        } else {

            $fecha_fin = false;
        }
        if ($estado != 'null') {
            if ($estado == 1) {
                $estado = array(1,2);
            } elseif ($estado == 0) {
                $estado = array(0,3);
            }
        } else {
            $estado = false;
        }

        $index= Inventario::todosConDatosFilter($lote,$fecha_ini,$fecha_fin,$producto_id,$estado);
        return $index;
    }

    public function store(Request $request)
    {
        $validateProgramacion = LotProgramacion::find($request->prog_lotes_id);
        $lote = Lote::find($validateProgramacion->lote_id);

        if ($validateProgramacion->estado != 2) {
            if (count(ProdVencimiento::where('producto_id','=',$request->producto_id)->where('prodAlmacenamiento_id','=',$request->prodAlmacenamiento_id)->get()) > 0 || $lote->producto_empacado) {

                $item = new Inventario($request->all());
                $item->estado = 1;
                $item->costo_promedio = 1;
                $item->tipo_invent = 2;
                $item->save();

                $prodTerminado = new ProductoTerminado($request->all());
                $prodTerminado->invent_id = $item->id;

                if ($lote->producto_empacado) {
                    $prodTerminado->almacenamiento = 0;
                    $prodTerminado->dias_vencimiento = 0;
                } else {
                    $dias_vencimiento = ProdVencimiento::where('producto_id','=',$request->producto_id)->where('prodAlmacenamiento_id','=',$request->prodAlmacenamiento_id)->get();
                    $prodTerminado->almacenamiento = ProdAlmacenamiento::find($request->prodAlmacenamiento_id)->nombre;
                    $prodTerminado->dias_vencimiento = $dias_vencimiento[0]->dias_vencimiento;
                }

                $prodTerminado->save();

                $this->imprimirEtiqueta($request->impresora, $item, $request->marinado);

                return 'doneNoRestore';
            } else {
                return 'Error: Este producto no tiene este tipo de almacenamiento registrado';
            }
        } else {
            return 'La programacion se encuentra cerrada.';
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = Inventario::find($id);

        $model->producto_id = $model->producto;
        $model->prodSubgrupo_id = $model->producto->prodSubgrupo;
        $model->prodGrupo_id = $model->producto->prodSubgrupo->prodGrupo;

        return $model;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = Inventario::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $validacion = SalPivotInventSalida::where('inventario_id',$id)->get();

        if (count($validacion) > 0) {
            return 'El producto ya ha sido despachado: No se puede eliminar';
        } else {

            $model = Inventario::find($id);
            $prod_terminado = ProductoTerminado::where('invent_id',$id);

            $val2 = $prod_terminado->delete();
            $val1 = $model->delete();

            if ($val1 && $val2) {
                return 'done';
            } else {
                return 'error';
            }
        }

    }

    public function reimprimir (Request $request) {

        $item = Inventario::find($request->etiqueta);

        // $r = self::imprimirEtiqueta($request->impresora, $item, $request->marinado);
        $r = GenEtiqueta::imprimirEtiqueta($request->impresora, $item, $request->marinado);

        return $r;
    }

    public function GetInfoSalMercancia($id)
    {
        $verify = SalPivotInventSalida::where('inventario_id','=', $id)->get();
        if ( count($verify) < 1) {
            $data = Inventario::GetDataSalMercancia($id);
            return $data;
        } else {
            return 'error';
        }
    }

    public function dividir(Request $request)
    {


        $validacion = SalPivotInventSalida::where('inventario_id',$request->canasta)->get();

        if (count($validacion) > 0) {
            return array('error','El producto ya ha sido despachado: No se puede eliminar');
        } else {

            $existente  = Inventario::find($request->canasta);

            $prodTerminadoExistente = ProductoTerminado::where('invent_id',$request->canasta)->get()->first();

            $item1 = new Inventario();
            $item1->cantidad = $request->cantidad;
            $item1->producto_id = $existente->producto_id;
            $item1->costo_promedio = 1;
            $item1->estado = 1;
            $item1->tipo_invent = 2;
            $item1->save();

            $item2 = new Inventario();
            $item2->cantidad = floatval($existente->cantidad) - floatval($request->cantidad);
            $item2->producto_id = $existente->producto_id;
            $item2->costo_promedio = 1;
            $item2->estado = 1;
            $item2->tipo_invent = 2;
            $item2->save();

            $prodTerminado1 = new ProductoTerminado();
            $prodTerminado1->invent_id = $item1->id;
            $prodTerminado1->prog_lotes_id = $prodTerminadoExistente->prog_lotes_id;
            $prodTerminado1->almacenamiento = $prodTerminadoExistente->almacenamiento;
            $prodTerminado1->dias_vencimiento = $prodTerminadoExistente->dias_vencimiento;
            $prodTerminado1->save();

            $prodTerminado2 = new ProductoTerminado();
            $prodTerminado2->invent_id = $item2->id;
            $prodTerminado2->prog_lotes_id = $prodTerminadoExistente->prog_lotes_id;
            $prodTerminado2->almacenamiento = $prodTerminadoExistente->almacenamiento;
            $prodTerminado2->dias_vencimiento = $prodTerminadoExistente->dias_vencimiento;
            $prodTerminado2->save();

            $prod1 = ProductoTerminado::find($prodTerminado1->id);
            $prod1->created_at = $prodTerminadoExistente->created_at;
            $prod1->save();

            $prod2 = ProductoTerminado::find($prodTerminado2->id);
            $prod2->created_at = $prodTerminadoExistente->created_at;
            $prod2->save();

            $prodTerminadoExistente->delete();
            $existente->delete();


            return array('done', $item1->id, $item2->id);
        }
    }

    public function GetDataExistentes($idproducto, $idprogramacion)
    {
        $data = Inventario::GetDataExistentes($idproducto, $idprogramacion);

        return $data;
    }

    public function GetPiezasImpresas($idprogramacion, $idproducto)
    {
        $data = LotEtiquetaInterna::where('producto_id',$idproducto)->where('prog_lotes_id',$idprogramacion)->get();

        $counter = count($data);

        return $counter;
    }

    public function GetProductosPorLotePDF($idlote)
    {
        $data = Inventario::productosPorLote($idlote);

        $itemsSumatoria = array();
        $flag = 0;

        foreach ($data as $element) {

            $flag = 0;

            $element->peso = number_format($element->peso, 3, '.', '');

            foreach ($itemsSumatoria  as $elementSumatoria) {
                if ($element->producto_id == $elementSumatoria->producto_id) {
                    $elementSumatoria->peso = $elementSumatoria->peso + $element->peso;
                    $flag = 1;
                }
            }

            if ($flag != 1) {
                array_push($itemsSumatoria, $element);
            }
        }


        $data = ['itemsSumatoria' => $itemsSumatoria, 'lote' => $idlote];
        $pdf = PDF::loadView('informes.totalProductosLote', $data);

        // return view('certificados.pdf');

        return $pdf->stream();
    }

    public static function imprimirEtiqueta($impresora, $item, $marinado) {

        $marinado = true;
        $almace ='';
        $empresa = GenEmpresa::find(1);
        $empresa->municipio = GenMunicipio::find($empresa->gen_municipios_id)->nombre;
        $empresa->departamento = GenDepartamento::find(GenMunicipio::find($empresa->gen_municipios_id)->departamento_id)->nombre;
        // $nombre_impresora = str_replace('SMB', 'smb', strtoupper($impresora));
        // $connector = new WindowsPrintConnector($nombre_impresora);
        // $printer = new Printer($connector);
        // $printer->setJustification(Printer::JUSTIFY_CENTER);
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
        if ($marinado && $lote->producto_empacado && $data->encabezado_etiqueta >0) {
            $proceso = "^FT50,550^A0N,30^FH\^CI28^FDPROCESADO Y DISTRIBUIDO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";
        } elseif ($lote->producto_empacado || $data->encabezado_etiqueta == 0) {
            $proceso = "^FT130,550^A0N,30^FH\^CI28^FDDISTRIBUIDO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";
        }
        $etiqueta = "
                ^XA
                ^CF0,80
                ^CI28
                ".$proceso."
                ^FT140,590^ARN,24,24^FH\^CI28^FD".$empresa->municipio." ".$empresa->direccion." Tel ".$empresa->telefono."^FS^^CI28";
        if($data->producto_aprobado != null && !$lote->producto_empacado && !$marinado){
            $etiqueta .= "^FT580,300^A0N,24,24^FH\^CI28^FDAprobado^FS^CI28";
        }elseif($data->producto_aprobado != null && $lote->producto_empacado){
            $etiqueta .= "^FT200,260^A0N,30,30^FH\^CI28^FD|Aprobado^FS^CI28";
        }
        if(!$lote->producto_empacado){// LOTES JH
            if(!$marinado){
                $etiqueta .= "
                    ".$titulo."
                    ^FPH,1^FT27,170^A0N,50,50^FD".strtoupper(eliminar_acentos($data->producto))."^FS^CI28
                    ^FT430,220^ARN,1^FH\^CI28^FDPeso:^FS^CI28
                    ^FT430,260^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                    ^FT430,300^ARN,1^FH\^CI28^FDLote:^FS^CI28
                    ^FT520,220^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28
                    ^FT515,260^A0N,22,22^FH\^CI28^FD".$data->marca."^FS^CI28
                    ^FT510,300^A0N,24,24^FH\^CI28^FD".$data->lote."^FS^CI28
                    ^FT20,350^A0N,22,22^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                    ".self::logoEtiqueta()."
                    ^BY3,3,80^FT250,480^BCN,,Y,N
                    ^FH\^FD>:".$item->id."^FS
                    ^FT30,225^ARN,1^FH\^CI28^FDFecha Sacrificio:^FS^CI28
                    ^FT30,250^ARN,5,5^FH\^CI28^FDFecha empaque:^FS^CI28
                    ^FT30,275^ARN,5,5^FH\^CI28^FDFecha Desposte:^FS^CI28
                    ^FT30,300^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT235,225^A0N,24,24^FH\^CI28^FD".$data->fecha_sacrificio."^FS^CI28
                    ^FT235,250^A0N,24,24^FH\^CI28^FD".$data->fecha_empaque."^FS^CI28
                    ^FT235,275^A0N,24,24^FH\^CI28^FD".$data->fecha_desposte."^FS^CI28
                    ^FT270,300^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28";
            }else{
                $etiqueta .= "
                    ".$titulo."
                    ^FPH,1^FT30,126^A0N,43,43^FH\^FD".strtoupper(eliminar_acentos($data->producto))."^FS^CI28
                    ^FPH,1^FT30,155^ARN,27,27^FH\^FDReg. RSA  ".$data->registro_sanitario."^FS^CI28
                    ^FT430,180^ARN,1^FH\^CI28^FDPeso:^FS^CI28
                    ^FT430,210^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                    ^FT430,235^ARN,1^FH\^CI28^FDLote:^FS^CI28
                    ^FT515,180^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28
                    ^FT515,210^A0N,22,22^FH\^CI28^FD".$data->marca."^FS^CI28
                    ^FT515,235^A0N,24,24^FH\^CI28^FD".$data->lote."^FS^CI28
                    ^FT35,285^A0N,20,20^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                    ^FT300,320^A0N,28,28^FH\^CI28^FDINGREDIENTES:^FS^CI28
                    ^FT25,340^A0N,23,23^FH\^CI28^FDCarne marinada al " .$porcMarinado." por inyecci\A2n, agua, salmuera (Sal), tripolifosfato de sodio E451^FS^CI28
                    ^FT25,360^A0N,23,23^FH\^CI28^FD(Emulsificador), fosfato de sodio 450 (Estabilizante), fosfato tricalcico E341 ^FS^CI28
                    ^FT25,380^A0N,23,23^FH\^CI28^FD(Estabilizante) menor al 1%.^FS^CI28
                    ".self::logoEtiqueta()."
                    ^BY3,3,80^FT250,480^BCN,,Y,N
                    ^FH\^FD>:".$item->id."^FS
                    ^FT30,185^ARN,1^FH\^CI28^FDFecha Sacrificio:^FS^CI28
                    ^FT30,210^ARN,5,5^FH\^CI28^FDFecha empaque:^FS^CI28
                    ^FT30,235^ARN,5,5^FH\^CI28^FDFecha Desposte:^FS^CI28
                    ^FT30,260^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT235,185^A0N,24,24^FH\^CI28^FD".$data->fecha_sacrificio."^FS^CI28
                    ^FT235,210^A0N,24,24^FH\^CI28^FD".$data->fecha_empaque."^FS^CI28
                    ^FT235,235^A0N,24,24^FH\^CI28^FD".$data->fecha_desposte."^FS^CI28
                    ^FT270,260^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28
                    ^PQ1,0,1,Y
                    ^XZ";
            }
        }else{
            if(!$marinado){// LOTES TERCEROS
                $etiqueta .= "
                    ".self::logoEtiqueta()."
                    ^FT440,220^ARN,1^FH\^CI28^FDPeso:^FS^CI28
                    ^FT440,260^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                    ^FT30,260^ARN,1^FH\^CI28^FDLote:^FS^CI28
                    ^FT530,220^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28
                    ^FT525,260^A0N,30,30^FH\^CI28^FD".$data->marca."^FS^CI28
                    ^FT120,260^A0N,30,30^FH\^CI28^FD".$data->lote."^FS^CI28
                    ^FT40,325^A0N,30,30^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                    ^FT30,220^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT270,220^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28
                    ".$titulo."
                    ^FPH,1^FT30,145^A0N,50,50^FH\^FD".strtoupper(eliminar_acentos($data->producto))."^FS^CI28
                    ^BY3,3,80^FT250,440^BCN,,Y,N
                    ^FH\^FD>:".$item->id."^FS";
        }else{
            $etiqueta .= "
                ".self::logoEtiqueta()."
                ^FT300,320^A0N,28,28^FH\^CI28^FDINGREDIENTES:^FS^CI28
                ^FT25,340^A0N,23,23^FH\^CI28^FDCarne marinada al " .$porcMarinado." por inyeccion, agua, salmuera (Sal), tripolifosfato de sodio E451^FS^CI28
                ^FT25,360^A0N,23,23^FH\^CI28^FD(Emulsificador), fosfato de sodio 450 (Estabilizante), fosfato tricalcico E341 ^FS^CI28
                ^FT25,380^A0N,23,23^FH\^CI28^FD(Estabilizante) menor al 1%.^FS^CI28
                ^FT430,220^ARN,1^FH\^CI28^FDPeso:^FS^CI28
                ^FT430,260^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                ^FT30,260^ARN,1^FH\^CI28^FDLote:^FS^CI28
                ^FT520,220^A0N,40,40^FH\^CI28^FD".$data->peso."^FS^CI28
                ^FT515,260^A0N,22,22^FH\^CI28^FD".$data->marca."^FS^CI28
                ^FT120,260^A0N,24,24^FH\^CI28^FD".$data->lote."^FS^CI28
                ^FT30,285^A0N,22,22^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                ^FT30,220^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                ^FT270,220^A0N,24,24^FH\^CI28^FD".$data->fecha_vencimiento."^FS^CI28
                ".$titulo."
                ^FPH,1^FT27,145^A0N,50,50^FH\^FD".strtoupper(eliminar_acentos($data->producto))."^FS^CI28
                ^FPH,1^FT30,175^ARN,27,27^FH\^FDReg. RSA  ".$data->registro_sanitario."^FS^CI28
                ^BY3,3,80^FT250,480^BCN,,Y,N
                ^FH\^FD>:".$item->id."^FS";
          }
        }
            $etiqueta .="
                ^XZ";
        // $printer->text($etiqueta);
        // $printer->close();

                return $etiqueta;
    }

    public function imprimirEtiquetaInterna(Request $request)
    {
        $empresa = GenEmpresa::find(1);
        $empresa->municipio = GenMunicipio::find($empresa->gen_municipios_id)->nombre;
        $empresa->departamento = GenDepartamento::find(GenMunicipio::find($empresa->gen_municipios_id)->departamento_id)->nombre;
        $producto = Producto::find($request->producto_id);
        $data_producto = $producto->nombre;
        $data_grupo = $producto->prodSubgrupo->prodGrupo->nombre;
        $data_registro_sanitario = $producto->prodSubgrupo->prodGrupo->registro_sanitario;
        $data_fecha_empaque = Carbon::now()->toDateString();
        $programacion = LotProgramacion::find($request->prog_lotes_id);
        $data_fecha_desposte = $programacion->fecha_desposte;
        $data_lote = $programacion->lote_id;
        $data_marca = $programacion->lote->marca;
        $data_fecha_sacrificio = $programacion->lote->fecha_sacrificio;

        $almacenamiento = ProdAlmacenamiento::find($request->prodAlmacenamiento_id);

        $dias_vencimiento = ProdVencimiento::where('producto_id','=',$request->producto_id)->where('prodAlmacenamiento_id','=',$request->prodAlmacenamiento_id)->get();

        $data_fecha_vencimiento = Carbon::parse($data_fecha_sacrificio)->addDays($dias_vencimiento[0]->dias_vencimiento)->toDateString();
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper($request->impresora));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $almace="";
        for ($i = 0; $i < $request->numEtiquetas ; $i++) {

            $eti_interna = new LotEtiquetaInterna;
            $eti_interna->prog_lotes_id = $programacion->id;
            $eti_interna->reimpresion = $request->reimpresion;
            $eti_interna->producto_id = $producto->id;

            $almaRefrigerado = strrpos($almacenamiento, "Refrigerado");
            $almaCongelado = strrpos($almacenamiento, "Congelado");

            if ($data_grupo !== 'Res') { $porcMarinado = "10%"; }
            if ($data_grupo !== 'Cerdo') { $porcMarinado = "12%"; }

            // if ($almaRefrigerado !== false) { $almace = "^FT324,395^A0N,25,24^FH\^FDMANTENGASE REFRIGERADO DE 0\F8C A 4\F8C^FS"; }
            // if ($almaCongelado !== false) { $almace = "^FT324,395^A0N,25,24^FH\^FDMANTENGASE CONGELADO A -18\F8C ^FS"; }
            if ($almaRefrigerado !== false) { $almace = "MANTENGASE REFRIGERADO DE 0\F8C A 4\F8C^"; }
            if ($almaCongelado !== false) { $almace = "MANTENGASE CONGELADO A -18\F8C"; }

            // $titulo = "CARNE DE ".strtoupper($data_grupo);
            $titulo =  self::validarTitulo($producto->prodSubgrupo->encabezado_etiqueta, $data_grupo, $request->marinado);
            $proceso = "^FT140,550^ARN,40^FH\^CI28^FDDESPOSTADO POR: ".strtoupper($empresa->razon_social)."^FS^CI28";
            $etiqueta = "
                ^XA~TA000~JSN^LT0^MNW^MTD^PON^PMN^LH0,0^JMA^PR4,4~SD15^JUS^LRN^CI0^XZ
                ^XA
                ^MMT
                ^PW799
                ^LL0639
                ^LS0
                ".$proceso."
                ^FT140,590^ARN,24,24^FH\^CI28^FD".$empresa->municipio." ".$empresa->direccion." Tel ".$empresa->telefono."^FS^CI28";
                if($programacion->lote->producto_aprobado > 0 && !$request->marinado){
                    $etiqueta .= "^FT580,300^A0N,24,24^FH\^CI28^FDAprobado^FS^CI28";
                }elseif($programacion->lote->producto_aprobado > 0 &&  $request->marinado){
                    $etiqueta .= "^FT595,235^A0N,24,24^FH\^CI28^FDAprobado^FS^CI28";
                }
            if (!$request->marinado) {
                $etiqueta .= "
                    ".$titulo."
                    ^FPH,1^FT27,170^A0N,50,50^FH\^FD".strtoupper(eliminar_acentos($data_producto))."^FS^CI28
                    ^FT430,260^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                    ^FT430,300^ARN,1^FH\^CI28^FDLote:^FS^CI28
                    ^FT515,260^A0N,22,22^FH\^CI28^FD".$data_marca."^FS^CI28
                    ^FT510,300^A0N,24,24^FH\^CI28^FD".$data_lote."^FS^CI28
                    ^FT20,395^A0N,20,20^FH\^CI28^FD REQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                    ".self::logoEtiqueta()."
                    ^FT30,225^ARN,1^FH\^CI28^FDFecha Sacrificio:^FS^CI28
                    ^FT30,250^ARN,5,5^FH\^CI28^FDFecha empaque:^FS^CI28
                    ^FT30,275^ARN,5,5^FH\^CI28^FDFecha Desposte:^FS^CI28
                    ^FT30,300^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT235,225^A0N,24,24^FH\^CI28^FD".$data_fecha_sacrificio."^FS^CI28
                    ^FT235,250^A0N,24,24^FH\^CI28^FD".$data_fecha_empaque."^FS^CI28
                    ^FT235,275^A0N,24,24^FH\^CI28^FD".$data_fecha_desposte."^FS^CI28
                    ^FT270,300^A0N,24,24^FH\^CI28^FD".$data_fecha_vencimiento."^FS^CI28";
            }else{
                $etiqueta .= "
                    ".$titulo."
                    ^FPH,1^FT30,126^A0N,43,43^FH\^FD".strtoupper(eliminar_acentos($data_producto))."^FS^CI28
                    ^FPH,1^FT30,155^ARN,27,27^FH\^FDReg. RSA  ".$data_registro_sanitario."^FS^CI28
                    ^FT430,210^ARN,1^FH\^CI28^FDMarca:^FS^CI28
                    ^FT430,235^ARN,1^FH\^CI28^FDLote:^FS^CI28
                    ^FT515,210^A0N,22,22^FH\^CI28^FD".$data_marca."^FS^CI28
                    ^FT515,235^A0N,24,24^FH\^CI28^FD".$data_lote."^FS^CI28
                    ^FT35,285^A0N,20,20^FH\^CI28^FDREQUIERE COCCION ANTES DE CONSUMIR ".$almace."^FS^CI28
                    ^FT300,320^A0N,28,28^FH\^CI28^FDINGREDIENTES:^FS^CI28
                    ^FT25,340^A0N,23,23^FH\^CI28^FDCarne marinada al " .$porcMarinado." por inyecci\A2n, agua, salmuera (Sal), tripolifosfato de sodio E451^FS^CI28
                    ^FT25,360^A0N,23,23^FH\^CI28^FD(Emulsificador), fosfato de sodio 450 (Estabilizante), fosfato tricalcico E341 ^FS^CI28
                    ^FT25,380^A0N,23,23^FH\^CI28^FD(Estabilizante) menor al 1%.^FS^CI28
                    ".self::logoEtiqueta()."
                    ^FT30,185^ARN,1^FH\^CI28^FDFecha Sacrificio:^FS^CI28
                    ^FT30,210^ARN,5,5^FH\^CI28^FDFecha empaque:^FS^CI28
                    ^FT30,235^ARN,5,5^FH\^CI28^FDFecha Desposte:^FS^CI28
                    ^FT30,260^ARN,5,5^FH\^FDFecha Vencimiento:^FS^CI28
                    ^FT235,185^A0N,24,24^FH\^CI28^FD".$data_fecha_sacrificio."^FS^CI28
                    ^FT235,210^A0N,24,24^FH\^CI28^FD".$data_fecha_empaque."^FS^CI28
                    ^FT235,235^A0N,24,24^FH\^CI28^FD".$data_fecha_desposte."^FS^CI28
                    ^FT270,260^A0N,24,24^FH\^CI28^FD".$data_fecha_vencimiento."^FS^CI28
                    ^PQ1,0,1,Y
                    ^XZ";
            }

            $etiqueta .="
            ^PQ1,0,1,Y
            ^XZ";
            $printer->text($etiqueta);

            /*
                Para imprimir realmente, tenemos que "cerrar"
                la conexión con la impresora. Recuerda incluir esto al final de todos los archivos
            */
            $done = $printer->close();
            $eti_interna->save();

        }

        return 'doneNoRestore';
    }

    public static function validarTitulo($encabezado, $grupo, $marinado){
        if($encabezado > 0 ){
            if(!$marinado){
                return "^FPH,1^FT150,80^ARN,60,60^FH\^FDCARNE DE ".strtoupper($grupo)."^FS^CI28";
            }else{
                return "^FPH,1^FT20,80^ARN,60,60^FH\^FDCARNE DE ".strtoupper($grupo)." MARINADA^FS^CI28";
            }
        }else{
            return '^FPH,1^FT180,80^ARN,60,40^FH\^FDPRODUCTO CARNICO COMESTIBLE^FS^CI28';
        }
    }

    public static function logoEtiqueta () {
        return '';
    }
}
