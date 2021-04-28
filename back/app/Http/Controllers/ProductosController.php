<?php

namespace App\Http\Controllers;

use App\Producto;
use App\ProdVencimiento;
use App\ProdListaPrecio;
use App\ProdGrupo;
use App\ProdSubgrupo;
use App\ProdPivotListaProducto;
use App\GenVendedor;
use App\GenEmpresa;
use App\Tercero;
use App\TerceroSucursal;
use Illuminate\Http\Request;
use PDF;

class ProductosController extends Controller
{
    public function exportData()
    {
        $grupos = ProdGrupo::all();

        $subgrupos = ProdSubgrupo::all();

        $productos = Producto::all();

        foreach ($productos as $producto) {
            $producto->vencimientos = ProdVencimiento::where('producto_id',$producto->id)->get();
            $producto->precios = ProdPivotListaProducto::where('producto_id',$producto->id)->get();
        }

        $terceros = Tercero::all();

        foreach ($terceros as $tercero) {
            $tercero->terceroSucursal;
        };

        $data = [
            'grupos' => $grupos,
            'subgrupos' => $subgrupos,
            'productos' => $productos,
            'terceros' => $terceros
        ];

        return $data;
    }

    public function importaData(Request $request)
    {
        foreach ($request->grupos as $grupo) {
            $nuevoGrupo = new ProdGrupo($grupo);
            $nuevoGrupo->save();
        }

        foreach ($request->subgrupos as $subgrupo) {
            $nuevoSub = new ProdSubgrupo($subgrupo);
            $nuevoSub->save();
        }

        foreach ($request->productos as $producto) {
            $nuevoItem = new Producto($producto);
            $nuevoItem->gen_unidades_id = $producto['genUnidades_id'];
            $nuevoItem->prod_subgrupo_id = $producto['prodSubgrupo_id'];
            $nuevoItem->gen_iva_id = 1;

            $nuevoItem->save();

            foreach ($producto['vencimientos'] as $vencimiento) {
                $nuevoVen = new ProdVencimiento($vencimiento);
                $nuevoVen->producto_id = $nuevoItem->id;
                $nuevoVen->save();
            }

            foreach ($producto['precios'] as $precio) {
                $nuevoPrecio = new ProdPivotListaProducto($precio);
                $nuevoPrecio->producto_id = $nuevoItem->id;
                $nuevoPrecio->save();
            }
        }

        foreach ($request->terceros as $tercero) {
            $nuevoTer = new Tercero($tercero);
            $nuevoTer->digito_verificacion = null;
            if ($tercero['habilitado_traslados'] == null) {
                $nuevoTer->habilitado_traslados = 0;
            }
            $nuevoTer->save();

            foreach ($tercero['tercero_sucursal'] as $sucursal) {
                $nuevoSuc = new TerceroSucursal($sucursal);
                if ($sucursal['gen_municipios_id'] == null) {
                    $nuevoSuc->gen_municipios_id = 79;
                }
                $nuevoSuc->email = null;
                $nuevoSuc->tercero_id = $nuevoTer->id;
                $nuevoSuc->save();
            }
        }
    }

    public function index()
    {
        //Pendiente Cambiar
        $index= Producto::todosConGrupos();
        // $index= Producto::productosCustom();
        return $index;
    }

    public function store(Request $request)
    {
        // $v = \Validator::make($request->all(), [
        //     'nombre' => 'unique:productos',
        // ]);

        // if ($v->fails())
        // {
        //     return $v->errors()[0];
        // }

        if (count($request->precios) > 0 ){

            $nuevoItem = new Producto($request->all());
            $nuevoItem->save();

            foreach ($request->precios as $precio) {
                $prec = new ProdPivotListaProducto($precio);
                $prec->producto_id = $nuevoItem->id;
                $prec->save();
            }

            foreach ($request->vencimientos as $vencimiento) {
                $venc = new ProdVencimiento($vencimiento);
                $venc->producto_id = $nuevoItem->id;
                $venc->save();
            }

            return 'done';
        } else {
            return 'Error: Agregue al menos un precio';
        }
    }

