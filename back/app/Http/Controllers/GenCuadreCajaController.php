<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\GenCuadreCaja;
use App\FacTipoDoc;
use App\FacMovimiento;
use App\GenEmpresa;
use App\GenCuadreIngresoEfectivo;
use App\EgreEgreso;
use App\GenMunicipio;
use App\GenDepartamento;
use App\FacFormaPago;
use App\TerceroSucursal;
use App\FacPivotFormaRecibo;
use App\FacPivotMovFormapago;
use App\GenImpresora;
use App\User;
use App\EgreTipoEgreso;
use App\GenPivotCuadreFormapago;
use App\ReportesT80;
use App\GenPivotCuadreTiposdoc;
use Illuminate\Support\Facades\Auth;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use Carbon\Carbon;
use PDF;

class GenCuadreCajaController extends Controller
{
    public function index()
    {
        $list = GenCuadreCaja::todosConUsuario();
        return $list;
    }

    public function abiertoPorUsuario($id)
    {
        $list = GenCuadreCaja::where('estado', 1)->where('usuario_id', $id)->get();

        if (count($list) > 0) {
            return '1';
        } else {
            return '0';
        }
    }

    public function abrirCaja($monto_apertura)
    {
        $empresa = GenEmpresa::find(1);
        // $mac = substr(exec('getmac'), 0,17);
        // $lic = $empresa->nit.$mac;
        // $licencia = base64_decode($empresa->licencia);
        // if ($lic == $licencia) {
            if (count(GenCuadreCaja::where('estado', 1)->where('usuario_id', Auth::user()->id)->get()) > 0) {
              return 'Ya tiene un cuadre de caja abierto.';
            } else {
                $nuevoItem = new GenCuadreCaja();
                $nuevoItem->usuario_id = Auth::user()->id;
                $nuevoItem->monto_apertura = $monto_apertura;
                $nuevoItem->estado = 1;
                $nuevoItem->save();

                return 'done';
            }
        // } else {
        //   return 'Licencia no valida!';
        // }
    }

