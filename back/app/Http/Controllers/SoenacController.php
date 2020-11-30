<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SoenacRegimen;
use App\SoenacResponsabilidad;
use App\SoenacTipoDocumento;
use App\SoenacTipoOrg;

class SoenacController extends Controller
{
    public function regimenes()
    {
        $index= SoenacRegimen::all();
        return $index;
    }

    public function responsabilidades()
    {
        $index= SoenacResponsabilidad::all();
        return $index;
    }

    public function tiposDocumento()
    {
        $index= SoenacTipoDocumento::all();
        return $index;
    }

    public function tiposOrganizacion()
    {
        $index= SoenacTipoOrg::all();
        return $index;
    }
}
