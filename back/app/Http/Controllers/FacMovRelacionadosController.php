<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\FacMovRelacionado;
use App\FacMovimiento;

class FacMovRelacionadosController extends Controller
{

    public function getMovPrimario($id)
    {
        $sec = FacMovRelacionado::where('fac_tipo_doc_sec_id', $id)->get();
        return $item;
    }

    public function getItemsMovPrimarioAbiertos($id)
    {
    	$sec = FacMovRelacionado::where('fac_tipo_doc_sec_id', $id)->get()->first();
        $item = FacMovimiento::where('fac_tipo_doc_id', $sec->fac_tipo_doc_prim_id)->where('estado', '!=' , 0)->get();
        return $item;
    }

    public function getItemsMovPrimarios($id)
    {
        $sec = FacMovRelacionado::where('fac_tipo_doc_sec_id', $id)->get()->first();
        $item = FacMovimiento::where('fac_tipo_doc_id', $sec->fac_tipo_doc_prim_id)->where('estado','!=', 3)->get();
        return $item;
    }

}
