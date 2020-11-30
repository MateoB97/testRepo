<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacTipoRecCaja;
use App\FacPivotTipoDocTipoRec;

class FacTipoRecCajaController extends Controller
{
    public function index()
    {
        $list = FacTipoRecCaja::all();
        return $list;
    }

    public function store(Request $request)
    {
        if (count($request->tipos_doc) > 0) {

            $nuevoItem = new FacTipoRecCaja($request->all());
            $nuevoItem->save();

            foreach ($request->tipos_doc as $value) {
                $nuevoPivot = new FacPivotTipoDocTipoRec();

                $nuevoPivot->fac_tipo_rec_id = $nuevoItem->id;
                $nuevoPivot->fac_tipo_doc_id = $value;

                $nuevoPivot->save();
                
            }

            return 'done';

        } else {
            return 'Error: Debe seleccionar al menos un tipo doc.';
        }
    }

    public function show($id)
    {
        $model = FacTipoRecCaja::find($id);

        $tipos_doc = FacPivotTipoDocTipoRec::where('fac_tipo_rec_id', $model->id)->get();

        $array = array();

        foreach ($tipos_doc as $value) {
            array_push($array, $value->fac_tipo_doc_id);
        }

        $model->tipos_doc = $array;

        return $model;
    }

    public function update(Request $request, $id)
    {
        if (count($request->tipos_doc) > 0) {

            $model = FacTipoRecCaja::find($request->id);
            $model->fill($request->all());
            $model->save();

            $tipos_doc = FacPivotTipoDocTipoRec::where('fac_tipo_rec_id', $id)->get();

            foreach ($tipos_doc as $value) {
                $value->delete();
            }

            foreach ($request->tipos_doc as $value) {
                $nuevoPivot = new FacPivotTipoDocTipoRec();

                $nuevoPivot->fac_tipo_rec_id = $model->id;
                $nuevoPivot->fac_tipo_doc_id = $value;

                $nuevoPivot->save();
            }

            return 'done';

        } else {
            return 'Error: Debe seleccionar al menos un tipo doc.';
        }

    }

    public function destroy($id)
    {
        //
    }
}
