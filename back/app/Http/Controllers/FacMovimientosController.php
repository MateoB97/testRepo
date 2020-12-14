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

        // $i = 2;

        // foreach ($terceros as $item) {
        //     Tercero::create([
        //         'documento' => $item[0],
        //         'nombre' => strtolower($item[1]),
        //         'proveedor' => $item[2],
        //         'cliente' => $item[3],
        //         'empleado' => $item[4],
        //         'digito_verificacion' => $item[5],
        //         'activo' => 1,
        //         'habilitado_traslados' => 0,
        //         'soenac_regim_id' => null,
        //         'soenac_responsab_id' => null,
        //         'soenac_tipo_org_id' => null,
        //         'soenac_tipo_documento_id' => null,
        //         'registro_mercantil' => null
        //     ]);

        //     TerceroSucursal::create([
        //         'nombre' => strtolower($item[8]),
        //         'direccion' => $item[6],
        //         'telefono' => intval($item[7]),
        //         'tercero_id' => $i,
        //         'prodListaPrecio_id' => 1,
        //         'activo' => 1,
        //         'gen_municipios_id' => intval($item[9])
        //     ]);

        //     $i = $i + 1;
        // }


        $data =
            [['118','5897','1','8934','4131'],
            ['118','5914','1','12264','0'],
            ['118','5940','1','3865','0'],
            ['118','5953','1','12712','0'],
            ['118','5994','1','8386','0'],
            ['118','6246','1','24940','0'],
            ['102','3324','2','458778','5919'],
            ['118','6577','2','1218885','0'],
            ['118','6583','2','446715','0'],
            ['118','6601','2','903330','0'],
            ['118','6605','2','963975','0'],
            ['118','6610','2','532260','0'],
            ['118','6033','3','271695','200578'],
            ['118','6213','3','30000','0'],
            ['118','6266','3','201318','0'],
            ['102','3294','4','17076429','15491979'],
            ['102','3172','5','186500','0'],
            ['102','3257','5','453020','0'],
            ['102','3266','5','709080','0'],
            ['102','3271','5','79380','0'],
            ['102','3275','5','1985000','0'],
            ['102','3276','5','562500','0'],
            ['102','3280','5','625000','0'],
            ['102','3281','5','607875','0'],
            ['102','3283','5','279800','0'],
            ['102','3284','5','230800','0'],
            ['102','3285','5','270300','0'],
            ['102','3286','5','33500','0'],
            ['102','3289','5','45400','0'],
            ['102','3290','5','535000','0'],
            ['102','3291','5','29700','0'],
            ['102','3292','5','98400','0'],
            ['102','3293','5','130000','0'],
            ['102','3295','5','226000','0'],
            ['102','3296','5','44800','0'],
            ['102','3298','5','165000','0'],
            ['102','3299','5','163500','0'],
            ['102','3300','5','70000','0'],
            ['102','3302','5','926458','0'],
            ['102','3303','5','495000','0'],
            ['102','3304','5','14000','0'],
            ['102','3305','5','77400','0'],
            ['102','3306','5','52500','0'],
            ['102','3307','5','1137600','0'],
            ['102','3308','5','99000','0'],
            ['102','3310','5','42000','0'],
            ['102','3312','5','272300','0'],
            ['102','3313','5','1376600','0'],
            ['102','3314','5','56000','0'],
            ['102','3315','5','46200','0'],
            ['102','3316','5','285800','0'],
            ['102','3317','5','128000','0'],
            ['102','3319','5','187200','0'],
            ['102','3322','5','1703360','0'],
            ['102','3327','5','52875','0'],
            ['102','3329','5','130000','0'],
            ['102','3330','5','301060','0'],
            ['102','3331','5','93200','0'],
            ['102','3332','5','449260','0'],
            ['102','3333','5','256030','0'],
            ['102','3334','5','12840','0'],
            ['102','3335','5','67170','0'],
            ['102','3336','5','37170','0'],
            ['102','3337','5','73530','0'],
            ['102','3338','5','154370','0'],
            ['102','3339','5','45816','0'],
            ['102','3340','5','190520','0'],
            ['102','3341','5','533676','0'],
            ['102','3342','5','148357','0'],
            ['102','3343','5','243254','0'],
            ['102','3344','5','74860','0'],
            ['102','3345','5','169800','0'],
            ['102','3346','5','295390','0'],
            ['102','3347','5','261585','0'],
            ['102','3348','5','69896','0'],
            ['102','3349','5','452578','0'],
            ['102','3350','5','167500','0'],
            ['102','3351','5','145110','0'],
            ['102','3352','5','58392','0'],
            ['102','3353','5','70940','0'],
            ['102','3354','5','475150','0'],
            ['102','3355','5','373534','0'],
            ['102','3356','5','206130','0'],
            ['102','3357','5','326628','0'],
            ['102','3358','5','352872','0'],
            ['102','3359','5','271481','0'],
            ['102','3360','5','72570','0'],
            ['102','3361','5','234700','0'],
            ['102','3362','5','195838','0'],
            ['102','3363','5','81421','0'],
            ['102','3364','5','223180','0'],
            ['102','3365','5','175100','0'],
            ['102','3366','5','356955','0'],
            ['102','3367','5','167287','0'],
            ['102','3368','5','12000','0'],
            ['102','3369','5','16263','0'],
            ['102','3370','5','109345','0'],
            ['102','3371','5','74721','0'],
            ['102','3372','5','301055','0'],
            ['102','3373','5','338177','0'],
            ['102','3374','5','140920','0'],
            ['102','3375','5','68257','0'],
            ['118','5949','5','99000','0'],
            ['118','6042','5','213430','0'],
            ['118','6058','5','232925','0'],
            ['118','6067','5','59533','0'],
            ['118','6068','5','32880','0'],
            ['118','6071','5','52000','0'],
            ['118','6239','5','261420','0'],
            ['118','6427','5','360984','0'],
            ['118','6470','5','188553','0'],
            ['118','6505','5','294720','0'],
            ['118','6509','5','315290','0'],
            ['118','6559','5','258162','0'],
            ['118','6579','5','85119','0'],
            ['118','6597','5','92430','0'],
            ['118','6553','6','68925','0'],
            ['118','6596','6','9200','0'],
            ['118','6376','7','40570','18632'],
            ['102','2633','8','10073080','5000000'],
            ['118','6523','9','181661','0'],
            ['118','6592','9','34200','0'],
            ['118','5918','10','109230','0'],
            ['118','6477','11','153302','22110'],
            ['102','3279','12','270000','0'],
            ['102','3287','12','354700','0'],
            ['102','2541','13','7537448','1108640'],
            ['102','2556','13','7097698','0'],
            ['102','2558','13','5934946','0'],
            ['102','2562','13','204582','0'],
            ['102','2582','13','5139019','0'],
            ['102','2594','13','6446596','0'],
            ['118','6288','14','544709','0'],
            ['118','6317','14','284720','0'],
            ['118','6337','14','604334','0'],
            ['118','6379','14','484707','0'],
            ['118','6400','14','682124','0'],
            ['118','6446','14','548255','0'],
            ['118','6448','14','516979','0'],
            ['118','6465','14','601080','0'],
            ['118','6504','14','514613','0'],
            ['118','6510','14','599790','0'],
            ['118','6557','14','557468','0'],
            ['118','6565','14','582261','0'],
            ['118','6578','14','376060','0'],
            ['118','6528','15','56734','55235'],
            ['118','6569','15','88156','0'],
            ['118','6611','15','31000','0'],
            ['118','6440','16','102335','95703'],
            ['118','6442','16','79090','0'],
            ['118','6475','16','117837','0'],
            ['118','6490','16','173260','0'],
            ['118','6492','16','16770','0'],
            ['118','6507','16','90992','0'],
            ['118','6513','16','90445','0'],
            ['118','6518','16','104337','0'],
            ['118','6542','16','137465','0'],
            ['118','6580','16','164614','0'],
            ['118','6584','16','22770','0'],
            ['118','6417','17','265530','236937'],
            ['118','6522','17','245762','0'],
            ['118','6574','17','23300','0'],
            ['118','6576','17','10000','0'],
            ['118','5865','18','976489','936449'],
            ['118','5874','18','230100','0'],
            ['118','5891','18','830935','0'],
            ['118','5893','18','144327','0'],
            ['118','6302','18','697090','0'],
            ['118','6309','18','146180','0'],
            ['118','6459','18','106958','0'],
            ['118','6313','19','103195','86721'],
            ['118','6381','19','80730','0'],
            ['118','6398','19','86721','0'],
            ['118','6607','19','88853','0'],
            ['118','6600','20','239233','0'],
            ['118','6044','21','245000','192139'],
            ['118','6052','21','72556','0'],
            ['118','6075','21','122332','0'],
            ['118','6087','21','53646','0'],
            ['118','6092','21','13050','0'],
            ['118','6121','21','9851','0'],
            ['118','6125','21','27170','0'],
            ['118','6146','21','42075','0'],
            ['118','6157','21','63919','0'],
            ['118','6181','21','53375','0'],
            ['118','6183','21','56470','0'],
            ['118','6192','21','205201','0'],
            ['118','6198','21','15760','0'],
            ['118','6202','21','99978','0'],
            ['118','6205','21','367276','0'],
            ['118','6206','21','78503','0'],
            ['118','6230','21','249775','0'],
            ['118','6383','21','57400','0'],
            ['118','6488','21','48506','0'],
            ['118','6497','21','25614','0'],
            ['118','6543','21','103765','0'],
            ['118','6546','21','13191','0'],
            ['118','6595','22','144723','0'],
            ['102','3311','23','437975','0'],
            ['102','3318','23','276640','0'],
            ['102','3328','23','394850','0'],
            ['118','6586','24','146050','104266'],
            ['118','6377','25','45351','63'],
            ['118','6531','25','57182','0'],
            ['118','6561','25','10348','0'],
            ['118','6380','26','87590','70282'],
            ['118','6386','26','33970','0'],
            ['118','6405','26','47001','0'],
            ['118','6418','26','79383','0'],
            ['118','6434','26','8500','0'],
            ['118','6453','26','64956','0'],
            ['118','6462','26','54350','0'],
            ['118','6493','26','54630','0'],
            ['118','6517','26','8550','0'],
            ['118','6525','26','87691','0'],
            ['118','6532','26','32169','0'],
            ['118','6537','26','51307','0'],
            ['118','6602','26','14465','0'],
            ['102','2218','27','671834.38','671834'],
            ['102','2240','27','1707650.06','1707650'],
            ['102','2337','27','175250.56','175251'],
            ['102','2367','27','689800.45','689800'],
            ['102','2402','27','587000.12','587000'],
            ['102','2409','27','2144410.12','2144410'],
            ['102','2411','27','1218019.97','1218020'],
            ['102','3190','27','1015800','0'],
            ['102','3199','27','802020','0'],
            ['102','3207','27','657600','0'],
            ['102','3209','27','146900','0'],
            ['102','3220','27','148800','0'],
            ['102','3227','27','935660','0'],
            ['102','3240','27','558700','0'],
            ['102','3255','27','639200','0'],
            ['102','3260','27','499000','0'],
            ['102','3262','27','726750','0'],
            ['102','3263','27','287000','0'],
            ['102','3264','27','594800','0'],
            ['102','3265','27','408200','0'],
            ['102','3267','27','718400','0'],
            ['102','3268','27','420600','0'],
            ['102','3270','28','822900','0'],
            ['118','6265','28','19701','0'],
            ['118','6307','28','104137','0'],
            ['118','6437','29','69304','0'],
            ['118','6369','29','178910','0'],
            ['118','6388','29','139400','0'],
            ['118','6401','29','130560','0'],
            ['118','6408','29','204160','0'],
            ['118','6421','29','342905','0'],
            ['118','6439','29','269980','0'],
            ['118','6449','29','197490','0'],
            ['118','6452','29','207325','0'],
            ['118','6469','29','238695','0'],
            ['118','6472','29','202160','0'],
            ['118','6478','29','142000','0'],
            ['118','6483','29','59070','0'],
            ['118','6489','29','279805','0'],
            ['118','6499','29','244895','0'],
            ['118','6501','29','141500','0'],
            ['118','6515','29','396870','0'],
            ['118','6519','29','198560','0'],
            ['118','6526','29','175500','0'],
            ['118','6527','29','82390','0'],
            ['118','6538','29','120040','0'],
            ['118','6540','29','331865','0'],
            ['118','6545','29','144320','0'],
            ['118','6551','29','266770','0'],
            ['118','6560','29','284230','0'],
            ['118','6567','29','240260','0'],
            ['118','6570','29','442810','0'],
            ['118','6575','29','222910','0'],
            ['118','6593','29','347305','0'],
            ['118','6598','29','286900','0'],
            ['118','6604','29','199780','0'],
            ['118','6608','29','153565','0'],
            ['118','6612','30','740145','0'],
            ['118','6464','30','140315','0'],
            ['118','6473','30','138850','0'],
            ['118','6486','30','109700','0'],
            ['118','6495','30','118590','0'],
            ['118','6508','30','297330','0'],
            ['118','6514','30','94300','0'],
            ['118','6533','30','119710','0'],
            ['118','6547','30','52910','0'],
            ['118','6558','30','91170','0'],
            ['118','6564','30','92265','0'],
            ['118','6572','30','166478','0'],
            ['118','6582','30','88080','0'],
            ['118','6599','31','107815','0'],
            ['118','6334','31','81351','25424'],
            ['118','6335','31','34765','0'],
            ['118','6338','31','62437','0'],
            ['118','6343','31','8599','0'],
            ['118','6352','31','221054','0'],
            ['118','6353','31','78367','0'],
            ['118','6357','31','35025','0'],
            ['118','6361','31','84083','0'],
            ['118','6363','31','91965','0'],
            ['118','6399','31','20000','0'],
            ['118','6429','31','58075','0'],
            ['118','6468','31','50020','0'],
            ['118','6498','31','247983','0'],
            ['118','6544','31','83471','0'],
            ['118','6563','31','56530','0'],
            ['118','6585','31','84532','83532'],
            ['118','6588','31','80204','0']];

        $conseRec1 = 1;
        $conseRec2 = 1;

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
            $nuevoItem->fecha_vencimiento = '21/09/2020';
            $nuevoItem->fecha_facturacion = '10/10/2020';
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
            array_push($tipos, $tipo_doc->fac_tipo_doc_id);
        }

        // return $tipos;

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

        if ($tipoDoc->resolucion_soenac_id != 0 && !is_null($tipoDoc->resolucion_soenac_id) ){
            return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id, true]];
        } else {
            return ['callback', [$nuevoItem->consecutivo, $nuevoItem->id]];
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
            // dd($data);
        }

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

            $printer->text($etiqueta);
            $printer->feed(2);
            $printer->cut();
            $printer->pulse();
            $printer->close();

        } else if ($tipoDoc->naturaleza == 1) {

            for ($i = 0; $i < 1 ; $i++) {

                $etiqueta = str_pad(eliminar_acentos(strtoupper($tipoDoc->nombre_alt)), 48, ".", STR_PAD_BOTH);

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

                $printer->text($etiqueta);
                $printer->feed(2);
                $printer->cut();
                $printer->close();
            }

        }

    }

    public function saldosEnCartera()
    {

        $lineas = FacMovimiento::saldosEnCartera();

        $fecha = Carbon::now();

        $data = ['lineas' => $lineas, 'fecha' => $fecha];

        $pdf = PDF::loadView('facturacion.saldocarteraclientes', $data);

        return $pdf->stream();
    }


    public function saldosEnCarteraT80()
    {
        // $user = User::find(Auth::user())->first();
        // $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        //Usuario de prueba
        $user = User::find(1);
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(1)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $printer -> setFont(1);
        $printer->feed(1);
        $caracLinea = caracteres_linea_posT80FontOne();

        $empresa = GenEmpresa::find(1);

        $etiqueta  ='';// pad('', 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->razon_social), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->nombre),0,' ');
        $etiqueta .= pad("NIT: ".$empresa->nit, $caracLinea, 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->tipo_regimen), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->direccion), 0, ' ');
        $etiqueta .= pad("TEL: ".$empresa->telefono, 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $etiqueta .= pad('CUENTAS POR COBRAR', 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $hoy = date("m.d.y");
        $time = date('H:i');
        $etiqueta  .= pad('Fecha Impresion:='. $hoy .' - '.$time , 1, ' ');
        $etiqueta  .= pad('', 0, ' ');

        $granTotal = 0;
        $granSaldo = 0;
        $granAbono = 0;
        $row ='';
        $lineas = FacMovimiento::saldosEnCartera();
        $clientes  = $lineas->unique('documento')->sortBy('tercero');
        $sucursales = $lineas->unique('sucursal_id')->sortBy('nombre');
            foreach($clientes as $cliente){//ClientByClient
                $etiqueta .= pad($cliente->documento.'    '.$cliente->tercero .'  Tel: '. $cliente->telefono, 0, ' ');
                    foreach($sucursales->where('documento',$cliente->documento) as $sucursal){//BranchOfficeByBranchOffice
                        $etiqueta .= pad($sucursal->sucursal.'    '.$sucursal->telefono, 1, ' ');
                            foreach($lineas->where('sucursal',$sucursal->sucursal)->where('documento', $sucursal->documento) as $linea){//DetailsByBranchOffice
                                $row = str_pad(strval($linea->consecutivo),9, ' ');
                                $row .= str_pad($linea->fecha_facturacion,12, ' ');
                                $row .= str_pad('$'.strval(number_format($linea->total, 0, ',', '.')),15, ' ');
                                $row .= str_pad('$'.strval(number_format(($linea->total - $linea->saldo), 0, ',', '.')),14, ' ');
                                $row .= str_pad('$'.strval(number_format($linea->saldo, 0, ',', '.')),15, ' ');
                                // $etiqueta .= pad ($linea->consecutivo   .'   '.$linea->fecha_facturacion  .'   $ '.number_format($linea->total, 0, ',', '.'). '   $ '. number_format(($linea->total - $linea->saldo), 0, ',', '.'). '   $ '. number_format($linea->saldo, 0, ',', '.'), 1, ' ');
                                $etiqueta .= pad ($row, 1, ' ');
                                $row = '';
                                // $etiqueta .= pad('Total: $ '.number_format($linea->total, 0, ',', '.'). '      Abono: $ '. number_format(($linea->total - $linea->saldo), 0, ',', '.'). '     Saldo: $ '. number_format($linea->saldo, 0, ',', '.'), 1, ' ');
                            }
                        $sucursalTotal = $lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('total') - ($lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('total') - $lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('saldo'));
                        $etiqueta .= pad('Total Sucursal: $ ' . number_format($sucursalTotal, 0, ',', '.'),1,' ');
                        // $etiqueta .= pad('Total Sucursal: ' . number_format($lineas->where('documento',$sucursal->documento)->where('sucursal', $sucursal->sucursal)->sum('saldo'), 0, ',', '.'),1,' ');
                        $etiqueta  .= pad('', 0, '.');
                    }
                    $totalCliente = $lineas->where('documento',$cliente->documento)->sum('total') - ($lineas->where('documento',$cliente->documento)->sum('total') - $lineas->where('documento',$cliente->documento)->sum('saldo'));
                    $etiqueta .= pad('Total: $ ' . number_format($lineas->where('documento',$cliente->documento)->sum('saldo'), 0, ',', '.'),1,' ');
                    $etiqueta  .= pad('', 0, '-');
            }
        //Totales
        $granTotal = number_format($lineas->sum('total'), 0, ',', '.');
        $granSaldo = number_format($lineas->sum('saldo'), 0, ',', '.');
        $granAbono = number_format($lineas->sum('total') - $lineas->sum('saldo'), 0, ',', '.');
        $etiqueta  .= pad('', 0, '=');
        $etiqueta .= pad("Total Cartera: $ $granTotal", 1, ' ');
        $etiqueta .= pad("Total Abonado: $ $granAbono", 1, ' ');
        $etiqueta .= pad("Total Saldo: $ $granSaldo", 1, ' ');

        $printer->text($etiqueta);
        $printer->feed(1);
        $printer->cut();
        $printer->close();

    }

    public function saldosEnCarteraxTercero($tercero_id)
    {
        $sucursales = TerceroSucursal::where('tercero_id', $tercero_id)->orderBy('nombre','asc')->get();

        $lineas = array();

        foreach ($sucursales as $sucursal) {
            $lineasSucursal = FacMovimiento::saldosEnCarteraxSucursal($sucursal->id);

            foreach ($lineasSucursal as $lin) {
                array_push($lineas, $lin);
            }
        }

        $fecha = Carbon::now();

        $data = ['lineas' => $lineas, 'fecha' => $fecha];

        $pdf = PDF::loadView('facturacion.saldocarteraclientes', $data);

        return $pdf->stream();
    }

    public function ventasNetasPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $tiposdoc = FacTipoDoc::whereIn('naturaleza', [1, 4])->get();

        $ventasTotales = array();

        $granSubtotal = 0;
        $granDescuento = 0;
        $granIva = 0;
        $granTotal =0;

        $granDevSubtotal = 0;
        $granDevDescuento = 0;
        $granDevIva = 0;
        $granDevTotal =0;

        foreach ($tiposdoc as $tipo) {
            $ventas = FacMovimiento::ventasNetasPorFecha($tipo->id, $fechaIni, $fechaFin)->first();
            $devoluciones = FacMovimiento::devolucionesVentasNetasPorFecha($tipo->id, $fechaIni, $fechaFin)->first();

            if ($ventas->total > 0 || $devoluciones->total > 0) {
                array_push($ventasTotales, [$tipo->nombre, $ventas, $devoluciones]);
                $granSubtotal += $ventas->subtotal;
                $granDescuento += $ventas->descuento;
                $granIva += $ventas->ivatotal;
                $granTotal += $ventas->total;

                $granDevSubtotal += $devoluciones->subtotal;
                $granDevDescuento += $devoluciones->descuento;
                $granDevIva += $devoluciones->ivatotal;
                $granDevTotal += $devoluciones->total;
            }
        }

        $hoy = Carbon::now();

        $data = ['ventasTotales' => $ventasTotales,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'granSubtotal' => $granSubtotal,
                 'granDescuento' => $granDescuento,
                 'granIva' => $granIva,
                 'granTotal' => $granTotal,
                 'granDevSubtotal' => $granDevSubtotal,
                 'granDevDescuento' => $granDevDescuento,
                 'granDevIva' => $granDevIva,
                 'granDevTotal' => $granDevTotal,
                 'hoy' => $hoy, ];

        $pdf = PDF::loadView('facturacion.ventasnetasporfecha', $data);

        return $pdf->stream();
    }

    public function movimientosPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $tiposdoc = FacTipoDoc::all();
        $tiposRec = FacTipoRecCaja::all();

        $movimientosTotal = array();

        $totalTipo = 0;
        $subtotalTipo = 0;
        $saldoTipo = 0;
        $aboTipo = 0;

        foreach ($tiposdoc as $tipo) {
            $movimientos = FacMovimiento::ventasPorFecha($tipo->id, $fechaIni, $fechaFin,false);

            $totalTipo = 0;
            $subtotalTipo = 0;
            $saldoTipo = 0;
            $aboTipo = 0;

            foreach ($movimientos as $mov) {
                $subtotalTipo += $mov->subtotal;
                $totalTipo += $mov->total;
                $saldoTipo += $mov->saldo;
                $aboTipo += (intval($mov->total)- intval($mov->saldo));
            }

            if (count($movimientos) > 0) {
                array_push($movimientosTotal, [$tipo->nombre, $movimientos, $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
            }
        }

        foreach ($tiposdoc as $tipo) {
            $movimientos = FacMovimiento::devolucionesPorFecha($tipo->id, $fechaIni, $fechaFin, false);

            $totalTipo = 0;
            $subtotalTipo = 0;
            $saldoTipo = 0;
            $aboTipo = 0;

            foreach ($movimientos as $mov) {
                $subtotalTipo += $mov->subtotal;
                $totalTipo += $mov->total;
                $saldoTipo += $mov->saldo;
                $aboTipo += (intval($mov->total)- intval($mov->saldo));
            }

            if (count($movimientos) > 0) {
                array_push($movimientosTotal, ['Devolucion '.$tipo->nombre, $movimientos, $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
            }
        }

        foreach ($tiposRec as $tipo) {
            $recibos = FacReciboCaja::recibosPorFecha($tipo->id, $fechaIni, $fechaFin, false);

            $totalTipo = 0;
            $subtotalTipo = 0;
            $saldoTipo = 0;
            $aboTipo = 0;

            foreach ($recibos as $mov) {
                $subtotalTipo += $mov->subtotal;
                $totalTipo += $mov->total;
                $saldoTipo += $mov->saldo;
                $aboTipo += (intval($mov->total)- intval($mov->saldo));
            }

            if (count($recibos) > 0) {
                array_push($movimientosTotal, [$tipo->nombre, $recibos, $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
            }
        }

        // dd($movimientosTotal);

        $hoy = Carbon::now();

        $data = ['movimientosTotal' => $movimientosTotal,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'hoy' => $hoy ];

        $pdf = PDF::loadView('facturacion.movimientos', $data)->setPaper('a4','landscape');

        return $pdf->stream();
    }

    public function movimientosPorFechaT80($fechaIni, $fechaFin){

        $user = User::find(1);
        // $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find($user->gen_impresora_id)->ruta));
        // // $fechaIni= date('Y-m-d');
        // // $fechaFin = date('Y-m-d');

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $printer -> setFont(1);
        $printer->feed(1);
        $caracLinea = caracteres_linea_posT80FontOne();
        $empresa = GenEmpresa::find(1);
        $etiqueta  ='';// pad('', 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->razon_social), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->nombre),0,' ');
        $etiqueta .= pad("NIT: ".$empresa->nit, $caracLinea, 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->tipo_regimen), 0, ' ');
        $etiqueta .= pad(strtoupper($empresa->direccion), 0, ' ');
        $etiqueta .= pad("TEL: ".$empresa->telefono, 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $etiqueta .= pad('VENTAS NETAS POR FECHA', 0, ' ');
        $etiqueta  .= pad('', 0, ' ');
        $hoy = date("m.d.y");
        $time = date('H:i');
        $etiqueta  .= pad('Fecha Impresion:='. $hoy .' - '.$time , 1, ' ');
        $etiqueta  .= pad('', 0, ' ');

        $row = '';
        $granTotal = 0;
        $granAbono =0 ;
        $granSaldo = 0;

        $documentTypes = FacTipoDoc::get();
        $salesDetails = FacMovimiento::ventasPorFechaT80($fechaIni, $fechaFin);
        $devolutionDetails = FacMovimiento::DevolucionesPorFechaT80($fechaIni, $fechaFin);
        $receiptTypes = FacTipoRecCaja::all();
        $receiptDetails = FacReciboCaja::RecibosPorFechaT80($fechaIni, $fechaFin);

        $granTotal = $salesDetails->sum('total') + $devolutionDetails->sum('total') +  $receiptDetails->sum('total');
        $granSaldo = $salesDetails->sum('saldo') + $devolutionDetails->sum('saldo') +  $receiptDetails->sum('saldo');
        $granAbono = ($salesDetails->sum('total') - $salesDetails->sum('saldo')) + ($devolutionDetails->sum('total') - $devolutionDetails->sum('saldo')) + ($receiptDetails->sum('total') - $receiptDetails->sum('saldo'));

        foreach($documentTypes as $documentType){//Ventas

            if($salesDetails->where('tipo',$documentType->nombre)->count()>0){

                $etiqueta .= pad(eliminar_acentos(eliminar_acentos($documentType->nombre)) ,0, ' ');
                    foreach($salesDetails->where('tipo',$documentType->nombre) as $saleDetail){

                        $row = str_pad($saleDetail->consecutivo,11,' ');
                        $row .= str_pad($saleDetail->documento,12,' ');
                        $row .= str_pad(substr(eliminar_acentos($saleDetail->tercero),0,strlen($saleDetail->tercero)),41,' ');
                        $etiqueta .= pad($row, 1, ' ');
                        $row = str_pad($saleDetail->fecha,14,' ');
                        $row .=  str_pad('$ '.strval(number_format($saleDetail->total, 0, ',', '.')),12,' ');
                        $row .= str_pad('$ '.strval(number_format(($saleDetail->total - $saleDetail->saldo), 0, ',', '.')),12,' ');
                        $row .=  str_pad('$ '.strval(number_format($saleDetail->saldo, 0, ',', '.')),12,' ');
                        $etiqueta .= pad($row, 1, ' ');
                    }
                $etiqueta .= pad ('', 1, '-');
                $row = str_pad('$ '.strval(number_format($salesDetails->where('tipo',$documentType->nombre)->sum('total'), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format(($salesDetails->where('tipo',$documentType->nombre)->sum('total') - $salesDetails->where('tipo',$documentType->nombre)->sum('saldo')), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format($salesDetails->where('tipo',$documentType->nombre)->sum('saldo'), 0, ',', '.')),20,' ');
                $etiqueta .= pad ($row, 1, ' ');
                $etiqueta .= pad ('', 1, '=');
            }
        }
        foreach($documentTypes as $documentType){//Devoluciones
            if($devolutionDetails->where('tipo',$documentType->nombre)->count()){
                $etiqueta .= pad(eliminar_acentos(eliminar_acentos('Devolucion - '.$documentType->nombre)) ,0, ' ');
                    foreach($devolutionDetails->where('tipo',$documentType->nombre) as $devolutionDetail){
                        $row = str_pad($devolutionDetail->consecutivo,11,' ');
                        $row .= str_pad($devolutionDetail->documento,12,' ');
                        $row .= str_pad(substr(eliminar_acentos($devolutionDetail->tercero),0,strlen($devolutionDetail->tercero)),41,' ');
                        $etiqueta .= pad($row, 1, ' ');
                        $row = str_pad($devolutionDetail->fecha,14,' ');
                        $row .=  str_pad('$ '.strval(number_format($devolutionDetail->total, 0, ',', '.')),12,' ');
                        $row .= str_pad('$ '.strval(number_format(($devolutionDetail->total - $devolutionDetail->saldo), 0, ',', '.')),12,' ');
                        $row .=  str_pad('$ '.strval(number_format($devolutionDetail->saldo, 0, ',', '.')),12,' ');
                        $etiqueta .= pad($row, 1, ' ');
                    }
                $etiqueta .= pad ('', 1, '-');
                $row = str_pad('$ '.strval(number_format($devolutionDetails->where('tipo',$documentType->nombre)->sum('total'), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format(($devolutionDetails->where('tipo',$documentType->nombre)->sum('total') - $devolutionDetails->where('tipo',$documentType->nombre)->sum('saldo')), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format($devolutionDetails->where('tipo',$documentType->nombre)->sum('saldo'), 0, ',', '.')),20,' ');
                $etiqueta .= pad ($row, 1, ' ');
                $etiqueta .= pad ('', 1, '=');
            }
        }

        foreach($receiptTypes as $receiptType){//Recibos

            if($receiptDetails->where('tipo',$receiptType->nombre)->count()){
                $etiqueta .= pad(eliminar_acentos(eliminar_acentos($receiptType->nombre)) ,0, ' ');
                    foreach($receiptDetails->where('tipo',$receiptType->nombre) as $receiptDetail){
                        $row = str_pad($receiptDetail->consecutivo,11,' ');
                        $row .= str_pad($receiptDetail->documento,12,' ');
                        $row .= str_pad(substr(eliminar_acentos($receiptDetail->tercero),0,strlen($receiptDetail->tercero)),41,' ');
                        $etiqueta .= pad($row, 1, ' ');
                        $row = str_pad($receiptDetail->fecha,14,' ');
                        $row .=  str_pad('$ '.strval(number_format($receiptDetail->total, 0, ',', '.')),12,' ');
                        $row .= str_pad('$ '.strval(number_format(($receiptDetail->total - $receiptDetail->saldo), 0, ',', '.')),12,' ');
                        $row .=  str_pad('$ '.strval(number_format($receiptDetail->saldo, 0, ',', '.')),12,' ');
                        $etiqueta .= pad($row, 1, ' ');
                    }
                $etiqueta .= pad ('', 1, '-');
                $row = str_pad('$ '.strval(number_format($receiptDetails->where('tipo',$receiptType->nombre)->sum('total'), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format(($receiptDetails->where('tipo',$receiptType->nombre)->sum('total') - $receiptDetails->where('tipo',$receiptType->nombre)->sum('saldo')), 0, ',', '.')),20,' ');
                $row .= str_pad('$ '.strval(number_format($receiptDetails->where('tipo',$receiptType->nombre)->sum('saldo'), 0, ',', '.')),20,' ');
                $etiqueta .= pad ($row, 1, ' ');
                $etiqueta .= pad ('', 1, '=');
            }
        }

        $row = '';
        $row  .=  str_pad('$ '.strval(number_format($granTotal, 0, ',', '.')),20,' ');
        $row .= str_pad('$ '. strval(number_format(($granAbono), 0, ',', '.')),20,' ');
        $row .=  str_pad('$ '. strval(number_format($granSaldo, 0, ',', '.')),20,' ');
        $etiqueta .= pad($row, 1, ' ');
        $printer->text($etiqueta);
        $printer->feed(1);
        $printer->cut();
        $printer->close();
    }

    public function movimientosPorFechaPorTercero($fechaIni, $fechaFin, $sucursal_id)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $tiposdoc = FacTipoDoc::all();
        $tiposRec = FacTipoRecCaja::all();

        $movimientosTotal = array();

        $totalTipo = 0;
        $subtotalTipo = 0;
        $saldoTipo = 0;
        $aboTipo =0;

        foreach ($tiposdoc as $tipo) {
            $movimientos = FacMovimiento::ventasPorFecha($tipo->id, $fechaIni, $fechaFin,$sucursal_id);

            $totalTipo = 0;
            $subtotalTipo = 0;
            $saldoTipo = 0;
            $aboTipo =0;

            foreach ($movimientos as $mov) {
                $subtotalTipo += $mov->subtotal;
                $totalTipo += $mov->total;
                $saldoTipo += $mov->saldo;
                $aboTipo += (intval($mov->total)- intval($mov->saldo));
            }

            if (count($movimientos) > 0) {
                array_push($movimientosTotal, [$tipo->nombre, $movimientos, $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
            }
        }

        foreach ($tiposdoc as $tipo) {
            $movimientos = FacMovimiento::devolucionesPorFecha($tipo->id, $fechaIni, $fechaFin, $sucursal_id);

            $totalTipo = 0;
            $subtotalTipo = 0;
            $saldoTipo = 0;
            $aboTipo =0;

            foreach ($movimientos as $mov) {
                $subtotalTipo += $mov->subtotal;
                $totalTipo += $mov->total;
                $saldoTipo += $mov->saldo;
                $aboTipo += (intval($mov->total)- intval($mov->saldo));
            }

            if (count($movimientos) > 0) {
                array_push($movimientosTotal, ['Devolucion '.$tipo->nombre, $movimientos, $subtotalTipo, $totalTipo, $aboTipo, $saldoTipo]);
            }
        }

        foreach ($tiposRec as $tipo) {
            $recibos = FacReciboCaja::recibosPorFecha($tipo->id, $fechaIni, $fechaFin, $sucursal_id);

            $totalTipo = 0;
            $subtotalTipo = 0;
            $saldoTipo = 0;
            $aboTipo =0;

            foreach ($recibos as $mov) {
                $subtotalTipo += $mov->subtotal;
                $totalTipo += $mov->total;
                $saldoTipo += $mov->saldo;
                $aboTipo += (intval($mov->total)- intval($mov->saldo));
            }

            if (count($recibos) > 0) {
                array_push($movimientosTotal, [$tipo->nombre, $recibos, $subtotalTipo, $totalTipo,  $aboTipo, $saldoTipo]);
            }
        }

        // dd($movimientosTotal);

        $hoy = Carbon::now();

        $data = ['movimientosTotal' => $movimientosTotal,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'hoy' => $hoy ];

        $pdf = PDF::loadView('facturacion.movimientos', $data)->setPaper('a4','landscape');

        return $pdf->stream();
    }


    public function recaudoPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $recaudoTotales = array();

        $formaspago = FacFormaPago::all();

        $totalPOS = 0;
        $totalRecibos = 0;

        foreach ($formaspago as $forma) {
            $sumatoriaRecibos = FacPivotFormaRecibo::recaudoPorFecha($forma->id, $fechaIni, $fechaFin)->first();
            $sumatoriaPOS = FacPivotMovFormapago::recaudoPorFecha($forma->id, $fechaIni, $fechaFin)->first();

            if ($sumatoriaRecibos->valor > 0 || $sumatoriaPOS->valor > 0){
                array_push($recaudoTotales, [$forma->nombre, $sumatoriaRecibos->valor, $sumatoriaPOS->valor]);
                $totalPOS += $sumatoriaPOS->valor;
                $totalRecibos += $sumatoriaRecibos->valor;
            }
        }

        $hoy = Carbon::now();

        $data = ['recaudoTotales' => $recaudoTotales,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'totalPOS' => $totalPOS,
                 'totalRecibos' => $totalRecibos,
                 'hoy' => $hoy, ];

        $pdf = PDF::loadView('facturacion.recaudosporfecha', $data);

        return $pdf->stream();
    }

    public function movimientosConFormaPagoPorFecha($fechaIni, $fechaFin)
    {

        $date= explode('-', $fechaIni);
        $fechaIni = $date[2].'/'.$date[1].'/'.$date[0];

        $date= explode('-', $fechaFin);
        $fechaFin = $date[2].'/'.$date[1].'/'.$date[0];

        $formaspago = FacFormaPago::all();

        $datosTotal = array();

        foreach ($formaspago as $forma) {

            $total = 0;
            $movimientos = FacPivotMovFormapago::movimientosConFormaPagoPorFecha($forma->id, $fechaIni, $fechaFin);
            $recibos = FacPivotFormaRecibo::movimientosConFormaPagoPorFecha($forma->id, $fechaIni, $fechaFin);

            $concatenated = $movimientos->concat($recibos);

            foreach ($concatenated as $movimiento) {
                $total = $total + $movimiento->valor;
            }

            if ($total > 0) {
                array_push($datosTotal, [$forma->nombre, $concatenated, $total]);
            }
        }

        $hoy = Carbon::now();


        $data = ['datosTotal' => $datosTotal,
                 'fechaIni' => $fechaIni,
                 'fechaFin' => $fechaFin,
                 'hoy' => $hoy, ];

        $pdf = PDF::loadView('facturacion.movimientoconformadepagoporfecha', $data);

        return $pdf->stream();
    }

    public function readTiqueteDibal($tiquete)
    {
        $arrayTotal = array();
        $arrayItem = array();
        $tiquete = intval($tiquete);

        $ruta = GenEmpresa::find(1)->ruta_archivo_tiquetes_dibal;

        $dia = date('d');

        $mes = date('n');

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        $date = Carbon::now();
        $fechaIni = $date->format('d/m/Y');
        $fechaFin = $date->addDay()->format('d/m/Y');

        // dd($fechaIni , $fechaFin);

        $list = FacPivotMovProducto::where('num_tiquete', $tiquete)->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

        $lineasFacturadas = array();

        foreach ($list as $item) {
            array_push($lineasFacturadas, $item->num_linea_tiquete);
        }

        $val = $ruta.'/BL000'.$dia.$mes.'.TOT';

        $handle = @fopen($val, "r");

        if ($handle) {
            while (($buffer = fgets($handle)) !== false) {

                $arrayItem = array( intval(substr($buffer, 10, 6)), // cdigo producto
                                    intval(substr($buffer, 16, 6)), // cantidad
                                    intval(substr($buffer, 22, 9)),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 3, 4)), // numero tiquete
                                    intval(substr($buffer, 7, 3)), // linea tiquete
                                    intval(substr($buffer, 31, 2)));// vendedor

                if (( (intval(substr($buffer, 3, 4)) == $tiquete)) && (!in_array(intval(substr($buffer, 7, 3)), $lineasFacturadas)) ){
                    array_push($arrayTotal, $arrayItem);
                }
            }

            return $arrayTotal;

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }

    public function readTiqueteEpelsa($tiquete)
    {
        $arrayTotal = array();
        $arrayItem = array();
        $lineasFacturadas = array();
        $tiquete = intval($tiquete);

        $ruta = GenEmpresa::find(1)->ruta_archivo_tiquetes_epelsa;

        $dia = date('d');

        $mes = date('n');

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        $val = $ruta.'/tqgen'.$dia.$mes;

        $date = Carbon::now();
        $fechaIni = $date->format('d/m/Y');
        $fechaFin = $date->addDay()->format('d/m/Y');

        $list = FacPivotMovProducto::where('num_tiquete', $tiquete)->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

        foreach ($list as $item) {
            array_push($lineasFacturadas, $item->num_linea_tiquete);
        }

        $handle = @fopen($val, "r");

        if ($handle) {
            while (($buffer = fgets($handle)) !== false) {

                $arrayItem = array( intval(substr($buffer, 30, 4)), // cdigo producto
                                    intval(str_replace('.','',substr($buffer, 54, 7))), // cantidad
                                    intval(str_replace('.','',substr($buffer, 61, 8))),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 0, 4)), // numero tiquete
                                    intval(substr($buffer, 4, 3)), // linea tiquete
                                    intval(substr($buffer, 11, 4)));// vendedor

                if (( (intval(substr($buffer, 0, 4)) == $tiquete)) && (!in_array(intval(substr($buffer, 4, 3)), $lineasFacturadas)) ){

                    if ((strpos($buffer, '-') === false )){
                        array_push($arrayTotal, $arrayItem);
                    } else {
                        array_pop($arrayTotal);
                    }
                }
            }

            return $arrayTotal;

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }

    public function verificarTiqueteMarques($tiquete,$puesto, $fecha)
    {

        $date = Carbon::now()->subHours(5);
        $fechaIni = $date->format('d/m/Y H:i:s');
        $fechaFin = $date->addDay()->format('d/m/Y H:i:s');

        $list = FacPivotMovProducto::where('num_tiquete', $tiquete)->where('puesto_tiquete', $puesto)->whereBetween('created_at', [$fechaIni, $fechaFin])->get();
        return $list;
    }

    public function tiquetesNoFacturados($fecha)
    {
        $nombre_impresora = str_replace('SMB', 'smb', strtoupper(GenImpresora::find(Auth::user()->gen_impresora_id)->ruta));
        $connector = new WindowsPrintConnector($nombre_impresora);
        $printer = new Printer($connector);

        $empresa = GenEmpresa::find(1);

        $fechaIni = date('d/m/Y', strtotime($fecha));
        $fechaFin = date('d/m/Y', strtotime($fecha . ' + 1 day'));
        $arrayTotal = array();
        $arrayItem = array();
        $totalGnal = 0;
        $totalTiquete = 0;

        $dia = substr($fecha, 0,2);

        $mes = intval(substr($fecha, 3,2));

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        if ($empresa->tipo_escaner == 1) {
            $val = $empresa->ruta_archivo_tiquetes_dibal.'/BL000'.$dia.$mes.'.TOT';
        } else if ($empresa->tipo_escaner == 4) {
            $val = $empresa->ruta_archivo_tiquetes_epelsa.'/tqgen'.$dia.$mes;
        }


        $tiqueteAnterior = 0;

        $handle = @fopen($val, "r");

        if ($handle) {

            $etiqueta = str_pad("", 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->razon_social), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->nombre), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("NIT: ".$empresa->nit, 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->tipo_regimen), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad(strtoupper($empresa->direccion), 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("TEL: ".$empresa->telefono, 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad("", 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('TIQUETES NO FACTURADOS', 48, " ", STR_PAD_BOTH);
            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('Fecha: '.$fechaIni, 48, " ", STR_PAD_RIGHT);
            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);

            while (($buffer = fgets($handle)) !== false) {

                if ($empresa->tipo_escaner == 1) {
                    $arrayItem = array( intval(substr($buffer, 10, 6)), // cdigo producto
                                    intval(substr($buffer, 16, 6)), // cantidad
                                    intval(substr($buffer, 22, 9)),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 3, 4)), // numero tiquete
                                    intval(substr($buffer, 7, 3)), // linea tiquete
                                    intval(substr($buffer, 31, 2)));// vendedor
                } else if ($empresa->tipo_escaner == 4) {
                    $arrayItem = array( intval(substr($buffer, 30, 4)), // cdigo producto
                                    intval(str_replace('.','',substr($buffer, 54, 7))), // cantidad
                                    intval(str_replace('.','',substr($buffer, 61, 8))),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 0, 4)), // numero tiquete
                                    intval(substr($buffer, 4, 3)), // linea tiquete
                                    intval(substr($buffer, 11, 4)));// vendedor
                }

                $list = FacPivotMovProducto::where('num_tiquete', $arrayItem[3])
                                            ->where('num_linea_tiquete', $arrayItem[4])
                                            ->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

                if ( count($list) < 1) {

                    if (intval($tiqueteAnterior) != intval($arrayItem[3])) {
                        if ($v = GenVendedor::where('codigo_unico', intval($arrayItem[5]))->get()->first()) {
                            $vendedor = $v->nombre;
                        } else {
                            $vendedor = $arrayItem[5];
                        }
                        $etiqueta .= str_pad(' Tiq. # : '.$arrayItem[3].' - '.eliminar_acentos(str_replace('ñ', 'N',substr($vendedor,0, 30))).' ', 48, "-", STR_PAD_BOTH);
                        $tiqueteAnterior = $arrayItem[3];
                    }

                    $producto = Producto::where('codigo', $arrayItem[0])->get()->first();
                    if ($arrayItem[1] < 1) {
                        $arrayItem[1] = 1;
                    }
                    if ($arrayItem[1] < 100) {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1]));
                    } else {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1])/1000);
                    }

                    // linea 1
                    if ($producto) {
                        $etiqueta .= str_pad($producto->codigo, 3, "0", STR_PAD_LEFT);
                        $etiqueta .= ' ';
                        $nombre = strtoupper($producto->nombre);
                        $nombre = str_replace('ñ', 'N', $nombre);
                        $etiqueta .= str_pad(substr($nombre, 0, 28), 29, " ", STR_PAD_RIGHT);
                        $etiqueta .= ' ';
                        $total = intval($arrayItem[2]);
                        $totalGnal = $totalGnal + $total;
                        $total =  number_format($total, 0, ',', '.');
                        $etiqueta .= str_pad($total, 10, " ", STR_PAD_LEFT);
                        $etiqueta .= ' |';
                        $etiqueta .= str_pad('', 2, " ", STR_PAD_LEFT);
                        // linea 2
                        $etiqueta .= '    ';
                        if ($arrayItem[1] < 100) {
                            $etiqueta .= str_pad(number_format($arrayItem[1], 3, ',', '.'), 6, " ", STR_PAD_LEFT);
                        } else {
                            $etiqueta .= str_pad(number_format($arrayItem[1]/1000, 3, ',', '.'), 6, " ", STR_PAD_LEFT);
                        }
                        $etiqueta .= ' ';
                        $etiqueta .= GenUnidades::find($producto->gen_unidades_id)->abrev_pos;
                        $etiqueta .= ' ';
                        $etiqueta .= 'X';
                        $etiqueta .= ' ';
                        $etiqueta .= '$';
                        $etiqueta.= str_pad(number_format($precioUnit, 0, ',', '.'), 10, " ", STR_PAD_LEFT);
                        $etiqueta .= ' ';    //22
                        $etiqueta .= '                   ';
                    } else {
                        $etiqueta .= str_pad('El codigo '. $arrayItem[0]. 'no existe', 48, " ", STR_PAD_RIGHT);
                    }
                }

            }

            $etiqueta .= str_pad('', 48, " ", STR_PAD_LEFT);
            $etiqueta .= str_pad('', 48, "-", STR_PAD_LEFT);
            $etiqueta .= 'Total No Facturado:';
            $etiqueta .= str_pad(number_format($totalGnal, 0, ',', '.'), 29, " ", STR_PAD_LEFT);

            $printer->text($etiqueta);
            $printer->feed(2);
            $printer->cut();
            $printer->pulse();
            $printer->close();

            return 'done';

            if (!feof($handle)) {
                echo "Error: unexpected fgets() fail\n";
            }
            fclose($handle);
        }
    }

    public function tiquetesNoFacturadosPDF($fecha)
    {
        $empresa = GenEmpresa::find(1);

        if ($empresa->tipo_escaner == 2) {
            
            $arrayLines = $this->marquesPDF($fecha);

        } else  {

            $arrayLines = $this->epelsaDibalPDF($fecha);
        }

        $data = ['etiqueta' => $arrayLines];

        $pdf = PDF::loadView('facturacion.tiquetesnofacturados', $data);

        return $pdf->stream();

    }

    public function tiquetesNoFacturadosMarquesPDF(Request $request, $fecha)
    {
        $etiqueta = TempMarques::orderBy('bascula')->get();

        $data = ['etiqueta' => $etiqueta];

        $pdf = PDF::loadView('facturacion.nofacturadosmarques', $data);

        return $pdf->stream();
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

    public function tiquetesDiaBascula($bascula, $fecha){

        $date= explode('-', $fecha);
        $fecha = $date[2].'/'.$date[1].'/'.$date[0];

        $tiquetes = FacPivotMovProducto::tiquetesDiaBascula($bascula, $fecha);

        if (count($tiquetes)) {
            return $tiquetes;
        } else {
            return 'Error no hay tiquetes el dia '. $fecha . ' con la bascula '. $bascula;
        }
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


    public function marquesPDF($fecha)
    {

        $fechaArray = explode('-', $fecha);

        $fecha = $fechaArray[2].'-'.$fechaArray[1].'-'.$fechaArray[0];

        $arrayTotal = array();
        $arrayItem = array();
        $arrayLines = array();
        $totalGnal = 0;
        $totalTiquete = 0;

        $marquesData = GenEmpresa::find(1)->ruta_ip_marques;

        $basculas = explode('&', $marquesData);

        foreach ($basculas as $bascula) {
            $ip = explode('-', $bascula)[0];
            $puesto = explode('-', $bascula)[1];

            $tiquetesFacturados = $this->tiquetesDiaBascula($puesto, $fecha);

            $primerTiquete = $tiquetesFacturados->first()->num_tiquete;

            $primerTiquete = intval($primerTiquete) - 20;

            for ($i=0; $i < 5; $i++) {

                $primer = $primerTiquete + ($i * 100);

                $url = 'http://' .$ip. '/year/documentos?seek={"tipo_doc":1,"posto":'.$puesto. ',"numero":' .$primer. '}&limit=100';

                $tiquetesBascula = http_get($url);

                foreach ($tiquetesBascula as $tiqueteBasc) {
                    
                    if (($tiqueteBasc['d_doc'] == $fecha) && ($tiqueteBasc['posto'] == $puesto)) {

                        $tiqFacts = array_column($tiquetesFacturados->toArray(), 'num_tiquete');
                        
                        // $tiq = array_search(strval($tiqueteBasc['numero']), $tiqFacts);

                        $tiq = in_array($tiqueteBasc['numero'], $tiqFacts, false);

                        if ($tiq == false) {

                            if ($v = GenVendedor::where('codigo_unico', intval($tiqueteBasc['num_vendedor']))->get()->first()) {
                                $vendedor = $v->nombre;
                            } else {
                                $vendedor = $tiqueteBasc['num_vendedor'];
                            }

                            $url = 'http://'.$ip.'/year/documentos_lnh?seek={"tipo_doc":1,"posto":'.$puesto.',"numero":'.$tiqueteBasc['numero'].',"linha_f":0}&limit='.$tiqueteBasc['nr_parcelas'];

                            $lineasTiquete = http_get($url);

                            foreach ($lineasTiquete as $key => $linea) {

                                if ($tiqueteBasc['numero'] == $linea['numero']){

                                    $producto = Producto::where('codigo', intval($linea['codigo']))->get()->first();

                                    $total = intval($linea['valor']);

                                    // linea 1
                                    if ($producto) {

                                        $total = intval($linea['valor']);
                                        $totalGnal = $totalGnal + $total;

                                        array_push($arrayLines, array(
                                            'tiquete' => $tiqueteBasc['numero'],
                                            'vendedor' => $vendedor,
                                            'linea_tiquete'=> $key + 1,
                                            'codigo' => $producto->codigo,
                                            'producto' => strtoupper($producto->nombre),
                                            'total' => $total,
                                            'cantidad' => $linea['quantidade'],
                                            'unidades' => GenUnidades::find($producto->gen_unidades_id)->abrev_pos,
                                            'precio' => $linea['preco_unit']
                                        ));

                                    } else {
                                        array_push($arrayLines, array(
                                            'tiquete' => $tiqueteBasc['numero'],
                                            'vendedor' => $vendedor,
                                            'linea_tiquete'=> $key + 1,
                                            'codigo' =>  $linea['codigo'],
                                            'producto' => 'PRODUCTO NO EXISTENTE',
                                            'total' => $total,
                                            'cantidad' => $linea['quantidade'],
                                            'unidades' => '',
                                            'precio' => $linea['preco_unit']
                                        ));
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return $arrayLines;
    }

    public function epelsaDibalPDF($fecha)
    {
        
        $empresa = GenEmpresa::find(1);

        $fechaIni = date('d/m/Y', strtotime($fecha));
        $fechaFin = date('d/m/Y', strtotime($fecha . ' + 1 day'));
        $arrayTotal = array();
        $arrayItem = array();
        $arrayLines = array();
        $totalGnal = 0;
        $totalTiquete = 0;

        $dia = substr($fecha, 0,2);

        $mes = intval(substr($fecha, 3,2));

        if ($mes == 10 ) {
            $mes = 'A';
        } else if ($mes == 11) {
            $mes = 'B';
        } else if ($mes == 12) {
            $mes = 'C';
        }

        if ($empresa->tipo_escaner == 1) {
            $val = $empresa->ruta_archivo_tiquetes_dibal.'/BL000'.$dia.$mes.'.TOT';
        } else if ($empresa->tipo_escaner == 4) {
            $val = $empresa->ruta_archivo_tiquetes_epelsa.'/tqgen'.$dia.$mes;
        }

        $tiqueteAnterior = 0;

        $handle = @fopen($val, "r");

        if ($handle) {

            $etiqueta = str_pad("", 48, "-", STR_PAD_BOTH);

            while (($buffer = fgets($handle)) !== false) {

                if ($empresa->tipo_escaner == 1) {
                    $arrayItem = array( intval(substr($buffer, 10, 6)), // cdigo producto
                                    intval(substr($buffer, 16, 6)), // cantidad
                                    intval(substr($buffer, 22, 9)),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 3, 4)), // numero tiquete
                                    intval(substr($buffer, 7, 3)), // linea tiquete
                                    intval(substr($buffer, 31, 2)));// vendedor
                } else if ($empresa->tipo_escaner == 4) {
                    $arrayItem = array( intval(substr($buffer, 30, 4)), // cdigo producto
                                    intval(str_replace('.','',substr($buffer, 54, 7))), // cantidad
                                    intval(str_replace('.','',substr($buffer, 61, 8))),// precio total producto (precio*cantidad)
                                    intval(substr($buffer, 0, 4)), // numero tiquete
                                    intval(substr($buffer, 4, 3)), // linea tiquete
                                    intval(substr($buffer, 11, 4)));// vendedor
                }

                $list = FacPivotMovProducto::where('num_tiquete', $arrayItem[3])
                                            ->where('num_linea_tiquete', $arrayItem[4])
                                            ->whereBetween('created_at', [$fechaIni, $fechaFin])->get();

                if ( count($list) < 1) {

                    if ($v = GenVendedor::where('codigo_unico', intval($arrayItem[5]))->get()->first()) {
                        $vendedor = $v->nombre;
                    } else {
                        $vendedor = $arrayItem[5];
                    }

                    $producto = Producto::where('codigo', $arrayItem[0])->get()->first();

                    if ($arrayItem[1] < 1) {
                        $arrayItem[1] = 1;
                    }

                    if ($arrayItem[1] < 100) {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1]));
                    } else {
                        $precioUnit = intval($arrayItem[2])/(intval($arrayItem[1])/1000);
                        $arrayItem[1] = $arrayItem[1]/1000;
                    }

                    // linea 1
                    if ($producto) {

                        $total = intval($arrayItem[2]);
                        $totalGnal = $totalGnal + $total;

                        array_push($arrayLines, array(
                            'tiquete' => $arrayItem[3],
                            'vendedor' => $vendedor,
                            'linea_tiquete'=> $arrayItem[4],
                            'codigo' => $producto->codigo,
                            'producto' => strtoupper($producto->nombre),
                            'total' => $total,
                            'cantidad' => $arrayItem[1],
                            'unidades' => GenUnidades::find($producto->gen_unidades_id)->abrev_pos,
                            'precio' => $precioUnit
                        ));

                    } else {
                        array_push($arrayLines, array(
                            'tiquete' => $arrayItem[3],
                            'vendedor' => $vendedor,
                            'linea_tiquete'=> $arrayItem[4],
                            'codigo' =>  $arrayItem[0],
                            'producto' => 'PRODUCTO NO EXISTENTE',
                            'total' => $total,
                            'cantidad' => $arrayItem[1],
                            'unidades' => '',
                            'precio' => ''
                        ));
                    }

                    if ((strpos($buffer, '-') !== false && $empresa->tipo_escaner == 4)){
                        array_pop($arrayLines);
                        array_pop($arrayLines);
                    }
                }
            }

            return $arrayLines;

        } else {

            return 'El archivo para la fecha especificada no existe';
        }

    }
}
