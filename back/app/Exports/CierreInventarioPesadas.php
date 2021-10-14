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

class CierreInventarioPesadas implements FromView
{
    public function __construct($fecha_inicial)
    {
        $this->fecha_inicial = $fecha_inicial;
    }

    public function view(): View
    {
        $fecha_inicial = $this->fecha_inicial == "null" ? null : $this->fecha_inicial;
        $cierre = InvCierreInventario::where('fecha_cierre','=', $fecha_inicial)->get()->first();
        $hoy = Carbon::now();
        $data = InvCierreInventario::getDataCierreInventarioPesadas($cierre->id);
        $sendData = [
            'details' => $data,
            'fechaIni' => $fecha_inicial,
            'hoy' => $hoy
        ];
        // dd($data);
        return view('inventarios.cierrePesadas', $sendData);
    }

    public static function toCollect($collect){
        $details = collect($collect)->map(function ($item) {
            return (object) $item;
        });
        return $details;
    }
}