    public function show($id)
    {
        $model = Producto::find($id);

        $model->prod_subgrupo_id = $model->prodSubgrupo;
        $model->prodGrupo_id = $model->prodSubgrupo->prodGrupo;
        $model->gen_unidades_id =$model->GenUnidades;
        $model->gen_iva_id =$model->GenIva;

        $arrayPrecios = [];
        $arrayVencimientos = [];

        foreach ($model->ProdPivotListaProducto as $precios) {
            $precios->prodListaPrecio_nombre = $precios->prodListaPrecio->nombre;
            array_push($arrayPrecios, $precios);
        }

        foreach ($model->prodVencimiento as $vencimiento) {
            $vencimiento->prodAlmacenamiento_nombre = $vencimiento->prodAlmacenamiento->nombre;
            array_push($arrayVencimientos, $vencimiento);
        }

        $model->precios = $arrayPrecios;
        $model->vencimientos = $arrayVencimientos;

        return $model;

    }

    public function estado($id, $cambio)
    {
         $model = Producto::find($id);

         $modificacion = ($cambio == 'activar') ? $model->activo = 1 : $model->activo =0;
         $validate = $model->save();
         $return = $validate ? 'true' : 'false';

         return $return;
    }

    public function update(Request $request, $id)
    {
        $model = Producto::find($request->id);
        $model->fill($request->all());
        $model->save();

        foreach ($request->precios as $precio) {
            $validate = strrpos($precio['id'], "nuevo");
            if ( $validate === false) {
                $suc = ProdPivotListaProducto::find($precio['id']);
                $suc->fill($precio);
                $suc->save();
            }else{
                $suc = new ProdPivotListaProducto($precio);
                $suc->producto_id = $model->id;
                $suc->save();
            }
        }

        foreach ($request->vencimientos as $vencimiento) {
            $validate = strrpos($vencimiento['id'], "nuevo");
            if ( $validate === false) {
                $suc = ProdVencimiento::find($vencimiento['id']);
                $suc->fill($vencimiento);
                $suc->save();
            }else{
                $suc = new ProdVencimiento($vencimiento);
                $suc->producto_id = $model->id;
                $suc->save();
            }
        }

        return 'done';
    }

    public function destroy($id)
    {
        $model = Producto::find($id);
        $delete = $model->delete();

        if ($delete) {
            return 'deleted';
        }
    }

    public function todosConImpuestos(){
        $list = Producto::todosConImpuestos();
        return $list;
    }

    public function ProductosxListaprecio($listaprecio_id){
        $lineas = Producto::ProductosxListaprecio($listaprecio_id);
        $listaprecios = ProdListaPrecio::find($listaprecio_id);

        $data = ['lineas' => $lineas,'listaprecios' => $listaprecios];

        $pdf = PDF::loadView('productos.productosxlistaprecio', $data);

        return $pdf->stream();
    }

    public function listadoDeProductosPorLista($listaprecio_id){

        $lineas = Producto::ProductosxListaprecio($listaprecio_id);

        return $lineas;
    }

    public function ListadoProductos(){
        $lineas = Producto::ListadoProductos();

        $data = ['lineas' => $lineas];

        $pdf = PDF::loadView('productos.listadoproductos', $data);

        return $pdf->stream();
    }

    public function modificarPrecio($lista_id, $codigo, $precio_nuevo){

        $producto = Producto::where('codigo', $codigo)->get()->first();

        $prodPrecio = ProdPivotListaProducto::where('producto_id', $producto->id)->where('prodListaPrecio_id', $lista_id)->get()->first();

        $prodPrecio->precio = $precio_nuevo;

        $v = $prodPrecio->save();

        if ($v) {
            return 'done';
        } else {
            return 'Error';
        }
    }


    public function subGroupFilter($id){
        $list = Producto::where('prod_subgrupo_id', $id)->get();
        return $list;
    }

