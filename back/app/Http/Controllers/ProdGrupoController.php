<?php

namespace App\Http\Controllers;

use App\ProdGrupo;
use Illuminate\Http\Request;

class ProdGrupoController extends Controller
{
    public function index()
    {
        $index= ProdGrupo::orderBy('created_at', 'desc')->exclude(['created_at','updated_at'])->get();
        return $index;
    }

     public function activos()
    {
        $index= ProdGrupo::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {
        $nuevoItem = new ProdGrupo($request->all());
        $nuevoItem->save();

        return 'done';
    }

    public function show($id)
    {
        $model = ProdGrupo::find($id);
        $model->animal = ($model->animal == 1) ? true : false;

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = ProdGrupo::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = ProdGrupo::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = ProdGrupo::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function animalFilter(){
        
        $list = ProdGrupo::where('animal','=', 1)->get();
        return $list;

    }

}
