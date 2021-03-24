<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacTipoDoc;
use App\FacMovRelacionado;

class FacTipoDocController extends Controller
{

    public function index()
    {
        $index= FacTipoDoc::all();

        return $index;
    }

    public function facTipoDocPorEstado($estado)
    {
        $index= FacTipoDoc::facTipoDocPorEstado($estado);
        return $index;
    }

    public function facturas()
    {
        $list = FacTipoDoc::where('naturaleza', 1)->get();
        return $list;
    }

    public function store(Request $request)
    {
        $nuevoItem = new FacTipoDoc($request->all());
        $nuevoItem->save();

        if ($request->doc_relacionado) {
            $nuevoDocRel = new FacMovRelacionado();
            $nuevoDocRel->fac_tipo_doc_prim_id = $request->doc_relacionado;
            $nuevoDocRel->fac_tipo_doc_sec_id = $nuevoItem->id;
            $nuevoDocRel->save();
        }

        return 'done';
    }

    public function show($id)
    {
        $model = FacTipoDoc::find($id);

        if ($model->naturaleza == 2 || $model->naturaleza == 3 || $model->naturaleza == 0) {
            $model->doc_relacionado = FacMovRelacionado::where('fac_tipo_doc_sec_id', $id)->get()->first()->fac_tipo_doc_prim_id;
        }

        return $model;
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $model = FacTipoDoc::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = FacTipoDoc::find($id);
        $v = $model->delete();

        if ($v) {
            return 'done';
        } else {
           return 'Imposible eliminar';
        }
    }
}