    public function cerrarCaja($monto_cierre)
    {

       $cuadre =  GenCuadreCaja::where('estado', 1)->where('usuario_id', Auth::user()->id)->get()->first();

       $cuadre->monto_cierre = $monto_cierre;
       $cuadre->estado = 0;

       $totalegresos = EgreEgreso::sumEfectivoCuadre($cuadre->id)->first();

       $totalIngresos = GenCuadreIngresoEfectivo::sumIngresoEfectivoCuadre($cuadre->id)->first();

       if ($totalIngresos->valor) {
            $cuadre->total_ingresos = $totalIngresos->valor;
       } else {
            $cuadre->total_egresos = 0;
       }

       if ($totalegresos->valor) {
            $cuadre->total_egresos = $totalegresos->valor;
       } else {
            $cuadre->total_egresos = 0;
       }


       $v = $cuadre->save();

       if ($v) {

           $tiposdoc = FacTipoDoc::whereIn('naturaleza', [1, 4])->get();

           foreach ($tiposdoc as $tipo) {

               $sumatoria = FacMovimiento::sumMovCuadre($tipo->id, $cuadre->id)->first();
               $sumatoriaDev = FacMovimiento::sumMovCuadreDevoluciones($tipo->id, $cuadre->id)->first();

                if ($sumatoria->total){
                   $nuevoPivotMov = new GenPivotCuadreTiposdoc();
                   $nuevoPivotMov->total = $sumatoria->total;
                   $nuevoPivotMov->iva = $sumatoria->ivatotal;
                   $nuevoPivotMov->subtotal = $sumatoria->subtotal;
                   $nuevoPivotMov->descuento = $sumatoria->descuento;
                } else {
                  $nuevoPivotMov = new GenPivotCuadreTiposdoc();
                  $nuevoPivotMov->total = 0;
                  $nuevoPivotMov->iva = 0;
                  $nuevoPivotMov->subtotal = 0;
                  $nuevoPivotMov->descuento = 0;
                }

                if ($sumatoriaDev->total) {
                   $nuevoPivotMov->devolucion_total = $sumatoriaDev->total;
                   $nuevoPivotMov->devolucion_iva = $sumatoriaDev->ivatotal;
                   $nuevoPivotMov->devolucion_subtotal = $sumatoriaDev->subtotal;
                   $nuevoPivotMov->devolucion_descuento = $sumatoriaDev->descuento;
                } else {
                   $nuevoPivotMov->devolucion_total = 0;
                   $nuevoPivotMov->devolucion_iva = 0;
                   $nuevoPivotMov->devolucion_subtotal = 0;
                   $nuevoPivotMov->devolucion_descuento = 0;
                }

                $nuevoPivotMov->gen_cuadre_caja_id = $cuadre->id;
                $nuevoPivotMov->fac_tipo_doc_id = $tipo->id;

                if ($sumatoria->total || $sumatoriaDev->total) {
                  $nuevoPivotMov->save();
                }
           }

           $formaspago = FacFormaPago::all();

           foreach ($formaspago as $forma) {
               $sumatoria = FacPivotFormaRecibo::sumFormapagoCuadre($forma->id, $cuadre->id)->first();

               if ($sumatoria->valor != 0){
                   $nuevoPivotForma = new GenPivotCuadreFormapago();
                   $nuevoPivotForma->valor = intval($sumatoria->valor);
                   $nuevoPivotForma->gen_cuadre_caja_id = $cuadre->id;
                   $nuevoPivotForma->fac_formas_pago_id = $forma->id;
                   $nuevoPivotForma->referente = 2;
                   $nuevoPivotForma->save();
                }

               $sumatoria = FacPivotMovFormapago::sumFormapagoCuadre($forma->id, $cuadre->id)->first();

               if ($sumatoria->valor != 0){
                   $nuevoPivotForma = new GenPivotCuadreFormapago();
                   $nuevoPivotForma->valor = intval($sumatoria->valor);
                   $nuevoPivotForma->gen_cuadre_caja_id = $cuadre->id;
                   $nuevoPivotForma->fac_formas_pago_id = $forma->id;
                   $nuevoPivotForma->referente = 1;
                   $nuevoPivotForma->save();
                }

           }

          return [
            'respuesta' => 'done',
            'cuadre_id' => $cuadre->id
          ];

        } else {
            return 'No se pudo cerrar la caja';
        }
    }

