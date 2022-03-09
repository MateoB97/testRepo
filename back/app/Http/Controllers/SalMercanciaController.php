<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalMercancia;
use App\SalPivotInventSalida;
use App\SalPivotSalProducto;
use App\ProdVencimiento;
use App\Inventario;
use App\Producto;
use Carbon\Carbon;
use PDF;
use App\GenEmpresa;
use App\GenMunicipio;
use App\GenDepartamento;
use App\TerceroSucursal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class SalMercanciaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = SalMercancia::todosConSucursales();
        return $list;
    }

    public function despachoSinFactura()
    {
        $list = SalMercancia::despachosSinFactura();
        return $list;
    }

    public function despachoSinFacturaParaTraslados()
    {
        $list = SalMercancia::despachoSinFacturaParaTraslados();
        return $list;
    }

    public function store(Request $request) // falta probar
    {

        $visceras = array();
        $cortes = array();
        $congelado = array();

        foreach ($request->datos as $prod) {
            $producto = Producto::find($prod->codigo);
            if ($producto->conservacion === 0) {
                array_push($visceras, $prod);
            } else if ($producto->conservacion === 1) {
                array_push($cortes, $prod);
            } else if ($producto->conservacion === 2) {
                array_push($congelado, $prod);
            }
        }
        if (count($visceras) >= 1 && $request->temperatura_refrigerado >= 5) {
            return 'La temperatura de refrigeración de visceras debe ser menor a 5°';
        } else if (count($cortes) >= 1 && $request->temperatura_refrigerado >= 7) {
            return 'La temperatura de refrigeración de cortes debe ser menor a 7°';
        } else if ($request->temperatura_congelado >= -18) {
            return 'La temperatura de congelación debe ser menor a -18°';
        } else if ( ((count($visceras) >= 1 || count($cortes) >= 1) && gettype($request->temperatura_refrigerado) !== 'NULL')
        || (count($congelado) >=1 && gettype($request->temperatura_congelado) !== 'NULL')) {
            $nuevoItem = new SalMercancia($request->all());
            $lastConsect =  intval(SalMercancia::max('consecutivo')) +1 ;
            $nuevoItem->consecutivo = $lastConsect;
            $nuevoItem->save();

            foreach ($request->items as $item) {
                $prod = new SalPivotInventSalida;
                $prod->inventario_id = $item;
                $prod->salMercancia_id = $nuevoItem->id;
                $prod->save();

                $inv = Inventario::find($item);
                $modificacion1 = ($inv->estado == 2) ? $inv->estado = 3 : '';
                $modificacion2 = ($inv->estado == 1) ? $inv->estado = 0 : '';
                $inv->save();
            }

            foreach ($request->datos as $item) {
                $nuevoPeso = new SalPivotSalProducto($item);
                $nuevoPeso->sal_mercancia_id = $nuevoItem->id;
                $nuevoPeso->cantidad = $item['peso_despacho'];
                $nuevoPeso->save();
            }
            return 'done';
        }


        if ($request->temperatura_congelado <= -18 ) {
            $this->salMercSave($request);

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $item = SalMercancia::find($id);

        $itemsPivot = SalPivotInventSalida::where('salMercancia_id',$id)->get();

        foreach ($itemsPivot as $pivot) {

            $invent = Inventario::find($pivot->inventario_id);
            $modificacion1 = ($invent->estado == 3) ? $invent->estado = 2 : '';
            $modificacion2 = ($invent->estado == 0) ? $invent->estado = 1 : '';
            $invent->save();

            $pivot->delete();

        }
        $itemsPivot = SalPivotSalProducto::where('sal_mercancia_id',$id)->get();

        foreach ($itemsPivot as $pivot) {
            $pivot->delete();
        }

        $item->delete();

        return 'done';
    }

    public function generatePDF(Request $request,$id){

        $salMercancia = SalMercancia::find($id);
        // dd($salMercancia);
        $empresa = GenEmpresa::find(1);
        $empresa->municipio = GenMunicipio::find($empresa->gen_municipios_id)->nombre;
        $empresa->departamento = GenDepartamento::find(GenMunicipio::find($empresa->gen_municipios_id)->departamento_id)->nombre;
        // $sucursal = $salMercancia->terceroSucursal;
        $sucursal = TerceroSucursal::getDataSucursal($salMercancia->terceroSucursal_id)->first();
        // dd($sucursal);
        // dd($sucurzal);
        // if ($sucursal->genMunicipio) {
        //     $sucursal->municipio  = $sucursal->genMunicipio->nombre;
        //     $sucursal->departamento  = $sucursal->genMunicipio->genDepartamento->nombre;
        // }

        $tercero = $salMercancia->terceroSucursal->tercero;

        // $items = SalPivotInventSalida::GetDataCertificado($id);
        $items = SalPivotInventSalida::dataCertificado($id);
        $itemsSumatoria = array();
        $flag = 0;
        $totalCanastas = 0;
        $totalKilos = 0;
        foreach ($items as $element) {

            $flag = 0;
            if (!$element->producto_empacado > 0) {
                $element->fecha_empaque = Carbon::parse($element->fecha_empaque)->toDateString();
                $element->fecha_sacrificio = Carbon::parse($element->fecha_sacrificio)->toDateString();
                $element->fecha_vencimiento = Carbon::parse($element->fecha_sacrificio)->addDays($element->vencimiento)->toDateString();
                $element->peso = number_format($element->peso, 2, '.', '');
            } else if ($element->producto_empacado == 1){
                $element->fecha_empaque = Carbon::parse($element->fecha_empaque_lote_tercero)->toDateString();
                $element->fecha_desposte = Carbon::parse($element->fecha_empaque_lote_tercero)->toDateString();
                $element->fecha_sacrificio = Carbon::parse($element->fecha_sacrificio)->toDateString();
                $element->fecha_vencimiento = Carbon::parse($element->prod_term_fecha_vencimiento)->toDateString();
            } else {
                $empaque = strrpos($element->almacenamiento, "vacio");
                if ($empaque !== false) {
                    if ($element->grupo == 'Res'){
                        $element->fecha_vencimiento = Carbon::parse($element->fecha_desposte)->addDays($element->vencimiento)->toDateString();
                    } else if ($element->grupo === 'Cerdo'  && $element->prod_tipo_almacenamiento_id == 1 ) {
                        $element->fecha_vencimiento = Carbon::parse($element->fecha_venc_refrigerado_vacio)->toDateString();
                    } else if ($element->grupo === 'Cerdo'  && $element->prod_tipo_almacenamiento_id == 2 ) {
                        $element->fecha_vencimiento = Carbon::parse($element->fecha_venc_congelado_vacio)->toDateString();
                    }
                } else {
                    $almacenamiento = strrpos($element->almacenamiento, "Refrigerado");
                    if ($almacenamiento !== false) {
                        $element->fecha_vencimiento = Carbon::parse($element->fecha_venc_refrigerado_granel)->toDateString();
                    } else {
                        $element->fecha_vencimiento = Carbon::parse($element->fecha_venc_congelado_granel)->toDateString();
                    }
                }
                $element->fecha_empaque = Carbon::parse($element->fecha_empaque)->toDateString();
                $element->fecha_sacrificio = Carbon::parse($element->fecha_sacrificio)->toDateString();
                // $element->fecha_vencimiento = Carbon::parse($element->fecha_sacrificio)->addDays($element->vencimiento)->toDateString();
                $element->peso = number_format($element->peso, 2, '.', '');
            }

            $totalKilos += $element->peso;


            foreach ($itemsSumatoria  as $elementSumatoria) {
                if (($element->lote == $elementSumatoria->lote) && ($element->producto_id == $elementSumatoria->producto_id) && ($element ->almacenamiento ==  $elementSumatoria->almacenamiento)) {
                    $elementSumatoria->peso = $elementSumatoria->peso + $element->peso;
                    $elementSumatoria->canastas = $elementSumatoria->canastas + 1;
                    $totalCanastas += 1;
                    $flag = 1;
                }
            }

            if ($flag != 1) {
                $element->canastas = 1;
                $totalCanastas += 1;
                array_push($itemsSumatoria, $element);
            }
        }
        $data = ['salMercancia' => $salMercancia, 'sucursal' => $sucursal, 'tercero' => $tercero, 'itemsSumatoria' => $itemsSumatoria, 'totalCanastas' => $totalCanastas, 'empresa' => $empresa, 'totalKilos' => $totalKilos];
        // dd($data);
        $pdf = PDF::loadView('despachos.certificado', $data);
        // return view('certificados.pdf');

        return $pdf->stream();

    }

    public function despachoParaFactura ($consec) {

        $salMercancia = SalMercancia::findByCosec($consec)->first();
        $sucursal = TerceroSucursal::getDataSucursal($salMercancia->terceroSucursal_id)->first();
        $items = self::validarDespacho($consec, $sucursal);
        $itemsSumatoria = array();
        $flag = 0;
        $cantCanastas = 0;
        foreach ($items as $element) {
            $flag = 0;
            $element->peso = number_format($element->peso, 3, '.', '');
            foreach ($itemsSumatoria  as $elementSumatoria) {
                if (($element->producto_id == $elementSumatoria->producto_id)) {
                    $elementSumatoria->peso = $elementSumatoria->peso + $element->peso;

                    $elementSumatoria->canastas = $elementSumatoria->canastas + 1;

                    $flag = 1;
                }
            }
            if ($flag != 1) {
                $element->canastas = 1;
                array_push($itemsSumatoria, $element);
            }
        }
        return [$itemsSumatoria, $sucursal->sucursal_id, $sucursal->plazo_facturacion];
    }

    public function pesoDespacho ($id) {

        $salMercancia = SalMercancia::find($id);

        $items = SalPivotInventSalida::despachoParaPesoDespacho($id);

        $itemsSumatoria = array();
        $flag = 0;
        $cantCanastas = 0;

        foreach ($items as $element) {

            $flag = 0;

            $cantidad = SalPivotSalProducto::where('sal_mercancia_id',$id)->where('producto_id',$element->producto_id)->get()->first();

            if ($cantidad) {
                $element->cantidad = $cantidad->cantidad;
            } else {
                $element->cantidad = 0;
            }

            $element->peso = number_format($element->peso, 3, '.', '');

            foreach ($itemsSumatoria  as $elementSumatoria) {

                if (($element->producto_id == $elementSumatoria->producto_id)) {
                    $elementSumatoria->peso = $elementSumatoria->peso + $element->peso;

                    $elementSumatoria->canastas = $elementSumatoria->canastas + 1;

                    $flag = 1;
                }
            }

            if ($flag != 1) {
                $element->canastas = 1;
                array_push($itemsSumatoria, $element);
            }
        }

        // dd($itemsSumatoria);

        return $itemsSumatoria;
    }

    public function DateFilter(Request $request,$fecha_inicial,$fecha_final,$sucursal){

        if ($sucursal == 'all') {
            $list = SalMercancia::DateFilter($fecha_inicial,$fecha_final);
        } else {
            $list = SalMercancia::DateSucursalFilter($fecha_inicial,$fecha_final,$sucursal);
        }
        return $list;
    }

    public function GetDataResumen(Request $request,$id){

        $items = SalPivotInventSalida::GetDataCertificado($id);

        foreach ($items as $element) {
            $element->fecha_empaque = Carbon::parse($element->fecha_empaque)->toDateString();
            $element->fecha_vencimiento = Carbon::parse($element->fecha_empaque)->addDays($element->vencimiento)->toDateString();

            $element->peso = number_format($element->peso, 3, '.', '');
        }

        return $items;

    }

    public function CrearPorLote(Request $request){

        $productos = Inventario::productosPorLote($request->lote_id);

        $flag = 0;
        $arrayItems = array();

        foreach ($productos as $item) {
            if ( ($item->estado == 0) || ($item->estado == 3) ){
                $flag = 1;
                array_push($arrayItems, $item->id);
            }
        }

        if ( $flag == 0 ){

            $nuevoItem = new SalMercancia($request->all());
            $lastConsect =  intval(SalMercancia::max('consecutivo')) +1 ;
            $nuevoItem->consecutivo = $lastConsect;
            $nuevoItem->save();

            foreach ($productos as $item) {
                $prod = new SalPivotInventSalida;
                $prod->inventario_id = $item->id;
                $prod->salMercancia_id = $nuevoItem->id;
                $prod->save();

                $inv = Inventario::find($item->id);
                $modificacion1 = ($inv->estado == 2) ? $inv->estado = 3 : '';
                $modificacion2 = ($inv->estado == 1) ? $inv->estado = 0 : '';
                $inv->save();
            }

            return 'done';
        } else {
            return 'los siguientes items ya han sido despachados: '.print_r($arrayItems);
        }
    }

    public static function validarDespacho($consec, $sucursal){
        $items = null;
        $items = SalPivotSalProducto::pesoDespachoParaFactura($consec, $sucursal->prodListaPrecio_id);
        if($items->count() == 0 ){
            $items = SalPivotInventSalida::GetDataCertificadoByConsec($consec); //Despachos de lote completo.
        }
        return $items;
    }
}
