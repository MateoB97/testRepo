<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Lote;
use App\LotPivotEntprodLote;
use App\LotProgramacion;
use App\EntPivotProdEntrada;
use App\ProdGrupo;
use App\ComCompra;
use App\LotPesoPlanta;
use App\Inventario;
use PDF;
use App\ProdPivotListaProducto;
use App\LotPesosProgramacion;


class LotesController extends Controller
{

    public function index()
    {
        $index= Lote::todosConMarca();
        return $index;
    }

    public function store(Request $request)
    {

        if ( (count($request->programaciones) > 0) ){

            $lastSeed = intval(Lote::max('consecutivo'));
            $nuevoItem = new Lote($request->all());
            if($lastSeed > 0){
                $nuevoItem->consecutivo = $lastSeed +1;
            } else {
                $nuevoItem->consecutivo = 1 ;
            }
            if (($request->producto_empacado  == 0 || $request->producto_empacado == '0')) {
                $nuevoItem->fecha_empaque_lote_tercero = null;
            }
            $nuevoItem->estado = 1;
            $nuevoItem->save();

            foreach ($request->programaciones as $programacion) {
                $prog = new LotProgramacion($programacion);
                $prog->lote_id = $nuevoItem->id;
                $prog->estado = 1;
                $prog->save();
            }

            return 'done';
        } else {
            return 'Error: Agrege programacion y productos';
        }

    }

    public function preLiquidacionProgramacion($id)
    {
        $precioValidate = 0;
        $costoTotal = 0;
        $cantidadTotal = 0;
        $pesoPlantaTotal = 0;
        $programacion = LotProgramacion::find($id);
        $ret = array();
        $productos= Inventario::productosPorProgramacion($id);
        $pesosCompra = LotPesosProgramacion::where('lotProgramacion_id', $id)->get();
        $ppe = 0;
        $pcc = 0;
        $pcr = 0;

        $procedencia = ComCompra::proveedorPorProgramacion($id)->first();

        $productosSumatoria = array();

        if ( (count($productos) > 0) ) {


            foreach ($pesosCompra as $pesos) {
                $ppe = $pesos->ppe + $ppe;
                $pcc = $pesos->pcc + $pcc;
                $pcr = $pesos->pcr + $pcr;
            }

            $pesosCompraTotal = ['ppe' => $ppe, 'pcc' => $pcc, 'pcr' => $pcr];

            foreach ($productos as $element) {

                $flag = 0;

                $element->peso = number_format($element->peso, 2, '.', '');

                foreach ($productosSumatoria  as $elementSumatoria) {
                    if ($element->producto_id == $elementSumatoria->producto_id) {
                        $elementSumatoria->peso = number_format($elementSumatoria->peso + $element->peso, 2, '.', '');
                        $listaPrecio = ProdPivotListaProducto::where('producto_id', $elementSumatoria->producto_id)->where('prodListaPrecio_id', 1)->get()->first();
                        $elementSumatoria->precio = $listaPrecio->precio;
                        $flag = 1;
                    }
                }

                if ($flag != 1) {
                    $listaPrecio = ProdPivotListaProducto::where('producto_id', $element->producto_id)->where('prodListaPrecio_id', 1)->get()->first();
                    $element->precio = $listaPrecio->precio;
                    array_push($productosSumatoria, $element);
                }
            }

            $ventaTotal = 0;
            $vacioTotal = 0;

            foreach ($productosSumatoria as $products) {
                $ventaTotal = $ventaTotal + ($products->precio * $products->peso);

                if (strrpos($products->almacenamiento, "vacio")) {
                    $vacioTotal = $vacioTotal + $products->peso;
                }
            }

            $vacioTotal = number_format($vacioTotal, 2, '.', '');

            $data = ['productosSumatoria' => $productosSumatoria,
                     'ventaTotal' => $ventaTotal,
                     'vacioTotal' => $vacioTotal,
                     'pesosCompraTotal' => $pesosCompraTotal,
                     'programacion' => $programacion,
                     'procedencia' => $procedencia
                    ];

            return $data;

        } else {
            if (count($productos) < 1) {
                array_push($ret, 'productos');
            }

            if (count($pesosCompra) != $programacion->num_animales) {
                array_push($ret, 'pesosCompra');
            }

            return $ret;
        }
    }

    public function show($id)
    {
        $model = Lote::find($id);

        $model->programaciones = $model->lotProgramacion;

        $model->ProdGrupo_id = ProdGrupo::find($model->prodGrupo_id);

        $model->productos = Inventario::todosConDatosFilter($id, false, false, false, false);

        $model->lotPesoPlanta;

        $model->marinado = ($model->marinado == 1) ? true : false;
        $model->producto_empacado = ($model->producto_empacado == 1) ? true : false;
        $model->genero = ($model->genero == 1) ? true : false;
        $model->producto_aprobado = ($model->producto_aprobado == 1) ? true : false;

        $model->consec_compra = ComCompra::find($model->com_compras_id)->consecutivo;

        return $model;
    }

    public function update(Request $request, $id)
    {
        if ( (count($request->programaciones) > 0) ){

            $model = Lote::find($request->id);
            $model->fill($request->all());
            $model->save();

            foreach ($request->programaciones as $programacion) {
                $validate = strrpos($programacion['id'], "nuevo");
                if ( $validate === false) {
                    $suc = LotProgramacion::find($programacion['id']);
                    $suc->fill($programacion);
                    $suc->save();
                }else{
                    $suc = new LotProgramacion($programacion);
                    $suc->lote_id = $model->id;
                    $suc->save();
                }
            }

            return 'done';

        } else {
            return 'Error: Agrege programacion y productos';
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
