<?php

namespace App\Http\Controllers;

use App\ProdSubgrupo;
use App\ProdGrupo;

use Illuminate\Http\Request;

class ProdSubgrupoController extends Controller
{
    public function index()
    {
        $index= ProdSubgrupo::todosConGrupos();
        return $index;
    }

    public function activos()
    {
        $index= ProdSubgrupo::where('activo','1')->orderBy('created_at', 'desc')->get();
        return $index;
    }

    public function store(Request $request)
    {

        $nuevoItem = new ProdSubgrupo($request->all());

        $verificarNombre = ProdSubgrupo::where('nombre','=',$nuevoItem->nombre)->where('prodGrupo_id','=', $nuevoItem->prodGrupo_id)->get();

        if ( count($verificarNombre) < 1) {
            $nuevoItem->save();
            return 'done';
        } else {
            return 'Nombre ya existe';
        }
    }

    public function show($id)
    {
        $model = ProdSubgrupo::find($id);

        $model->prodGrupo_id = $model->prodGrupo;

        return $model;
    }

    public function estado($id, $cambio)
    {
         $model = ProdSubgrupo::find($id);
         
         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = ProdSubgrupo::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = ProdSubgrupo::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function groupFilter($id){
        $list = ProdSubgrupo::where('prodGrupo_id', $id)->get();
        return $list;

    }
}
