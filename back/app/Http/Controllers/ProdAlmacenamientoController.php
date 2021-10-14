<?php

namespace App\Http\Controllers;

use App\ProdAlmacenamiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdAlmacenamientoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $index= ProdAlmacenamiento::orderBy('created_at', 'desc')->exclude(['created_at','updated_at'])->get();
        return $index;
    }

    public function reprocesado($reprocesado)
    {
        $index= ProdAlmacenamiento::where('reprocesado','=',$reprocesado)->orderBy('created_at', 'desc')->exclude(['created_at','updated_at'])->get();
        return $index;
    }

    public function activos()
    {
        $index= ProdAlmacenamiento::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $data = $request->all();

        $nuevoAlmacenamiento = new ProdAlmacenamiento;

        $nuevoAlmacenamiento->nombre = $data['nombre'];

        $nuevoAlmacenamiento->save();

        return 'done';
    }

    public function show($id)
    {
        $model = ProdAlmacenamiento::find($id);

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = ProdAlmacenamiento::find($id);

         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = ProdAlmacenamiento::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {

        $model = ProdAlmacenamiento::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function eliminarVencimiento($id) {
        DB::delete("delete from prod_vencimientos where id = '$id'");
        return 'done';
    }
}
