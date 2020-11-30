<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenDepartamento;

class GenDepartamentosController extends Controller
{
    public function index()
    {
        $index= GenDepartamento::all();
        return $index;
    }
}
