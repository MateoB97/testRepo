<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SalMercancia;
use App\SalPivotInventSalida;
use Illuminate\Support\Facades\DB;
use stdClass;

class TestController extends Controller
{
    public function testing1 () {
        $recordSM = new SalMercancia;

        $recordSM->temperatura = 0;
        $recordSM->vehiculo = 'fic123';
        $recordSM->terceroSucursal_id = 1;

        $recordSM->save();

        $InvList = DB::table("inventarios
            full join [sal_pivot_invent_salidas] on inventarios.id = sal_pivot_invent_salidas.inventario_id
            full join producto_terminados on producto_terminados.invent_id = inventarios.id
            full join lot_programaciones on lot_programaciones.id = producto_terminados.prog_lotes_id
            full join lotes on lotes.id = lot_programaciones.lote_id
            full join productos on productos.id = inventarios.producto_id
            where [inventarios].tipo_invent = 2
            and salMercancia_id is null
            and cast(inventarios.created_at as date) < '2021-06-17'
            order by productos.nombre asc")->where('inventarios.tipo_invent', 2)->limit(1000)->offset(0)->orderBy('id', 'DESC')->get();

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
}
