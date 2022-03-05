<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\FacTipoDoc;
use App\FacTipoRecCaja;
use App\InvCierreInventario;
use Carbon\Carbon;
use App\Tercero;
use PDF;
use stdClass;

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
        // dd($cierre1);
        if (gettype($cierre1) !== "object" || gettype($cierre2) !== "object" ) {
            return 0;
        }
        $dataCierre = InvCierreInventario::getDataCierreInventarioVariacion($fecha_final, $fecha_ini_entra_salid, $cierre1->id, $cierre2->id);

        foreach ($dataCierre as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if (floatval($value2) != 0) {
                    $dataCierre[$key]->$key2 = rtrim(rtrim(sprintf('%.8F', $value2), '0'), ".");
                }
            }
            // if (gettype($dataCierre[$key]->coPad) !== 'NULL') {
            //     $emparejamiento[$key] = collect([
            //         'codPro' => $dataCierre[$key]->codPro,
            //         'coPad' => $dataCierre[$key]->coPad
            //     ]);
            // }
        }

        $data = self::toCollect($dataCierre);
        $subGroups = $data->unique('SubGrupo');

        // foreach ($emparejamiento as $key => $value) {
        //     $hijo = $data->where('codPro', $emparejamiento[$key]['codPro']);
        //     $padre = $data->where('codPro', $value['coPad']); // sumar a este
        //     $concat = $padre->concat($hijo);
        //     $suma = $concat->sum('QtyVentas');
        //     $padre[$padre->keys()[0]]->QtyVentas = strval($suma);
        //     // dd($padre);
        //     // $padre = $padre->replace(['QtyVentas' => strval($suma)]);
        //     // dump($data[$padre->keys()[0]]);
        //     // dump($padre->keys()[0]);
        //     // dump($suma);
        //     // dump($data);
        //     // $data[$padre->keys()[0]]->merge([$padre]);
        // }


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
