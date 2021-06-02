<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\FacMovimiento;
use App\FacPivotMovProducto;
use App\FacPivotMovSalmercancia;
use App\FacPivotFormaRecibo;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;
use App\FacMovRelacionado;
use App\FacPivotMovFormapago;
use App\FacTipoDoc;
use App\FacTipoRecCaja;
use App\Producto;
use App\SalMercancia;
use App\GenImpresora;
use App\GenMunicipio;
use App\GenDepartamento;
use App\FacDataFE;
use App\GenIva;
use App\GenVendedor;
use App\FacFormaPago;
use App\GenUnidades;
use App\FacCruce;
use App\GenEmpresa;
use App\TerceroSucursal;
use App\Tercero;
use App\GenCuadreCaja;
use App\FacPivotMovVendedor;
use App\Inventario;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use PDF;
use App\FacReciboCaja;
use App\FacPivotRecMov;
use App\ReportesT80;
use DNS2D;
use App\SoenacRegimen;
use App\SoenacResponsabilidad;
use App\SoenacTipoDocumento;
use App\SoenacTipoOrg;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\TempMarques;
use App\FacPivotTipoDocTipoRec;

class FacMovimientosController extends Controller
{

    public function importarDatos() {

        // $terceros =
        // [['201030','ALBA MELLA','0','1','0','4',' CARRERA 44A 89-144','2852653','UNICA','79','NO TIENE'],
        //     ['1017190518','ASADOS COMPANY','0','1','0','0','CRR 48a N° 76 - 74','3014196911','UNICA','79','NO TIENE'],
        //     ['43025443','BLANCA ESTELA PASOS','0','1','0','4','CARRERA 44A NUME 89 190','2141924','UNICA','79','NO TIENE'],
        //     ['900199056','COMERCIALIZADORA PALACIO G S.A.S','0','1','0','1','CRA 49B  44-06','5971518','UNICA','79','NO TIENE'],
        //     ['890941786','creaciones picardia picardia','0','1','0','7','CLL10A#41-81','4488831','UNICA','79','NO TIENE'],
        //     ['1063357504','deymer antonio perez','0','1','0','0','PREGUNTAR','1','UNICA','79','NO TIENE'],
        //     ['201098','diana cuadros','0','1','0','4','casa de la casa','2','UNICA','79','NO TIENE'],
        //     ['98618621','DISTRIBUCIONES URABA C','0','1','0','4','CALLE 96 104-31','3','UNICA','44','NO TIENE'],
        //     ['201094','dorlan garcias','0','1','0','5','PREGUNTAR','5724355','UNICA','79','NO TIENE'],
        //     ['21928144','el hotel dorado','0','1','0','2','cra 70 # 44b-66','3117777965','UNICA','79','NO TIENE'],
        //     ['201012','farit pacheco','0','1','0','1','cr44 88-63','5731948','UNICA','79','NO TIENE'],
        //     ['900643143','FOOD AND DRINKS INCORPORATED S.A.S','0','1','0','9','CR 70 43 42 IN 203','260 98 05','UNICA','79','NO TIENE'],
        //     ['900359787','grupo pescadero pescadero','0','1','0','4','poblado','4488831','UNICA','79','NO TIENE'],
        //     ['900816979','HOGAR DE PASO SANTA TERESITA S.A.S','0','1','0','2','CALLE 61 50 56','3008260967','UNICA','79','NO TIENE'],
        //     ['70115586','HUGO AGURRE','0','1','0','3','CRA 44 # 84-28','5826138','UNICA','79','NO TIENE'],
        //     ['1214717267','jeison cardona','0','1','0','5','calle 54#86-26','4','UNICA','79','NO TIENE'],
        //     ['3513894','JHON DARIO POSADA','0','1','0','0','CARRE 45A NUMER 82 42','2637863','UNICA','79','NO TIENE'],
        //     ['201056','JHON SANCOCHO','0','1','0','5','LA CASA','4587936','UNICA','79','NO TIENE'],
        //     ['1011211006','jonathan stiven ramirez jaramillo','0','1','0','0','CALLE 81 #43-14','3024293655','UNICA','79','NO TIENE'],
        //     ['201091','JORGE MESA mesa','0','1','0','3','cr44A N 89190','5219956','UNICA','79','NO TIENE'],
        //     ['201009','JUAN VELEZ','0','1','0','9','CR44#86-26','5','UNICA','79','NO TIENE'],
        //     ['1035862884','julieth alejandra parra garcia','0','1','0','0','cra 68 # 93-30','3023365244','UNICA','79','NO TIENE'],
        //     ['900643143_9','LICO EXPRESS','0','1','0','7','LA MAYORISTA','3117777965','UNICA','79','NO TIENE'],
        //     ['201084','maria del pilar echavarria','0','1','0','1','cr45a 8250','2115806','UNICA','79','NO TIENE'],
        //     ['21779010','maria griselda giraldo','0','1','0','4','calle84 #44-34','3117553866','UNICA','79','NO TIENE'],
        //     ['1036338985','nataly ospina','0','1','0','3','cra 51 calle 98a-21','3147807764','UNICA','79','NO TIENE'],
        //     ['900491711','NUTRIALIMENTAMOS','0','1','0','9','CR81 A CL 33-41','4129099','UNICA','79','NO TIENE'],
        //     ['1017146847','olmedo idarraga','0','1','0','0','cra 68 #92f-18','3197350098','UNICA','79','NO TIENE'],
        //     ['10376104477','romulo de calle','0','1','0','9','medellin','6','UNICA','79','NO TIENE'],
        //     ['71386506','VLADIMIR ALZATE HINCAPIE','0','1','0','1','calle 95 49 05','3022078732','UNICA','79','NO TIENE'],
        //     ['201080','yeison chiki','0','1','0','2','cr48 52-26','2339319','UNICA','79','NO TIENE']];

        $i = 2;

        foreach ($terceros as $item) {
            Tercero::create([
                'documento' => $item[0],
                'nombre' => strtolower($item[1]),
                'proveedor' => $item[2],
                'cliente' => $item[3],
                'empleado' => $item[4],
                'digito_verificacion' => $item[5],
                'activo' => 1,
                'habilitado_traslados' => 0,
                'soenac_regim_id' => null,
                'soenac_responsab_id' => null,
                'soenac_tipo_org_id' => null,
                'soenac_tipo_documento_id' => null,
                'registro_mercantil' => null
            ]);

            TerceroSucursal::create([
                'nombre' => strtolower($item[8]),
                'direccion' => $item[6],
                'telefono' => intval($item[7]),
                'tercero_id' => $i,
                'prodListaPrecio_id' => 1,
                'activo' => 1,
                'gen_municipios_id' => intval($item[9])
            ]);

            $i = $i + 1;
        }


        $data =
            // [['118','5897','1','8934','4131'],
            // ['118','6588','31','80204','0']];
                [['102', '930','8' ,'40','0'],
                ['118', '3360','4' ,'12180','0'],
                ['102', '3499','4' ,'15380','0'],
                ['102', '3527','4' ,'24840','0'],
                ['102', '1017','8' ,'26050','0'],
                ['102', '1048','8' ,'27690','0'],
                ['102', '985','8' ,'28600','0'],
                ['102', '1018','8' ,'33990','0'],
                ['102', '964','8' ,'34690','0'],
                ['102', '1085','9' ,'34765','0'],
                ['102', '1064','8' ,'35110','0'],
                ['102', '1079','8' ,'35210','0'],
                ['118', '3175','6' ,'36220','0'],
                ['102', '1071','8' ,'38330','0'],
                ['102', '1005','8' ,'38890','0'],
                ['102', '921','8' ,'39933','0'],
                ['102', '992','8' ,'40640','0'],
                ['118', '3169','6' ,'42565','0'],
                ['102', '3507','4' ,'43200','0'],
                ['102', '977','8' ,'43720','0'],
                ['102', '950','8' ,'45296','0'],
                ['102', '1066','8' ,'46115','0'],
                ['118', '3412','4' ,'46438','0'],
                ['102', '1003','8' ,'47150','0'],
                ['102', '1041','8' ,'48830','0'],
                ['102', '1045','8' ,'49180','0'],
                ['102', '1023','8' ,'49670','0'],
                ['102', '990','8' ,'50090','0'],
                ['102', '1033','8' ,'53730','0'],
                ['102', '1086','8' ,'53835','0'],
                ['102', '1029','8' ,'56460','0'],
                ['102', '982','8' ,'57440','0'],
                ['102', '980','8' ,'57903','0'],
                ['102', '1007','8' ,'58590','0'],
                ['102', '1000','8' ,'59960','0'],
                ['102', '968','8' ,'61545','0'],
                ['102', '1077','8' ,'62960','0'],
                ['118', '3384','4' ,'65468','0'],
                ['102', '1075','8' ,'67160','0'],
                ['102', '953','8' ,'69483','0'],
                ['102', '938','8' ,'69600','0'],
                ['102', '962','8' ,'72425','0'],
                ['102', '979','8' ,'72500','0'],
                ['102', '1052','8' ,'74590','0'],
                ['102', '1069','8' ,'80170','0'],
                ['102', '1082','8' ,'82635','0'],
                ['118', '3163','6' ,'85490','0'],
                ['118', '3165','6' ,'87775','0'],
                ['102', '981','8' ,'92263','0'],
                ['102', '3531','4' ,'94965','0'],
                ['102', '984','8' ,'95940','0'],
                ['102', '1037','8' ,'96523','0'],
                ['102', '1047','8' ,'98870','0'],
                ['102', '969','8' ,'101298','0'],
                ['102', '870','9' ,'102003','0'],
                ['102', '875','8' ,'102916','0'],
                ['102', '1015','8' ,'104540','0'],
                ['102', '871','8' ,'104635','0'],
                ['102', '1065','8' ,'109390','0'],
                ['102', '995','8' ,'112553','0'],
                ['102', '956','8' ,'118755','0'],
                ['102', '799','9' ,'119800','0'],
                ['102', '941','8' ,'128248','0'],
                ['102', '957','8' ,'136650','0'],
                ['102', '1050','8' ,'140005','0'],
                ['102', '1076','8' ,'140270','0'],
                ['102', '3532','4' ,'152665','0'],
                ['102', '952','8' ,'157216','0'],
                ['102', '1022','8' ,'160970','0'],
                ['102', '868','10','167969','0'],
                ['102', '966','4','170529','0'],
                ['118', '3167','6','180713','0'],
                ['102', '1062','3','275551','138382'],
                ['102', '1080','7','441435','0'],
                ['102', '970','9','641232','0'],
                ['102', '1058','3','676573','0'],
                ['102', '1067','3','1937822','0']];


        $conseRec1 = 32;
        $conseRec2 = 57;

        foreach ($data as $item) {
            $nuevoItem = new FacMovimiento();

            if (intval($item[0]) == 102) {
                $nuevoItem->fac_tipo_doc_id = 1;
            } else {
                $nuevoItem->fac_tipo_doc_id = 7;
            }

            $nuevoItem->consecutivo = intval($item[1]);
            $nuevoItem->cliente_id = intval($item[2]);
            $nuevoItem->subtotal = intval($item[3]);
            $nuevoItem->total = intval($item[3]);
            $nuevoItem->estado = 1;
            $nuevoItem->descuento = 0;
            $nuevoItem->ivatotal = 0;
            $nuevoItem->saldo = intval($item[3]) - intval($item[4]);
            $nuevoItem->fecha_vencimiento = '01/02/2021';
            $nuevoItem->fecha_facturacion = '01/02/2021';
            $nuevoItem->nota ='Cargado desde Costo y venta el 01/02/2021, no se tiene fecha real de la factura';
            $nuevoItem->gen_cuadre_caja_id = 1;
            $nuevoItem->save();

            if (intval($item[4]) > 0) {

                $nuevoRecibo = new FacReciboCaja();

                if (intval($item[0]) == 102) {
                    $nuevoRecibo->fac_tipo_rec_caja_id = 1;
                    $nuevoRecibo->consecutivo = $conseRec1;
                    $conseRec1 = $conseRec1 + 1;
                } else {
                    $nuevoRecibo->fac_tipo_rec_caja_id = 2;
                    $nuevoRecibo->consecutivo = $conseRec2;
                    $conseRec1 = $conseRec2 + 1;
                }
                $nuevoRecibo->tercero_sucursal_id = intval($item[2]);
                $nuevoRecibo->gen_cuadre_caja_id = 1;
                $nuevoRecibo->abono = intval($item[4]);
                $nuevoRecibo->total = intval($item[4]);
                $nuevoRecibo->ajuste = 0;
                $nuevoRecibo->ajuste_observacion = 'no aplica';
                $nuevoRecibo->fecha_recibo = '21/09/2020';
                $nuevoRecibo->save();

                $nuevoPago = new FacPivotFormaRecibo();
                $nuevoPago->fac_recibo_id = $nuevoRecibo->id;
                $nuevoPago->fac_formas_pago_id = 1;
                $nuevoPago->valor = intval($item[4]);
                $nuevoPago->save();

                $cruce = new FacPivotRecMov();
                $cruce->fac_mov_id = $nuevoItem->id;
                $cruce->fac_recibo_id = $nuevoRecibo->id;
                $cruce->valor = intval($item[4]);
                $cruce->save();
            }
        }
    }