    public function generarArchivoProductosBasculaDival(){

        $empresa = GenEmpresa::find(1);

        $lineas = Producto::ProductosxListaprecio(1);

        $fp = fopen($empresa->ruta_archivo_tx_dival.'/TX.txt','w+');

        foreach ($lineas as $linea) {

            $linea1 = '00L200M0';
            $linea1 .= str_pad($linea->codigo, 5, "0", STR_PAD_LEFT);
            $linea1 .= '   ';
            $nombre = strtoupper($linea->nombre);
            $nombre = str_replace('ñ', 'N', $nombre);
            $linea1 .= str_pad($nombre, 73, " ", STR_PAD_RIGHT);
            $linea1 .= str_pad($linea->precio, 7, "0", STR_PAD_LEFT);
            $linea1 .= str_pad('', 17, "0", STR_PAD_LEFT);

            fwrite($fp, $linea1.PHP_EOL);

            $linea2 = '00H3000';
            $linea2 .= str_pad($linea->codigo, 5, "0", STR_PAD_LEFT);
            if ($linea->unidad_id == 1) {
                $linea2 .= '0';
            } else {
                $linea2 .= '1';
            }
            $linea2 .= '000000000000000000000000000000000000000000000000000             00000000000000000000000000000000000000000000000000000';
            fwrite($fp, $linea2.PHP_EOL);

            // solo para clientes con 2 secciones de basculas
            // $linea1 = '02L200M0';
            // $linea1 .= str_pad($linea->codigo, 5, "0", STR_PAD_LEFT);
            // $linea1 .= '   ';
            // $nombre = strtoupper($linea->nombre);
            // $nombre = str_replace('ñ', 'N', $nombre);
            // $linea1 .= str_pad($nombre, 73, " ", STR_PAD_RIGHT);
            // $linea1 .= str_pad($linea->precio, 7, "0", STR_PAD_LEFT);
            // $linea1 .= str_pad('', 17, "0", STR_PAD_LEFT);

            // fwrite($fp, $linea1.PHP_EOL);

            // $linea2 = '02H3000';
            // $linea2 .= str_pad($linea->codigo, 5, "0", STR_PAD_LEFT);
            // if ($linea->unidad_id == 1) {
            //     $linea2 .= '0';
            // } else {
            //     $linea2 .= '1';
            // }
            // $linea2 .= '000000000000000000000000000000000000000000000000000             00000000000000000000000000000000000000000000000000000';
            // fwrite($fp, $linea2.PHP_EOL);
        }

        $vendedores = GenVendedor::all();

        $cantGrupos = ceil(count($vendedores)/3);

        $groups = $vendedores->split($cantGrupos);

        $groups->toArray();


        foreach ($groups as $group) {
            if (count($group) == 3) {

                $linea = '00X500000';
                foreach ($group as $vendedor) {
                    $linea .= str_pad($vendedor['codigo_unico'], 2, "0", STR_PAD_LEFT);
                    $nombre = strtoupper($vendedor['nombre']);
                    $nombre = str_replace('ñ', 'N', $nombre);
                    $linea .= str_pad($nombre, 26, " ", STR_PAD_RIGHT);
                    $linea .= str_pad($vendedor['codigo_unico'], 2, "0", STR_PAD_LEFT);
                    $linea .= '000';
                }
                $linea .= str_pad('', 22, "0", STR_PAD_RIGHT);
                fwrite($fp, $linea.PHP_EOL);

            } elseif (count($group) == 2) {

                $linea = '00X500000';
                foreach ($group as $vendedor) {
                    $linea .= str_pad($vendedor['codigo_unico'], 2, "0", STR_PAD_LEFT);
                    $nombre = strtoupper($vendedor['nombre']);
                    $nombre = str_replace('ñ', 'N', $nombre);
                    $linea .= str_pad($nombre, 26, " ", STR_PAD_RIGHT);
                    $linea .= str_pad($vendedor['codigo_unico'], 2, "0", STR_PAD_LEFT);
                    $linea .= '000';
                }
                $linea .= str_pad('', 55, "0", STR_PAD_RIGHT);
                fwrite($fp, $linea.PHP_EOL);

            } elseif (count($group) == 1) {
                $linea = '00X500000';
                foreach ($group as $vendedor) {
                    $linea .= str_pad($vendedor['codigo_unico'], 2, "0", STR_PAD_LEFT);
                    $nombre = strtoupper($vendedor['nombre']);
                    $nombre = str_replace('ñ', 'N', $nombre);
                    $linea .= str_pad($nombre, 26, " ", STR_PAD_RIGHT);
                    $linea .= str_pad($vendedor['codigo_unico'], 2, "0", STR_PAD_LEFT);
                    $linea .= '000';
                }
                $linea .= str_pad('', 88, "0", STR_PAD_RIGHT);
                fwrite($fp, $linea.PHP_EOL);
            }
        }

        fclose($fp);

        return 'done';
    }

