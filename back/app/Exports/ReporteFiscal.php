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
use App\ReportesGenerados;
use App\GenEmpresa;

class ReporteFiscal implements FromView
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
        $empresa = GenEmpresa::find(1);
        $details = ReportesGenerados::DetailsFiscal($fecha_inicial, $fecha_final);
        $headers = ReportesGenerados::HeadersFiscal($fecha_inicial, $fecha_final);
        $taxes = ReportesGenerados::impuestosFiscal($fecha_inicial, $fecha_final);
        $Notas = ReportesGenerados::impuestoNcFiscal($fecha_inicial, $fecha_final);
        $bolsas = ReportesGenerados::impuestoBolsaFiscal($fecha_inicial, $fecha_final);
        $formasPago = ReportesGenerados::formaPagoFiscal($fecha_inicial, $fecha_final);
        $totalEfectivo = 0;
        $totalCredito = 0;
        $totalVenta = 0;
        $impuestoBolsas = 0;
        foreach ($details as $detail) {
            $totalEfectivo += $detail->TotalEfectivo;
            $totalCredito += $detail->TotalCreditos;
            $totalVenta = $totalEfectivo + $totalCredito;
        }
        foreach($bolsas as $bolsa){
            $impuestoBolsas += $bolsa->precio * $bolsa->cantidad;
        }
        $hoy = Carbon::now();
        // dd($taxes);
        // dd($details);
        $sendData = [
            'details' => $details,
            'headers' => $headers,
            'empresa' => $empresa,
            'fechaIni' => $fecha_inicial,
            'fechaFin' => $fecha_final,
            'totalEfectivo' => $totalEfectivo,
            'totalCredito' => $totalCredito,
            'totalVenta' => $totalVenta,
            'taxes' => $taxes,
            'bolsas' => $bolsas,
            'impuestoBolsas' => $impuestoBolsas,
            'notas' => $Notas,
            'formas' => $formasPago,
            'hoy' => $hoy
        ];
        // dd($sendData);
        return view('facturacion.reportefiscal', $sendData);
    }

}
