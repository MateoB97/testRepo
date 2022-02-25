<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalMercancia;
use App\SalPivotInventSalida;
use App\FacMovimiento;
use App\FacPivotMovProducto;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Tools;
use stdClass;

class TestController extends Controller
{
    public function testing1 () {
        $recordSM = new SalMercancia;

        $recordSM->temperatura = 0;
        $recordSM->vehiculo = 'fic123';
        $recordSM->terceroSucursal_id = 1;

        $recordSM->save(); // se grabo esto cuando se ejecuto, restaurar bd

        $InvList = DB::table('inventarios')
            ->leftJoin('sal_pivot_invent_salidas', 'inventarios.id', '=', 'sal_pivot_invent_salidas.inventario_id')
            ->join('producto_terminados', 'producto_terminados.invent_id', '=', 'inventarios.id')
            ->join('lot_programaciones', 'lot_programaciones.id', '=', 'producto_terminados.prog_lotes_id')
            ->join('lotes', 'lotes.id', '=', 'lot_programaciones.lote_id')
            ->join('productos', 'productos.id', '=', 'inventarios.producto_id')
            ->where('inventarios.tipo_invent', 2)
            ->whereNull('sal_pivot_invent_salidas.salMercancia_id')
            ->where('inventarios.created_at', '<', '2021-17-06')
            ->limit(1000)
            ->offset(0)
            ->orderBy('productos.nombre', 'ASC')
            ->get(); // probar si si trae igual con left join a full join

        dd($InvList, $recordSM->id);
        $response = array();
        $response['recordSM'] = $recordSM->id;
        $response['InvList'] = $InvList;

        $this->testing2($response);

        return $response;
    }

    public function testing2 ($data) {

        foreach ($data['InvList'] as $value) {
            $salMeInv = new SalPivotInventSalida;
            $salMeInv->salMercancia_id = $data['recordSM'];
            $salMeInv->inventario_id = $value->id;
            $salMeInv->save();

        }

    }

    public function facE () {


    }
}
