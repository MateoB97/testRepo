<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Tercero;
use App\TerceroSucursal;

class TerceroController extends Controller
{
    public function index()
    {
        $index= Tercero::all();

        return $index;
    }

    public function activos()
    {
        $index= Tercero::where('activo', 1)->get();

        return $index;
    }

    public function store(Request $request)
    {
        if ( count($request->sucursales) > 0){
            $nuevoItem = new Tercero($request->all());
            $nuevoItem->activo = 1;
            $nuevoItem->save();

            foreach ($request->sucursales as $sucursal) {
                $suc = new TerceroSucursal($sucursal);
                $suc->tercero_id = $nuevoItem->id;
                $suc->activo = 1;
                $suc->save();

            }

            return 'done';
        } else {
            return 'Error: Agregue al menos una sucursal';
        }
    }

    public function show($id)
    {
        $model = Tercero::find($id);
        
        $arraySucursales = [];

        foreach ($model->terceroSucursal as $sucursal) {
            $sucursal->lista_de_precio = $sucursal->prodListaPrecio->nombre;
            array_push($arraySucursales, $sucursal);
        }

        $model->sucursales = $arraySucursales;

        $model->proveedor = ($model->proveedor == 1) ? true : false;
        $model->cliente = ($model->cliente == 1) ? true : false;
        $model->empleado = ($model->empleado == 1) ? true : false;
        $model->habilitado_traslados = ($model->habilitado_traslados == 1) ? true : false;

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = Tercero::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = Tercero::find($request->id);
        $model->fill($request->all());

        $model->save();

        foreach ($request->sucursales as $sucursal) {
            $validate = strrpos($sucursal['id'], "nuevo");
            if ( $validate === false) {
                $suc = TerceroSucursal::find($sucursal['id']);
                $suc->fill($sucursal);
                $suc->save();
            }else{
                $suc = new TerceroSucursal($sucursal);
                $suc->tercero_id = $model->id;
                $suc->activo = 1;
                $suc->save();
            }
            
        }

        return 'done';
    }

    public function destroy($id)
    {
        $model = Tercero::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

}