    public function printCuadre ($id) {

        $caracLinea = caracteres_linea_pos();

        $t80 = new ReportesT80();

        $user = User::find(Auth::user()->id);

        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));

        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $cuadre = GenCuadreCaja::porId($id);

        $cuadre->created_at = formato_fecha($cuadre->created_at);
        $cuadre->updated_at = formato_fecha($cuadre->updated_at);

        $recibos = GenPivotCuadreFormapago::sumCuadreRecibos($id)->first();

        $etiqueta = str_pad("", $caracLinea, " ", STR_PAD_BOTH);

        $etiqueta .= $t80->posHeaderEmpresa();

        $etiqueta .= $t80->posDosItemsExtremos('CUADRE CAJA',$id);
        $etiqueta .= $t80->posDosItemsExtremos('Fecha Apertura:',$cuadre->created_at);
        $etiqueta .= $t80->posDosItemsExtremos('Fecha Cierre:',$cuadre->updated_at);
        $etiqueta .= $t80->posDosItemsExtremos('Usuario cuadre:',$cuadre->usuario_id);
        $etiqueta .= $t80->posDosItemsExtremos('+ Monto Apertura:', $t80->toNumber($cuadre->monto_apertura));


        $etiqueta .= $t80->posLineaBlanco();

        $etiqueta .= $t80->posLineaGuion();
        $etiqueta .= $t80->posLineaCentro('VENTAS');
        $etiqueta .= $t80->posLineaGuion();


        $ventas = GenPivotCuadreTiposdoc::porCuadreConTipodoc($id);
        $ventasTotal = 0;

        foreach ($ventas  as $venta) {
            // ENCABEZADO
            $etiqueta .= $t80->posLineaDerecha('- '.$venta->tipodoc);
            $etiqueta .= $t80->posDosItemsExtremos('-- Total', $t80->toNumber($venta->total));
            $etiqueta .= $t80->posDosItemsExtremos('-- Dev Total', $t80->toNumber($venta->devolucion_total));
            $etiqueta .= $t80->posDosItemsExtremos('-- Gran Total', $t80->toNumber($venta->total - $venta->devolucion_total));
            $etiqueta .= $t80->posLineaBlanco();

            $ventasTotal = $ventasTotal + ($venta->total - $venta->devolucion_total);
        }

        $etiqueta .= $t80->posDosItemsExtremos('VENTAS TOTALES', $t80->toNumber($ventasTotal));

        $etiqueta .= $t80->posLineaBlanco();

        $etiqueta .= $t80->posDosItemsExtremos('TOTAL RECIBOS', $t80->toNumber($recibos->valor));


        $notasCredito = FacMovimiento::notasCreditoPorCuadre($id);

        $totalNotasCredito = 0;

        $etiqueta .= $t80->posLineaDerecha('NOTAS CREDITO');

        foreach ($notasCredito as $notaCredito) {
          $etiqueta .= $t80->posDosItemsExtremos('- '. $notaCredito->consecutivo, $t80->toNumber($notaCredito->total));

          $totalNotasCredito += $notaCredito->total;
        }

        $etiqueta .= $t80->posLineaBlanco();
        $etiqueta .= $t80->posDosItemsExtremos('TOTAL NOTAS CREDITO', $t80->toNumber($totalNotasCredito));
        $etiqueta .= $t80->posLineaBlanco();

        $etiqueta .= $t80->posLineaGuion();
        $etiqueta .= $t80->posLineaCentro('INGRESOS');
        $etiqueta .= $t80->posLineaGuion();

        $ingresosTotales = 0;
        $ingresosEfectivoTotal = 0;

        $totalIngresos = 0;

        $formaspago = FacFormaPago::all();

        foreach ($formaspago as $forma) {
            $ingreso = GenPivotCuadreFormapago::sumCuadrePorForma($id, $forma->id)->first();

            if (!is_null($ingreso->valor)) {

              $etiqueta .= $t80->posDosItemsExtremos($forma->nombre, $t80->toNumber($ingreso->valor));

              $totalIngresos += $ingreso->valor;

              if ($forma->id == 1){
                $ingresosEfectivoTotal = $ingreso->valor;
              }
            }
        }

        $etiqueta .= $t80->posLineaBlanco();
        $etiqueta .= $t80->posDosItemsExtremos('INGRESOS TOTALES', $t80->toNumber($totalIngresos));
        $etiqueta .= $t80->posLineaBlanco();

        $etiqueta .= $t80->posLineaGuion();
        $etiqueta .= $t80->posLineaCentro('EGRESOS');
        $etiqueta .= $t80->posLineaGuion();

        $tiposEgreso = EgreTipoEgreso::all();

        foreach ($tiposEgreso as $tipo) {

            $egresos = EgreEgreso::where('egre_tipo_egreso_id',$tipo->id)->where('gen_cuadre_caja_id', $cuadre->id)->get();

            if (count($egresos) > 0) {
                $total = 0;

                $etiqueta .= $t80->posLineaGuion();
                $etiqueta .= $t80->posLineaBlanco();
                $etiqueta .= $t80->posLineaCentro($tipo->nombre);
                $etiqueta .= $t80->posLineaBlanco();

                foreach ($egresos as $egreso) {
                    $total = intval($egreso->valor) + $total;

                    $etiqueta .= $t80->posDosItemsExtremos($egreso->consecutivo.' - '.substr(eliminar_acentos($egreso->descripcion), 0, 25), '$'.$t80->toNumber($egreso->valor));
                }

                $etiqueta .= $t80->posDosItemsExtremos('*Total', '$'.$t80->toNumber($total));
            }
        }

        $etiqueta .= $t80->posLineaBlanco();

        $etiqueta .= $t80->posDosItemsExtremos('EGRESOS EFECT. TOT.', $t80->toNumber($cuadre->total_egresos));
        $etiqueta .= $t80->posDosItemsExtremos('INGRESOS EFECT. TOT.', $t80->toNumber($cuadre->total_ingresos));


        $etiqueta .= $t80->posLineaBlanco();
        $etiqueta .= $t80->posLineaGuion();
        $etiqueta .= $t80->posLineaGuion();

        $etiqueta .= $t80->posLineaDerecha('RESUMEN CUADRE');

        $etiqueta .= $t80->posLineaBlanco();

        $etiqueta .= $t80->posDosItemsExtremos('= BALANCE', $t80->toNumber($ingresosEfectivoTotal - $cuadre->total_egresos + $cuadre->total_ingresos));
        $etiqueta .= $t80->posDosItemsExtremos('- Monto Cierre', $t80->toNumber($cuadre->monto_cierre));
        $etiqueta .= $t80->posDosItemsExtremos('DIFERENCIA', $t80->toNumber($cuadre->monto_cierre - ($ingresosEfectivoTotal - $cuadre->total_egresos + $cuadre->total_ingresos)));

        $printer->text($etiqueta);
        $printer->feed(2);
        $printer->cut();

        $printer->close();

    }

    public function printCuadrePDF ($id) {

        $caracLinea = caracteres_linea_pos();

        $cuadre = GenCuadreCaja::porId($id);

        $cuadre->created_at = formato_fecha($cuadre->created_at);
        $cuadre->updated_at = formato_fecha($cuadre->updated_at);

        $empresa = GenEmpresa::find(1);

        $municipio = GenMunicipio::find($empresa->gen_municipios_id);

        $departamento = GenDepartamento::find($municipio->departamento_id);

        $ventas = GenPivotCuadreTiposdoc::porCuadreConTipodoc($id);

        // $ingresosPos = GenPivotCuadreFormapago::sumCuadrePorReferente($id, 1);

        $etiqueta = '';

        $ingresosTotales = 0;
        $ingresosEfectivoTotal = 0;

        // $ingresosRecibos = GenPivotCuadreFormapago::sumCuadrePorReferente($id, 2);

        $formaspago = FacFormaPago::all();

        $ingresos = array();

        $totalIngresos = 0;

        foreach ($formaspago as $forma) {
            $ingreso = GenPivotCuadreFormapago::sumCuadrePorForma($id, $forma->id)->first();

            if (!is_null($ingreso->valor)) {
              array_push($ingresos, [$forma->nombre, $ingreso->valor]);

              $totalIngresos += $ingreso->valor;
            }else{
                array_push($ingresos, [$forma->nombre, 0]);
            }

        }

        $recibos = GenPivotCuadreFormapago::sumCuadreRecibos($id)->first();

        $tiposEgreso = EgreTipoEgreso::all();

        $data = [
          'caracLinea' => $caracLinea,
          'empresa' => $empresa,
          'municipio' => $municipio,
          'departamento' => $departamento,
          'cuadre' => $cuadre,
          'id' => $id,
          'ventas' => $ventas,
          'ingresos' => $ingresos,
          'recibos' => $recibos,
          'totalIngresos' => $totalIngresos,
          'tiposEgreso' => $tiposEgreso
        ];

        $pdf = PDF::loadView('facturacion.cuadrepdf', $data);

        return $pdf->stream();
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
