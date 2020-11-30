<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProdListaPrecio;
use App\ProdPivotListaProducto;
use App\TerceroSucursal;

class ProdListaPrecioController extends Controller
{
    public function index()
    {
        $index= ProdListaPrecio::orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function activos()
    {
        $index= ProdListaPrecio::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new ProdListaPrecio($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = ProdListaPrecio::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = ProdListaPrecio::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }


    public function update(Request $request, $id)
    {
        $model = ProdListaPrecio::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = ProdListaPrecio::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function preciosPorLista($id){
        $list = ProdPivotListaProducto::where('prodListaPrecio_id','=',$id)->get();

        return $list;
    }

    public function preciosPorSucursal($id){

        $sucursal = TerceroSucursal::find($id);

        $list = ProdPivotListaProducto::where('prodListaPrecio_id','=', $sucursal->prodListaPrecio_id)->get();

        return $list;
    }
}