    public function index()
    {
        $index= FacMovimiento::todosConTipoSucursalGrupoTipo();
        return $index;
    }

    public function todosConTipoSucursalGrupo()
    {
        $index= FacMovimiento::todosConTipoSucursalGrupoTipo();
        return $index;
    }

    public function porTipos($id)
    {
        $index = FacMovimiento::where('fac_tipo_doc_id', $id)->get();
        return $index;
    }

    public function porSucursal($sucursal_id, $tipodoc_id)
    {
        $index = FacMovimiento::where('tercero_sucursal_id', $sucursal_id)->where('fac_tipo_doc_id', $tipodoc_id)->get();
        return $index;
    }

    public function facturasPendientesPorSucursal($sucursal_id)
    {
        $index = FacMovimiento::facturasPendientesPorSucursal($sucursal_id);
        return $index;
    }

    public function facturasPendientesPorSucursalYTipo($sucursal_id, $tipo_rec_id)
    {
        $tipos = array();

        $tipos_doc = FacPivotTipoDocTipoRec::where('fac_tipo_rec_id', $tipo_rec_id)->get();

        foreach ($tipos_doc as $tipo_doc) {
            array_push($tipos, intval($tipo_doc->fac_tipo_doc_id));
        }

        $index = FacMovimiento::facturasPendientesPorSucursalYTipo($sucursal_id, $tipos);

        return $index;
    }

