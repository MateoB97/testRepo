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


    public function imprimirEtiqueta ($impresora, $item, $marinado) {

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

        if ($almaRefrigerado !== false) { $almace = "^FT324,326^A0N,25,24^FH\^FDMANTENGASE REFRIGERADO DE 0\F8C A 4\F8C^FS"; }
        if ($almaCongelado !== false) { $almace = "^FT324,326^A0N,25,24^FH\^FDMANTENGASE CONGELADO A -18\F8C ^FS"; }

        if ($data->grupo !== 'Res') { $porcMarinado = "10%"; }
        if ($data->grupo !== 'Cerdo') { $porcMarinado = "12%"; }

        $titulo = "CARNE DE ".strtoupper($data->grupo);

        $proceso = "^FT117,42^A0N,31,31^FH\^FDDESPOSTADO POR:  ".strtoupper($empresa->razon_social)."^FS";

        if ($marinado && $lote->producto_empacado) {
            $proceso = "^FT117,42^A0N,31,31^FH\^FDPROCES. Y DISTR. POR:  ".strtoupper($empresa->razon_social)."^FS";
        } elseif ($lote->producto_empacado) {
            $proceso = "^FT117,42^A0N,31,31^FH\^FDDISTRIBUIDO POR:  ".strtoupper($empresa->razon_social)."^FS";
        }

        $etiqueta = "CT~~CD,~CC^~CT~
                ^XA~TA000~JSN^LT0^MNW^MTD^PON^PMN^LH0,0^JMA^PR4,4~SD15^JUS^LRN^CI0^XZ
                ^XA
                ^MMT
                ^PW799
                ^LL0639
                ^LS0
                ".$proceso."
                ^FT199,82^A0N,25,24^FH\^FD".$empresa->municipio." ".$empresa->direccion." Tel ".$empresa->telefono."^FS";

        if ($marinado) {
            $etiqueta .= "^FT256,119^A0N,25,24^FH\^FDReg. RSA^FS
            ^FT401,120^A0N,28,28^FH\^FD".$data->registro_sanitario."^FS";
        }

        $etiqueta .= "^FO1,135^GB798,0,3^FS
                ^FT72,239^A0N,39,38^FH\^FD".strtoupper($data->producto)."^FS
                ^FT543,239^A0N,39,38^FH\^FD".$data->peso."^FS
                ^FT650,240^A0N,39,38^FH\^FDKg^FS
                ^FT328,286^A0N,25,24^FH\^FDREQUIERE COCCI\E3N ANTES DE CONSUMIR^FS";

        if ($lote->producto_empacado) {
            $etiqueta .= "^FT28,469^A0N,25,24^FH\^FDFecha Vencimiento:^FS";
        } else {
            $etiqueta .= $almace."^FT30,372^A0N,25,24^FH\^FDFecha Sacrificio:^FS
                ^FT29,403^A0N,25,24^FH\^FDFecha Desposte:^FS
                ^FT29,435^A0N,25,24^FH\^FDFecha Empaque:^FS
                ^FT28,469^A0N,25,24^FH\^FDFecha Vencimiento:^FS
                ^FO382,349^GB0,139,2^FS";
        }

        if ($marinado){

            $titulo = "CARNE DE ".strtoupper($data->grupo)." MARINADA";

            $etiqueta .= "^FT400,370^A0N,28,28^FH\^FDINGREDIENTES:^FS
                ^FT399,392^A0N,20,19^FH\^FDCarne marinada al ".$porcMarinado." por inyecci\A2n, agua,^FS
                ^FT399,416^A0N,20,19^FH\^FDsalmuera (Sal), tripolifosfato de sodio E451^FS
                ^FT399,440^A0N,20,19^FH\^FD(Emulsificador), fosfato de sodio 450 ^FS
                ^FT399,464^A0N,20,19^FH\^FD(Estabilizante), fosfato tricalcico E341 ^FS
                ^FT399,488^A0N,20,19^FH\^FD(Estabilizante) menor al 1%.^FS";
        }

        if ($lote->producto_empacado) {
            $etiqueta .= "^FT231,467^A0N,25,24^FH\^FD".$data->fecha_vencimiento."^FS";
        } else {
            $etiqueta .= "^FT232,372^A0N,25,24^FH\^FD".$data->fecha_sacrificio."^FS
                    ^FT232,402^A0N,25,24^FH\^FD".$data->fecha_desposte."^FS
                    ^FT232,434^A0N,25,24^FH\^FD".$data->fecha_empaque."^FS
                    ^FT231,467^A0N,25,24^FH\^FD".$data->fecha_vencimiento."^FS";
        }

        $etiqueta .= "^BY4,3,68^FT263,571^BCN,,Y,N
                ^FD>:".$item->id."^FS
                ^FT33,295^A0N,25,24^FH\^FDMarca:^FS
                ^FT33,331^A0N,25,24^FH\^FDLote:^FS
                ^FT109,296^A0N,28,28^FH\^FD".$data->marca."^FS
                ^FT109,332^A0N,28,28^FH\^FD".$data->lote."^FS
                ^FT254,183^A0N,28,28^FH\^FD".$titulo."^FS
                ^PQ1,0,1,Y^XZ";

        $printer->text($etiqueta);

        $printer->close();
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

        for ($i = 0; $i < $request->numEtiquetas ; $i++) {

            $eti_interna = new LotEtiquetaInterna;
            $eti_interna->prog_lotes_id = $programacion->id;
            $eti_interna->reimpresion = $request->reimpresion;
            $eti_interna->producto_id = $producto->id;

            $almaRefrigerado = strrpos($almacenamiento, "Refrigerado");
            $almaCongelado = strrpos($almacenamiento, "Congelado");

            if ($data_grupo !== 'Res') { $porcMarinado = "10%"; }
            if ($data_grupo !== 'Cerdo') { $porcMarinado = "12%"; }

            if ($almaRefrigerado !== false) { $almace = "^FT324,395^A0N,25,24^FH\^FDMANTENGASE REFRIGERADO DE 0\F8C A 4\F8C^FS"; }
            if ($almaCongelado !== false) { $almace = "^FT324,395^A0N,25,24^FH\^FDMANTENGASE CONGELADO A -18\F8C ^FS"; }

            if ($request->marinado) {
                $textoMarinado = "^FT256,119^A0N,25,24^FH\^FDReg. RSA^FS
                                  ^FT401,120^A0N,28,28^FH\^FD".$data_registro_sanitario."^FS";
                $tituloMarinado = " MARINADA";
                $ingredientesMarinado  = "^FT400,438^A0N,28,28^FH\^FDINGREDIENTES:^FS
                ^FT399,461^A0N,20,19^FH\^FDCarne marinada al ".$porcMarinado." por inyecci\A2n, agua,^FS
                ^FT399,485^A0N,20,19^FH\^FDsalmuera (Sal), tripolifosfato de sodio E451^FS
                ^FT399,509^A0N,20,19^FH\^FD(Emulsificador), fosfato de sodio 450 ^FS
                ^FT399,533^A0N,20,19^FH\^FD(Estabilizante), fosfato tricalcico E341 ^FS
                ^FT399,557^A0N,20,19^FH\^FD(Estabilizante) menor al 1%.^FS";
            } else {
                $textoMarinado = "";
                $tituloMarinado = "";
                $ingredientesMarinado = "";
            }

            $titulo = "CARNE DE ".strtoupper($data_grupo).$tituloMarinado;

            $etiqueta = "CT~~CD,~CC^~CT~
                ^XA~TA000~JSN^LT0^MNW^MTD^PON^PMN^LH0,0^JMA^PR4,4~SD15^JUS^LRN^CI0^XZ
                ^XA
                ^MMT
                ^PW799
                ^LL0639
                ^LS0
                ^FT117,42^A0N,31,31^FH\^FDDESPOSTADO POR: ".strtoupper($empresa->razon_social)."^FS
                ^FT199,82^A0N,25,24^FH\^FD".$empresa->municipio." ".$empresa->direccion." Tel ".$empresa->telefono."^FS"
                .$textoMarinado.
                "^FO1,135^GB798,0,3^FS
                ^FT250,262^A0N,42,40^FH\^FD".strtoupper($data_producto)."^FS
                ^FT328,354^A0N,25,24^FH\^FDREQUIERE COCCI\E3N ANTES DE CONSUMIR^FS"
                .$almace.
                "^FT30,441^A0N,25,24^FH\^FDFecha Sacrificio:^FS
                ^FT29,472^A0N,25,24^FH\^FDFecha Desposte:^FS
                ^FT29,504^A0N,25,24^FH\^FDFecha Empaque:^FS
                ^FT28,538^A0N,25,24^FH\^FDFecha Vencimiento:^FS
                ^FO382,417^GB0,140,2^FS"
                .$ingredientesMarinado.
                "^FT232,440^A0N,25,24^FH\^FD".$data_fecha_sacrificio."^FS
                ^FT232,471^A0N,25,24^FH\^FD".$data_fecha_desposte."^FS
                ^FT232,502^A0N,25,24^FH\^FD".$data_fecha_empaque."^FS
                ^FT231,536^A0N,25,24^FH\^FD".$data_fecha_vencimiento."^FS
                ^FT33,364^A0N,25,24^FH\^FDMarca:^FS
                ^FT33,399^A0N,25,24^FH\^FDLote:^FS
                ^FT109,365^A0N,28,28^FH\^FD".$data_marca."^FS
                ^FT109,401^A0N,28,28^FH\^FD".$data_lote."^FS
                ^FT254,183^A0N,28,28^FH\^FD".$titulo."^FS
                ^PQ1,0,1,Y^XZ";
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

    public function reimprimir (Request $request) {

        $item = Inventario::find($request->etiqueta);

        $this->imprimirEtiqueta($request->impresora, $item);

        return 'done';
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


}
