<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ComTipoCompra;
use App\ComTipoCompraRelacionado;

class ComTipoComprasController extends Controller
{
    public function index()
    {
        $index= ComTipoCompra::all();

        return $index;
    }

    public function compras()
    {
        $list = ComTipoCompra::where('naturaleza', 1)->get();
        return $list;
    }

    public function comprasPorTipo($tipo_id)
    {
        $list = ComTipoCompra::where('naturaleza', 1)->where('id', $tipo_id)->get();
        return $list;
    }

    public function store(Request $request)
    {
        $nuevoItem = new ComTipoCompra($request->all());
        $nuevoItem->save();

        if ($request->doc_relacionado) {
            $nuevoDocRel = new ComTipoCompraRelacionado();
            $nuevoDocRel->com_tipo_compra_prim_id = $request->doc_relacionado;
            $nuevoDocRel->com_tipo_compra_sec_id = $nuevoItem->id;
            $nuevoDocRel->save();
        }

        return 'done';
    }

    public function show($id)
    {
        $model = ComTipoCompra::find($id);

        return $model;
    }

    public function update(Request $request, $id)
    {
        $model = ComTipoCompra::find($request->id);
        $model->fill($request->all());
        $model->save();

        return 'done';
    }

    public function destroy($id)
    {
        $model = ComTipoCompra::find($id);
        $v = $model->delete();

        if ($v) {
            return 'done';
        } else {
           return 'Imposible eliminar';
        }
    }
}
