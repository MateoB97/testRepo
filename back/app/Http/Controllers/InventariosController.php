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
use App\FacPivotAlmacenamientoLoteTercero;

class InventariosController extends Controller
{
    public function index()
    {
        $tipo_invent = 1;
        $list = Inventario::todosConCodigoProducto($tipo_invent);

        foreach ($list as $value) {
            $value->cantidad_cierre = 0;
        }

        return $list;

    }

    public function inventarioProduccion()
    {
        $index= Inventario::todosConDatosProduccion();
        return $index;
    }

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
                $item->cantidad = str_replace('=', ' ', strval($item->cantidad));
                $item->cantidad = floatval($item->cantidad);
                $item->estado = 1;
                $item->costo_promedio = 1;
                $item->tipo_invent = 2;
                $item->save();

                $prodTerminado = new ProductoTerminado($request->all());
                $prodTerminado->invent_id = $item->id;
                $prodTerminado->num_piezas = $request->num_piezas;
                $prodTerminado->marinado = $request->marinado;

                if ($lote->producto_empacado) {
                    // // $dias_vencimiento = ProdVencimiento::where('producto_id','=',$request->producto_id)->where('prodAlmacenamiento_id','=',$request->prodAlmacenamiento_id)->get();
                    $prodTerminado->almacenamiento = ProdAlmacenamiento::find($request->prodAlmacenamiento_id)->nombre;
                    $prodTerminado->fecha_vencimiento = str_replace('/','-',$request->fecha_vencimiento);
                    // // $prodTerminado->dias_vencimiento = $dias_vencimiento[0]->dias_vencimiento;
                    // $prodTerminado->almacenamiento = 0;
                    $prodTerminado->dias_vencimiento = 0;
                } else {
                    $prodTerminado->fecha_vencimiento = null;
                    $dias_vencimiento = ProdVencimiento::where('producto_id','=',$request->producto_id)->where('prodAlmacenamiento_id','=',$request->prodAlmacenamiento_id)->get();
                    $prodTerminado->almacenamiento = ProdAlmacenamiento::find($request->prodAlmacenamiento_id)->nombre;
                    $prodTerminado->dias_vencimiento = $dias_vencimiento[0]->dias_vencimiento;
                }

                $prodTerminado->save();

                // $this->imprimirEtiqueta($request->impresora, $item, $request->marinado);
                $response = GenEtiqueta::imprimirEtiqueta($request->impresora, $item, $request->marinado);

                return $response;
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
        $marinado = ProductoTerminado::where('invent_id',$request->etiqueta)->get()->first();
        // $r = self::imprimirEtiqueta($request->impresora, $item, $request->marinado);
        $r = GenEtiqueta::imprimirEtiqueta($request->impresora, $item, $marinado->marinado);
        // $r = $this->imprimirEtiqueta($request->impresora, $item, $request->marinado);

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
        $data = LotEtiquetaInterna::getEtiquetasImpresas($idproducto, $idprogramacion);

        if (is_null($data[0]->cantidad)){
            $data[0]->cantidad = 0;
        }

        return $data[0]->cantidad;
    }

    public function GetProductosPorLotePDF()
    {

        $idlote = $_GET['lote_id'];

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

    public function imprimirEtiquetaInterna(Request $request)
    {

        $eti_interna = new LotEtiquetaInterna;
        $eti_interna->prog_lotes_id = $request->prog_lotes_id;
        $eti_interna->reimpresion = $request->reimpresion;
        $eti_interna->producto_id = $request->producto_id;
        $eti_interna->cantidad = $request->numEtiquetas;

        for ($i = 0; $i < $request->numEtiquetas ; $i++) {
        
            $etiqueta = GenEtiqueta::imprimirEtiqueta($request->impresora, $request, $request->marinado, true);

        }

        $eti_interna->save();

        if (strtoupper($request->impresora) != 'MANUAL') {
            return 'doneNoRestore';
        } else {
            return $etiqueta;
        }
    }
}