    public function FacturasPendientesParaNotas($sucursal_id, $tipodoc_id)
    {
        $tipoRelacionado = FacMovRelacionado::where('fac_tipo_doc_sec_id', $tipodoc_id)->get()->first();

        $list = FacMovimiento::facturasPendientesPorSucursalYTipo($sucursal_id, [$tipoRelacionado->fac_tipo_doc_prim_id]);
        return $list;
    }

    public function ultimoPorTipoDoc ($tipodoc) {
        $item = FacMovimiento::where('fac_tipo_doc_id', $tipodoc)->get();

        if (count($item) > 0) {
            return $item->last()->consecutivo;
        } else {
            return 'error';
        }
    }

    public function store(Request $request)
    {

        $nuevoItem = new FacMovimiento($request->all());

        $nuevoItem->gen_cuadre_caja_id = GenCuadreCaja::where('estado', 1)->where('usuario_id', Auth::user()->id)->get()->first()->id;

        $tipoDoc = FacTipoDoc::find($nuevoItem->fac_tipo_doc_id);

        if ( count(FacMovimiento::where('fac_tipo_doc_id', $nuevoItem->fac_tipo_doc_id)->get()) > 0 ){
            $consecutivo = FacMovimiento::where('fac_tipo_doc_id', $nuevoItem->fac_tipo_doc_id)->get()->last();
            $nuevoItem->consecutivo = $consecutivo->consecutivo + 1;
        }else{
            $nuevoItem->consecutivo = $tipoDoc->consec_inicio;
        }

        // si es una devolucion cambia el estado del movimiento
        if ($tipoDoc->naturaleza == 0) {

            $docRelacionado = FacMovimiento::find($request->docReferencia['id']);
            self::descargarDevolucionInventario($docRelacionado->id);
            $docRelacionado->estado = 3;

            $docRelacionado->save();

            return 'done';

         // se valida si es un documento factura
        } elseif ($tipoDoc->naturaleza == 1) {

           $nuevoItem->estado = 1;
           $nuevoItem->saldo = $nuevoItem->total;

           if (intval($nuevoItem->total) > 0) {
                $nuevoItem->save();
           } else {
            return 'Se evito una factura en 0.';
           }


           $pivotVendedor = new FacPivotMovVendedor();
           if ($request->gen_vendedor_id) {
                $pivotVendedor->gen_vendedor_id = $request->gen_vendedor_id;
           } else {
                $pivotVendedor->gen_vendedor_id = 1;
           }

           $pivotVendedor->fac_mov_id = $nuevoItem->id;
           $pivotVendedor->save();

        // factura POS
        } elseif ($tipoDoc->naturaleza == 4) {

            $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
            $connector = new WindowsPrintConnector($nombre_impresora);
            $printer = new Printer($connector);

            $printer->pulse();

            $nuevoItem->estado = 0;
            $nuevoItem->saldo = 0;

            if (intval($nuevoItem->total) > 0) {
                $nuevoItem->save();
            } else {
                return 'Se evito una factura en 0.';
            }

            $empresa = GenEmpresa::find(1);

            $sucursal = TerceroSucursal::find($request->cliente_id);

            $tercero = $sucursal->Tercero;

            $municipio = GenMunicipio::find($empresa->gen_municipios_id);

            $departamento = GenDepartamento::find($municipio->departamento_id);

            $pivotVendedor = new FacPivotMovVendedor();
            if ($request->gen_vendedor_id) {
                $pivotVendedor->gen_vendedor_id = $request->gen_vendedor_id;
            } else {
                $pivotVendedor->gen_vendedor_id = 1;
            }

            $pivotVendedor->fac_mov_id = $nuevoItem->id;
            $pivotVendedor->save();

            foreach ($request->pagos as $pago) {

                if ($pago['valor'] > 0) {
                    $nuevoPago = new FacPivotMovFormapago();
                    $nuevoPago->fac_mov_id = $nuevoItem->id;
                    $nuevoPago->fac_formas_pago_id = $pago['id'];
                    $nuevoPago->valor_recibido = $pago['valor'];
                    if ( $request->devuelta && $pago['id'] == 1) {
                        $nuevoPago->valor_pagado = intval($pago['valor']) - intval($request->devuelta);
                    } else {
                        $nuevoPago->valor_pagado = $pago['valor'];
                    }
                    $nuevoPago->save();
                }
            }

         // si es secundario se busca el item relacionado (primario) para realizar la modificacion del saldo y el estado
        } else {

            $nuevoItem->estado = 0;
            $nuevoItem->saldo = 0;

            $docRelacionado = FacMovimiento::find($request->docReferencia['id']);

            $nuevoItem->cliente_id = $docRelacionado->cliente_id;

            if ($tipoDoc->naturaleza == 2 ) {
                if (floatval($docRelacionado->saldo) >= floatval($nuevoItem->total)){
                    $docRelacionado->saldo = floatval($docRelacionado->saldo) - floatval($nuevoItem->total);
                } else {
                    return 'Error: La nota supera el valor del saldo.';
                }
            } elseif ($tipoDoc->naturaleza == 3 ) {
                $docRelacionado->saldo = floatval($docRelacionado->saldo) + floatval($nuevoItem->total);
            }

            if ($docRelacionado->saldo == 0) {
                $docRelacionado->estado = 0;
            } elseif ($docRelacionado->saldo > 0) {
                $docRelacionado->estado = 1;
            } elseif ($docRelacionado->saldo < 0) {
                $docRelacionado->estado = 2;
            }

            $nuevoItem->save();

            $docRelacionado->save();

            // se genera el cruce entre los 2 documentos
            $cruce = new FacCruce();

            $cruce->fac_mov_principal = $docRelacionado->id;
            $cruce->fac_mov_secundario = $nuevoItem->id;

            $cruce->save();
        }

        foreach ($request->lineas as $linea) {
            $nuevoPivot = new FacPivotMovProducto($linea);
            $nuevoPivot->fac_mov_id = $nuevoItem->id;
            $nuevoPivot->precio = intval($linea['precio']);
            $nuevoPivot->save();

            if ($tipoDoc->naturaleza == 4 || $tipoDoc->naturaleza == 1) {

                self::afectarInventario($linea['producto_id'], $linea['cantidad'], 0);
            } elseif ($tipoDoc->naturaleza == 2 && $request->afectaInventario == true) {
                self::afectarInventario($linea['producto_id'], $linea['cantidad'], 1);
            }
        }

        if ($tipoDoc->resolucion_soenac_id != 0 && !is_null($tipoDoc->resolucion_soenac_id) ){
            return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id, true]];
        } else {
            return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];
        }
    }

    public static function afectarInventario ($producto_id, $cantidad, $tipo_operacion) {

        if ($tipo_operacion == 1){
            $cantidad = floatval($cantidad);
        } else {
            $cantidad = -floatval($cantidad);
        }

        $itemInventario = Inventario::where('producto_id', $producto_id)->where('tipo_invent','!=',2)->get()->first();
        if ($itemInventario) {
            $itemInventario->cantidad += $cantidad;
            $itemInventario->save();
        } else {
            $nuevoInventario = new Inventario();
            $nuevoInventario->cantidad = $cantidad;
            $nuevoInventario->producto_id = $producto_id;
            $nuevoInventario->costo_promedio = 0;
            $nuevoInventario->tipo_invent = 1;
            $nuevoInventario->save();
        }
    }

    public function update(Request $request, $id)
    {

        $nuevoItem = FacMovimiento::find($id);

        $tipoDoc = FacTipoDoc::find($nuevoItem->fac_tipo_doc_id);



        if ( ($tipoDoc->naturaleza == 1) || ($tipoDoc->naturaleza == 4)){

            if ($nuevoItem->cufe) {

                return 'La factura ya se envió electronicamente y no se puede modificar.';
            } else {

                if ( ( (intval($tipoDoc->naturaleza) == 1) && ( intval($nuevoItem->saldo) == intval($nuevoItem->total) )) || (( intval($tipoDoc->naturaleza) == 4) && ( intval($nuevoItem->saldo == 0) )) ){


                    $nuevoItem->fill($request->all());

                    if (intval($tipoDoc->naturaleza) == 1) {
                        $nuevoItem->saldo = $nuevoItem->total;
                    } elseif (intval($tipoDoc->naturaleza) == 4){
                        $nuevoItem->saldo = 0;
                    }

                    $nuevoItem->save();

                    $lineas = FacPivotMovProducto::where('fac_mov_id', $id)->get();

                    foreach ($lineas as $linea) {
                        $linea->delete();
                    }

                    foreach ($request->lineas as $linea) {
                        $nuevoPivot = new FacPivotMovProducto($linea);
                        $nuevoPivot->fac_mov_id = $nuevoItem->id;
                        $nuevoPivot->precio = intval($linea['precio']);
                        $nuevoPivot->save();

                        if ($tipoDoc->naturaleza == 4 || $tipoDoc->naturaleza == 1) {

                            $itemInventario = Inventario::where('producto_id', $linea['producto_id'])->where('tipo_invent','!=',2)->get()->first();
                            if ($itemInventario) {
                                $itemInventario->cantidad -= floatval($linea['cantidad']);
                                $itemInventario->save();
                            } else {
                                $nuevoInventario = new Inventario($linea);
                                $nuevoInventario->cantidad = - $linea['cantidad'];
                                $nuevoInventario->costo_promedio = 0;
                                $nuevoInventario->tipo_invent = 1;
                                $nuevoInventario->save();
                            }
                        }
                    }

                    return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];

                } else {
                    return 'La factura ya esta asociada a otro documento.';
                }
            }

        } else {
            return 'No se puede modificar el tipo de documento, Debe ser anulado.';
        }
    }

    public function editarFactura ($tipodoc_id, $consecmov) {

        $mov = FacMovimiento::where('fac_tipo_doc_id', $tipodoc_id)->where('consecutivo', $consecmov)->get();

        if ( count($mov) > 0 ) {
            $mov = $mov->first();

            $lineas = FacPivotMovProducto::lineasParaEditarFactura($mov->id);

            $sucursal = TerceroSucursal::find($mov->cliente_id);

            $pagos = FacFormaPago::all();

            foreach ($pagos as $item) {
                $pagoMov = FacPivotMovFormapago::where('fac_mov_id', $mov->id)->where('fac_formas_pago_id', $item->id)->get()->first();

                if ($pagoMov) {
                    $item->valor = $pagoMov->valor_recibido;
                }
            }

            if (count(FacPivotMovVendedor::where('fac_mov_id', $mov->id)->get()) > 0 ) {
                $vendedor = GenVendedor::find(FacPivotMovVendedor::where('fac_mov_id', $mov->id)->get()->first()->gen_vendedor_id)->codigo_unico;
            } else {
                $vendedor = GenVendedor::find(1);
            }



            return [
                'mov' => $mov,
                'pagos' => $pagos,
                'lineas' => $lineas,
                'sucursal' => $sucursal->id,
                'vendedor' => $vendedor
            ];


        } else {
            return 'No existe un documento con este consecutivo y tipo de documento.';
        }

    }

    public function generatePDF($id)
    {

        $movimiento = FacMovimiento::find($id);
        $data = null;
        $movimiento->qr = substr( $movimiento->qr, strpos($movimiento->qr, 'https://'));
        $tipoDoc = $movimiento->tipoDoc;

        $empresa = GenEmpresa::find(1);

        $lineas = FacPivotMovProducto::porMovimiento($id); //Details

        $sucursal = TerceroSucursal::find($movimiento->cliente_id);
        $tercero = $sucursal->tercero;

        $IdPrincipal = FacCruce::where('fac_mov_secundario', $id)->get()->first();
        $relatedDocument = null;
        if ($IdPrincipal != null){
            $relatedDocument = FacMovimiento::find($IdPrincipal->fac_mov_principal);
            $data = ['movimiento' => $movimiento, 'lineas' => $lineas, 'tercero' => $tercero, 'sucursal' => $sucursal, 'tipoDoc' => $tipoDoc, 'empresa' => $empresa, 'relatedDocument' => $relatedDocument];
            // dd($data);
        } else {
            $data = ['movimiento' => $movimiento, 'lineas' => $lineas, 'tercero' => $tercero, 'sucursal' => $sucursal, 'tipoDoc' => $tipoDoc, 'empresa' => $empresa, 'relatedDocument' => $relatedDocument];
        }
            // dd($data);
        if ($tipoDoc->formato_impresion == 1) {
            $pdf = PDF::loadView('facturacion.factura', $data);
        } elseif ($tipoDoc->formato_impresion == 2) {
            $pdf = PDF::loadView('facturacion.cuentacobro', $data);
        } elseif ($tipoDoc->formato_impresion == 3) {
            $pdf = PDF::loadView('facturacion.traslado', $data);
        }

        return $pdf->stream();
    }

    public function printPOS($id, $copia){

        $nuevoItem = FacMovimiento::find($id);
        $lineas = FacPivotMovProducto::where('fac_mov_id', $id)->get();
        $tipoDoc = FacTipoDoc::find($nuevoItem->fac_tipo_doc_id);
        $empresa = GenEmpresa::find(1);
        $sucursal = TerceroSucursal::find($nuevoItem->cliente_id);
        $tercero = $sucursal->Tercero;
        $municipio = GenMunicipio::find($empresa->gen_municipios_id);
        $departamento = GenDepartamento::find($municipio->departamento_id);
        $caractPorlinea = caracteres_linea_pos();
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $totales_por_unidad = array();
        $totales_por_unidad['Kgs'] = 0;
        $totales_por_unidad['Und'] = 0;

        // $printer->setJustification(Printer::JUSTIFY_CENTER);

        if ($tipoDoc->naturaleza == 4) {

            $caractPorlinea = caracteres_linea_pos();

            $pagos = FacPivotMovFormapago::where('fac_mov_id', $id)->get();

            // ENCABEZADO
            if ($empresa->print_logo_pos) {
                $img = EscposImage::load("../public/images/logo1.png");
                $printer->graphics($img);
            }
            $etiqueta = str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->razon_social), $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->nombre), $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("NIT: ".$empresa->nit, $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->tipo_regimen), $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->direccion), $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($municipio->nombre)." - ".strtoupper($departamento->nombre), $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("TEL: ".$empresa->telefono, $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);

            // DATOS DE FACTURACION
            if ($tipoDoc->prefijo) {
                $etiqueta .= str_pad("DE: ".strtoupper($tipoDoc->prefijo).' '.$tipoDoc->ini_num_fac. " A ".strtoupper($tipoDoc->prefijo).' '.$tipoDoc->fin_num_fac, $caractPorlinea, " ", STR_PAD_BOTH);
            } else {
                $etiqueta .= str_pad("DE: ".$tipoDoc->ini_num_fac. " A ".$tipoDoc->fin_num_fac, $caractPorlinea, " ", STR_PAD_BOTH);
            }
            $etiqueta .= str_pad("N RESOLUCION: ".$tipoDoc->resolucion, $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("FECHA: ".$tipoDoc->fec_resolucion, $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);
            $etiqueta .= str_pad("VIGENCIA 18 MESES", $caractPorlinea, "-", STR_PAD_BOTH);

            // DATOS DE LA VENTA
            $etiqueta .= str_pad("CAJERO: ".Auth::user()->name." - FECHA: ".$nuevoItem->fecha_facturacion, $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("VENDEDOR: ".eliminar_acentos(GenVendedor::find(FacPivotMovVendedor::where('fac_mov_id', $id)->get()->first()->gen_vendedor_id)->nombre), $caractPorlinea, " ", STR_PAD_BOTH);
            if ($copia == 1) {
                $etiqueta .= str_pad("   COPIA   ", $caractPorlinea, "*", STR_PAD_BOTH);
            }

            if ($tipoDoc->prefijo) {
                $etiqueta .= str_pad("FACTURA DE VENTA # ".strtoupper($tipoDoc->prefijo)." ".$nuevoItem->consecutivo, $caractPorlinea, " ", STR_PAD_BOTH);
            } else {
                $etiqueta .= str_pad("FACTURA DE VENTA # ".$nuevoItem->consecutivo, $caractPorlinea, " ", STR_PAD_BOTH);
            }
            if ($copia == 1) {
                $etiqueta .= str_pad("   COPIA   ", $caractPorlinea, "*", STR_PAD_BOTH);
            }
            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);

            // DATOS DEL CLIENTE
            $etiqueta .= str_pad(eliminar_acentos(substr($tercero->nombre, 0, 41)), $caractPorlinea, " ", STR_PAD_RIGHT);
            if ($tercero->digito_verificacion) {
                $etiqueta .= str_pad("DOC: ".$tercero->documento.'-'.$tercero->digito_verificacion.' - TEL: '.$sucursal->telefono, $caractPorlinea, " ", STR_PAD_RIGHT);
            } else {
                $etiqueta .= str_pad("DOC: ".$tercero->documento.' - TEL: '.$sucursal->telefono, $caractPorlinea, " ", STR_PAD_RIGHT);
            }
            $etiqueta .= str_pad("DIRECCION: ".$sucursal->direccion, $caractPorlinea, " ", STR_PAD_RIGHT);

            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);

            // PRODUCTOS
            $etiqueta .= str_pad("CODIGO   PRODUCTO   TOTAL | IVA", $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);
            $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);
            $totalGeneral = 0;

            foreach ($lineas as $linea) {

                // linea 1
                $etiqueta .= str_pad( substr(Producto::find($linea->producto_id)->codigo, 0 , 3) , 3, "0", STR_PAD_LEFT);
                $etiqueta .= ' ';
                $nombre = strtoupper(Producto::find($linea->producto_id)->nombre);
                $etiqueta .= str_pad(eliminar_acentos(substr($nombre, 0, $caractPorlinea - 19)), $caractPorlinea - 19, " ", STR_PAD_RIGHT);
                $etiqueta .= ' ';
                $total = intval($linea['precio']) * floatval($linea['cantidad']);
                $total =  number_format($total, 0, ',', '.');
                $etiqueta .= str_pad($total, 10, " ", STR_PAD_LEFT);
                $etiqueta .= ' |';
                $etiqueta .= str_pad($linea['iva'], 2, " ", STR_PAD_LEFT);
                // linea 2
                $etiqueta .= '    ';
                $etiqueta .= str_pad(number_format($linea['cantidad'], 3, ',', '.'), 7, " ", STR_PAD_LEFT);
                $etiqueta .= ' ';
                $etiqueta .= GenUnidades::find(Producto::find($linea['producto_id'])->gen_unidades_id)->abrev_pos;
                $etiqueta .= ' X $';
                $etiqueta.= str_pad(number_format($linea['precio'], 0, ',', '.'), 7, " ", STR_PAD_LEFT);
                $etiqueta .= ' ';    //22
                if ((intval($linea['descporcentaje']) != 0) && ($caractPorlinea > 40)) {
                    $etiqueta .= 'Desc ';
                    $etiqueta .= str_pad($linea['descporcentaje'], 2, " ", STR_PAD_LEFT);
                    $etiqueta .= str_pad('%', $caractPorlinea - 35, " ", STR_PAD_RIGHT);
                } else {
                    $etiqueta .= str_pad('', $caractPorlinea - 27, " ", STR_PAD_LEFT);
                }

                $totales_por_unidad[GenUnidades::find(Producto::find($linea['producto_id'])->gen_unidades_id)->abrev_pos] += $linea['cantidad'];
            }

            $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad('Total Kilos: '.$totales_por_unidad['Kgs'], $caractPorlinea, " ", STR_PAD_RIGHT);
            $etiqueta .= str_pad('Total Unidades: '.$totales_por_unidad['Und'], $caractPorlinea, " ", STR_PAD_RIGHT);
            // TOTAL
            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);

            $etiqueta .= str_pad('SUBTOTAL', $caractPorlinea - 12, " ", STR_PAD_RIGHT);
            $etiqueta .= '$';
            $etiqueta .= ' ';
            $etiqueta .= str_pad(number_format($nuevoItem->subtotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

            $etiqueta .= str_pad('DESCUENTO', $caractPorlinea - 12, " ", STR_PAD_RIGHT);
            $etiqueta .= '$';
            $etiqueta .= ' ';
            $etiqueta .= str_pad(number_format($nuevoItem->descuento, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

            $etiqueta .= str_pad('IVA', $caractPorlinea - 12, " ", STR_PAD_RIGHT);
            $etiqueta .= '$';
            $etiqueta .= ' ';
            $etiqueta .= str_pad(number_format($nuevoItem->ivatotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

            $etiqueta .= str_pad('TOTAL', $caractPorlinea - 12, " ", STR_PAD_RIGHT);
            $etiqueta .= '$';
            $etiqueta .= ' ';
            $etiqueta .= str_pad(number_format($nuevoItem->total, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

            //PAGOS
            $totalPagos = 0;

            $etiqueta .= str_pad("PAGOS", $caractPorlinea, "-", STR_PAD_BOTH);

            foreach ($pagos as $pago) {
                if ($pago['valor_recibido'] > 0) {
                    $etiqueta .= str_pad(eliminar_acentos(strtoupper(FacFormaPago::find($pago['fac_formas_pago_id'])->nombre)), $caractPorlinea - 12, " ", STR_PAD_RIGHT);
                    $etiqueta .= '$';
                    $etiqueta .= ' ';
                    $etiqueta .= str_pad(number_format($pago['valor_recibido'], 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                    $totalPagos = intval($totalPagos) + intval($pago['valor_recibido']);
                }
            }

            $etiqueta .= str_pad("----------", $caractPorlinea, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('TOTAL PAGOS', $caractPorlinea - 12, " ", STR_PAD_RIGHT);
            $etiqueta .= '$';
            $etiqueta .= ' ';
            $etiqueta .= str_pad(number_format($totalPagos, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);

            $etiqueta .= str_pad('DEVOLUCION', $caractPorlinea - 12, " ", STR_PAD_RIGHT);
            $etiqueta .= '$';
            $etiqueta .= ' ';
            $etiqueta .= str_pad(number_format($totalPagos - $nuevoItem->total, 0, ',', '.'), 10, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);

            // DESCRIPCION IMPUESTOS
            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);
            $etiqueta .= str_pad("IMPUESTOS", $caractPorlinea, "-", STR_PAD_BOTH);
            $etiqueta .= str_pad('IMP.        BASE       IVA', $caractPorlinea, " ", STR_PAD_BOTH);

            $arrayIvas = [];
            $arrayImpuestos = [];

            foreach ($lineas as $linea) {

                if (array_search($linea['iva'], $arrayIvas) === false) {
                    array_push($arrayIvas, $linea['iva']);
                }
            }

            foreach ($arrayIvas as $item) {
                $subtotal = 0;
                foreach ($lineas as $linea) {
                    if ($linea['iva'] == $item){
                        $subtotal = $subtotal + intval(((intval($linea['precio']) - ( intval($linea['precio']) * ( intval($linea['descporcentaje'])/100) ))) * floatval($linea['cantidad']));
                    }
                }
                $etiqueta .= str_pad(number_format($item, 0, ',', '.').'%', 3, " ", STR_PAD_LEFT);
                $etiqueta .= str_pad("", 12, " ", STR_PAD_RIGHT);
                $etiqueta .= str_pad(number_format(intval($subtotal), 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                $etiqueta .= str_pad("", 13, " ", STR_PAD_RIGHT);
                $etiqueta .= str_pad(number_format(intval(intval($subtotal) * (intval($item)/100)), 0, ',', '.'), 10, " ", STR_PAD_LEFT);
            }

            $etiqueta .= str_pad("", $caractPorlinea, "-", STR_PAD_BOTH);

            $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);

            $etiqueta .= str_pad("Impreso desde SGC de Byteco S.A.S.", $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("Nit: 901389565-8", $caractPorlinea, " ", STR_PAD_BOTH);

            $etiqueta .= str_pad("GRACIAS POR SU COMPRA !!", $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("Fecha Impresion " . date('Y-m-d H:i:s'), 48, " ", STR_PAD_RIGHT);

            $printer->text($etiqueta);
            $printer->feed(2);
            $printer->cut();
            $printer->pulse();
            $printer->close();

        } else if ($tipoDoc->naturaleza == 1) {

            for ($i = 0; $i < 1 ; $i++) {

                // $etiqueta = str_pad("Fecha Impresion " . date('d-m-Y H:i:s'), 48, " ", STR_PAD_RIGHT);
                $etiqueta = str_pad("", 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad(eliminar_acentos(strtoupper($tipoDoc->nombre_alt)), 48, ".", STR_PAD_BOTH);
                // ENCABEZADO
                if ($tipoDoc->formato_impresion == 1) {
                    $img = EscposImage::load("../public/images/logo1.png");
                    $printer -> graphics($img);
                    $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
                    $etiqueta .= str_pad(strtoupper($empresa->razon_social), 48, " ", STR_PAD_BOTH);
                    $etiqueta .= str_pad(strtoupper($empresa->nombre), 48, " ", STR_PAD_BOTH);
                    $etiqueta .= str_pad("NIT: ".$empresa->nit, 48, " ", STR_PAD_BOTH);
                    $etiqueta .= str_pad(strtoupper($empresa->tipo_regimen), 48, " ", STR_PAD_BOTH);
                }

                $etiqueta .= str_pad(strtoupper($empresa->direccion), 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad(strtoupper($municipio->nombre)." - ".strtoupper($departamento->nombre), 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("TEL: ".$empresa->telefono, 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);

                if ($tipoDoc->formato_impresion == 1) {
                    // DATOS DE FACTURACION
                    if ($tipoDoc->prefijo) {
                        $etiqueta .= str_pad("DE: ".strtoupper($tipoDoc->prefijo).' '.$tipoDoc->ini_num_fac. " A ".strtoupper($tipoDoc->prefijo).' '.$tipoDoc->fin_num_fac, 48, " ", STR_PAD_BOTH);
                    } else {
                        $etiqueta .= str_pad("DE: ".$tipoDoc->ini_num_fac. " A ".$tipoDoc->fin_num_fac, 48, " ", STR_PAD_BOTH);
                    }
                    $etiqueta .= str_pad("N RESOLUCION: ".$tipoDoc->resolucion, 48, " ", STR_PAD_BOTH);
                    $etiqueta .= str_pad("FECHA: ".$tipoDoc->fec_resolucion, 48, " ", STR_PAD_BOTH);
                    $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);
                }

                $etiqueta .= str_pad(eliminar_acentos(strtoupper($tipoDoc->nombre_alt)).' # '.$nuevoItem->consecutivo, 48, " ", STR_PAD_BOTH);

                $etiqueta .= str_pad("FECHA FACTURACION: ".$nuevoItem->fecha_facturacion, 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("FECHA VENCIMIENTO: ".$nuevoItem->fecha_vencimiento, 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);

                // DATOS DEL CLIENTE
                $etiqueta .= tercero_pos($tercero->nombre,48);
                if ($tercero->digito_verificacion) {
                    $etiqueta .= str_pad("DOC: ".$tercero->documento.'-'.$tercero->digito_verificacion.' - TEL: '.$sucursal->telefono, 48, " ", STR_PAD_RIGHT);
                } else {
                    $etiqueta .= str_pad("DOC: ".$tercero->documento.' - TEL: '.$sucursal->telefono, 48, " ", STR_PAD_RIGHT);
                }
                $etiqueta .= str_pad("DIRECCION: ".$sucursal->direccion, 48, " ", STR_PAD_RIGHT);

                $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);

                // PRODUCTOS
                $etiqueta .= "CODIGO              PRODUCTO         TOTAL | IVA";
                $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
                $totalGeneral = 0;

                foreach ($lineas as $linea) {
                    // linea 1
                    $etiqueta .= str_pad(Producto::find($linea->producto_id)->codigo, 3, "0", STR_PAD_LEFT);
                    $etiqueta .= ' ';
                    $nombre = strtoupper(Producto::find($linea->producto_id)->nombre);
                    $nombre = str_replace('ñ', 'N', $nombre);
                    $etiqueta .= str_pad($nombre, 29, " ", STR_PAD_RIGHT);
                    $etiqueta .= ' ';
                    $total = intval($linea['precio']) * floatval($linea['cantidad']);
                    $total =  number_format($total, 0, ',', '.');
                    $etiqueta .= str_pad($total, 10, " ", STR_PAD_LEFT);
                    $etiqueta .= ' |';
                    $etiqueta .= str_pad($linea['iva'], 2, " ", STR_PAD_LEFT);
                    // linea 2
                    $etiqueta .= '    ';
                    $etiqueta .= str_pad(number_format($linea['cantidad'], 3, ',', '.'), 6, " ", STR_PAD_LEFT);
                    $etiqueta .= ' ';
                    $etiqueta .= GenUnidades::find(Producto::find($linea['producto_id'])->gen_unidades_id)->abrev_pos;
                    $etiqueta .= ' ';
                    $etiqueta .= 'X';
                    $etiqueta .= ' ';
                    $etiqueta .= '$';
                    $etiqueta.= str_pad(number_format($linea['precio'], 0, ',', '.'), 7, " ", STR_PAD_LEFT);
                    $etiqueta .= ' ';    //22
                    if ((intval($linea['descporcentaje']) != 0) && ($caractPorlinea > 40)) {
                        $etiqueta .= 'Desc ';
                        $etiqueta .= str_pad($linea['descporcentaje'], 2, " ", STR_PAD_LEFT);
                        $etiqueta .= str_pad('%', 13, " ", STR_PAD_RIGHT);
                    } else {
                        $etiqueta .= '                      ';
                    }
                }

                // TOTAL
                $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);

                $etiqueta .= str_pad('SUBTOTAL', 36, " ", STR_PAD_RIGHT);
                $etiqueta .= '$';
                $etiqueta .= ' ';
                $etiqueta .= str_pad(number_format($nuevoItem->subtotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

                $etiqueta .= str_pad('DESCUENTO', 36, " ", STR_PAD_RIGHT);
                $etiqueta .= '$';
                $etiqueta .= ' ';
                $etiqueta .= str_pad(number_format($nuevoItem->descuento, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

                $etiqueta .= str_pad('IVA', 36, " ", STR_PAD_RIGHT);
                $etiqueta .= '$';
                $etiqueta .= ' ';
                $etiqueta .= str_pad(number_format($nuevoItem->ivatotal, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

                $etiqueta .= str_pad('TOTAL', 36, " ", STR_PAD_RIGHT);
                $etiqueta .= '$';
                $etiqueta .= ' ';
                $etiqueta .= str_pad(number_format($nuevoItem->total, 0, ',', '.'), 10, " ", STR_PAD_LEFT);

                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", 48, "-", STR_PAD_BOTH);
                $etiqueta .= str_pad("Nombre y Sello del cliente", 48, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("", $caractPorlinea, " ", STR_PAD_BOTH);
                $etiqueta .= str_pad("Fecha Impresion " . date('Y-m-d H:i:s'), 48, " ", STR_PAD_RIGHT);

                $printer->text($etiqueta);
                $printer->feed(2);
                $printer->cut();
                $printer->close();
            }

        }
    }



    public function dataFacturaElectronica($id)
    {

        $movimiento = FacMovimiento::find($id);
        $tipoDoc = $movimiento->tipoDoc;

        $cliente = TerceroSucursal::find($movimiento->cliente_id);

        $cliente->tercero;

        $cliente->soenac_regimen = SoenacRegimen::find($cliente->tercero->soenac_regim_id)->soenac_id;
        $cliente->soenac_responsabilidad = SoenacResponsabilidad::find($cliente->tercero->soenac_responsab_id)->soenac_id;
        $cliente->soenac_tipo_documento = SoenacTipoDocumento::find($cliente->tercero->soenac_tipo_documento_id)->soenac_id;
        $cliente->soenac_tipo_organizacion = SoenacTipoOrg::find($cliente->tercero->soenac_tipo_org_id)->soenac_id;

        $empresa = GenEmpresa::find(1);

        $lineas = FacPivotMovProducto::where('fac_mov_id', $id)->get();

        foreach ($lineas as $linea) {
            $producto = Producto::find($linea->producto_id);

            $unidad = GenUnidades::find($producto->gen_unidades_id);

            $linea->unit_measure_id = $unidad->soenac_unid_api_id;

            // $linea->tax_id = GenIva::find();

            $linea->description = $producto->nombre;
            $linea->code = $producto->codigo;

        }

        $data = ['movimiento' => $movimiento,
                 'tipoDoc' => $tipoDoc,
                 'cliente' => $cliente,
                 'empresa' => $empresa,
                 'lineas' => $lineas];

        if ($tipoDoc->naturaleza != 1) {
            $movPrimario = FacMovimiento::find(FacCruce::where('fac_mov_secundario', $movimiento->id)->get()->first()->fac_mov_principal);
            $tipoDocPrimario = FacTipoDoc::find($movPrimario->fac_tipo_doc_id);

            $data = ['movimiento' => $movimiento,
                 'tipoDoc' => $tipoDoc,
                 'cliente' => $cliente,
                 'empresa' => $empresa,
                 'movPrimario' => $movPrimario,
                 'tipoDocPrimario' => $tipoDocPrimario,
                 'lineas' => $lineas];
        }

        return $data;
    }

    public function agregarCufe(Request $request, $id)
    {
        $model = FacMovimiento::find($id);


        if (is_null($model->cufe)) {
            $model->fill($request->all());
        } else {
            $model->estado_fe = $request->estado_fe;
        }


        if ($request->estado_fe != 3)
        {

            $datafe = FacDataFE::where('fac_mov_id', $id)->get();

            if (count($datafe) > 0){
                $nuevoItem = $datafe->first();

                if ($nuevoItem->zip_key == null) {
                    $nuevoItem->zip_key = $request->zip_key;
                }
                if ($nuevoItem->zip_name == null) {
                    $nuevoItem->zip_name = $request->zip_name;
                }
                if ($nuevoItem->url_acceptance == null) {
                    $nuevoItem->url_acceptance = $request->url_acceptance;
                }
                if ($nuevoItem->url_rejection == null) {
                    $nuevoItem->url_rejection = $request->url_rejection;
                }
                if ($nuevoItem->pdf_base64_bytes == null) {
                    $nuevoItem->pdf_base64_bytes = $request->pdf_base64_bytes;
                }
                if ($nuevoItem->dian_response_base64_bytes == null) {
                    $nuevoItem->dian_response_base64_bytes = $request->dian_response_base64_bytes;
                }
                if ($nuevoItem->application_response_base64_bytes == null) {
                    $nuevoItem->application_response_base64_bytes = $request->application_response_base64_bytes;
                }

                $nuevoItem->save();
            } else {
                $nuevoItem = new FacDataFE($request->all());
                $nuevoItem->fac_mov_id = $id;
                $nuevoItem->save();
            }

        }


        $v = $model->save();

        if ($v) {
            return 'done';
        } else {
            return 'Hubo un error al guardar';
        }
    }

    public function eliminarDatosHabilitacion()
    {

        $tiposDocHabilitacion = FacTipoDoc::where('habilitacion_fe', 1)->get();

        foreach ($tiposDocHabilitacion as $tipodoc) {

            $movRelacionados = FacMovRelacionado::where('fac_tipo_doc_prim_id', $tipodoc->id)->get();

            foreach ($movRelacionados as $movRelacionado) {
                $movRelacionado->delete();
            }

            $movimientosHabilitacion = FacMovimiento::where('fac_tipo_doc_id', $tipodoc->id)->get();

            foreach ($movimientosHabilitacion as $movimiento) {

                $cruces = FacCruce::where('fac_mov_principal', $movimiento->id)->get();

                foreach ($cruces as $cruce) {
                    $cruce->delete();
                }

                $lineasHabilitacion = FacPivotMovProducto::where('fac_mov_id', $movimiento->id)->get();

                foreach ($lineasHabilitacion as $linea) {
                    $linea->delete();
                }

                $movVendedor = FacPivotMovVendedor::where('fac_mov_id', $movimiento->id)->get();

                foreach ($movVendedor as $vendedor) {
                    $vendedor->delete();
                }

                $movimiento->delete();
            }

            $tipodoc->delete();
        }

        return 'done';
    }

    public function allNotas ()
    {
        // dd('hola');
        $index = FacMovimiento::allNotas();
        return  $index;
    }

    //Busca las notas creditos o debitos relacionadas a una factura
    public function notaPorId ($id)
    {
        // dd('hola');
        $index = FacMovimiento::notasRelacionadas($id);
        return  $index;
    }

    //Busca los recibos relacionadas a una factura -- Pendiente
    public function reciboPorId ($id)
    {
        // recibos
        $index = FacMovimiento::reciboRelacionados($id);
        return  $index;
    }

    public function testimpresioncxc(){

        // $datos = FacMovimiento::todosConTipoSucursalGrupoTipoCustom();
        // // $clientes  = $lineas->unique('documento')->sortBy('tercero');
        // $quantityNotes = $datos->unique('id')->where('notescount', '>', 0)->count();
        // $quantityReceipts = $datos->unique('id')->where('ReceipsCount','>', 0)->count();
        // $print = 'Notas Globales  ' . $quantityNotes . '  Recibos Globales' . $quantityReceipts;
        // return $print;
        // ventasPorFechaTest
        $index= Producto::todosConGrupos();
        return $index;
    }

    public function sendFactToSoenac(Request $request){

        $http = http_post($request->url, $request->body);

        return $http;
    }

    public function limpiarTiquetesBascula (){
        FacMovimiento::limpiarTiquetesBascula();
    }

    public static function  descargarDevolucionInventario($id){
        $lineas = FacPivotMovProducto::porMovimiento($id);
        foreach($lineas as $linea){
            self::afectarInventario($linea->producto_id, $linea->cantidad, 0);
        }
    }
}