    public function generarArchivoProductosBasculaEpelsa(){

        $empresa = GenEmpresa::find(1);

        $lineas = Producto::ProductosxListaprecio(1);

        $fp = fopen($empresa->ruta_archivo_precios_epelsa.'/global.dat','w+');

        // fwrite($fp, '0001000000master               0011'.PHP_EOL);
        // fwrite($fp, '40010101192.168.001.170'.PHP_EOL);

        foreach ($lineas as $linea) {

            $linea1 = '5';
            $linea1 .= str_pad($linea->codigo, 7, "0", STR_PAD_LEFT);
            $linea1 .= '01';
            $linea1 .= str_pad($linea->codigo, 4, "0", STR_PAD_LEFT);
            $linea1 .= str_pad($linea->precio, 12, "0", STR_PAD_LEFT);

            if (intval($linea->unidad_id) == 1) {
                $linea1 .= 'W';
            } else if (intval($linea->unidad_id) == 2) {
                $linea1 .= 'U';
            }

            $linea1 .= '0000';

            $nombre = strtoupper($linea->nombre);
            $nombre = substr($nombre, 0,24);
            $nombre = str_replace('ñ', 'N', $nombre);
            $linea1 .= str_pad($nombre, 25, " ", STR_PAD_RIGHT);

            $linea1 .= '0000000000000000025500F           00';



            fwrite($fp, $linea1.PHP_EOL);
        }

        fclose($fp);

        return 'done';
    }

    public function subirDatosBasculaMarques(){

        $productos = Producto::ProductosxListaprecioMarques(1);

        foreach ($productos as $producto) {
            $producto->codigo = str_pad($producto->codigo, 3, "0", STR_PAD_LEFT);
            if ($producto->unidades == 'Kgs') {
                $producto->unidades = 'kg';
            } else {
                $producto->unidades = 'un';
            }
        }

        $familias = ProdGrupo::listadoGruposMarques();
        $vendedores = GenVendedor::listadoVendedoresMarques();

        $cantidadProductos = count($productos);
        $division = ceil($cantidadProductos/ 100);

        foreach ($vendedores as $vendedor) {
            $vendedor->blacklist = "AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA=";
            $vendedor->tipo_doc = 1;
        }

        $arrayProductos = [];

        $i = 0;
        while ($i < $division) {

            $chunck = $productos->splice($i * 100 , ($i*100) + 99);

            array_push($arrayProductos, $chunck);

            $productos = Producto::ProductosxListaprecioMarques(1);
            foreach ($productos as $producto) {
                $producto->codigo = str_pad($producto->codigo, 3, "0", STR_PAD_LEFT);
                if ($producto->unidades == 'Kgs') {
                    $producto->unidades = 'kg';
                } else {
                    $producto->unidades = 'un';
                }
            }

            $i = $i + 1;
        }


        $data = [$familias,$arrayProductos,$vendedores];

        return $data;
    }
}
