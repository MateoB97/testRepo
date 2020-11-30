<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenMunicipio;


class GenMunicipiosController extends Controller
{
    public function index()
    {
        $index= GenMunicipio::all();
        return $index;
    }

    public function porDepartamento($id)
    {
        $list= GenMunicipio::where('departamento_id', $id)->get();
        return $list;
    }
}
