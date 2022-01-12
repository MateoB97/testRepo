<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\FacTipoDoc;
use App\FacTipoRecCaja;
use App\InvCierreInventario;
use Carbon\Carbon;
use App\Tercero;
use PDF;

class CierreInventarioVariacion implements FromView
{
    public function __construct($fecha_inicial, $fecha_final)
    {
        $this->fecha_inicial = $fecha_inicial;
        $this->fecha_final = $fecha_final;
    }

    public function view(): View
    {
        $fecha_inicial = $this->fecha_inicial == "null" ? null : $this->fecha_inicial;
        $fecha_final = $this->fecha_final == "null" ? null : $this->fecha_final;
        $fecha_ini_entra_salid = Carbon::parse($fecha_inicial)->addDays(1)->toDateString();
        $cierre1 = InvCierreInventario::where('fecha_cierre','=', $fecha_inicial)->get()->first();
        $cierre2 = InvCierreInventario::where('fecha_cierre','=', $fecha_final)->get()->first();
        $hoy = Carbon::now();
        $dataCierre = InvCierreInventario::getDataCierreInventarioVariacion($fecha_final, $fecha_ini_entra_salid, $cierre1->id, $cierre2->id);

        foreach ($dataCierre as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if (floatval($value2) != 0) {
                    $dataCierre[$key]->$key2 = rtrim(rtrim(sprintf('%.8F', $value2), '0'), ".");
                }
            }
        }
        $data = self::toCollect($dataCierre);
        // dd($data);
        $subGroups = $data->unique('SubGrupo');
        $sendData = ['details' => $data,
        'subgrupos' => $subGroups,
        'fechaIni' => $fecha_inicial,
        'fechaFin' => $fecha_final,
        'hoy' => $hoy ];
        return view('inventarios.cierrevariacion', $sendData);
    }

    public static function toCollect($collect){
        $details = collect($collect)->map(function ($item) {
            return (object) $item;
        });
        return $details;
    }

}
