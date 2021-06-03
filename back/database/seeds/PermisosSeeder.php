<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;



class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::statement('delete from user_permisos');
        DB::statement('delete from user_permisos_categorias');


        $cat = array();

        $cat['facturacion'] = [
            [ 'consecutivo' => 1, 'nombre' => 'crear factura'  ],
            [ 'consecutivo' => 2, 'nombre' => 'crear ingreso efectivo'  ],
            [ 'consecutivo' => 3, 'nombre' => 'crear egreso de efectivo'  ]
        ];

        $cat['despachos'] = [
            [ 'consecutivo' => 4, 'nombre' => 'salida de mercancia'  ],
            [ 'consecutivo' => 5, 'nombre' => 'ver despachos'  ],
            [ 'consecutivo' => 6, 'nombre' => 'crear despacho por lote completo'  ],
            [ 'consecutivo' => 7, 'nombre' => 'peso despacho'  ]
        ];

        $cat['recibos_de_caja'] = [
            [ 'consecutivo' => 8, 'nombre' => 'crear recibo de caja'  ]
        ];

        $cat['despachos'] = [
            [ 'consecutivo' => 9, 'nombre' => 'salida de mercancia'  ],
            [ 'consecutivo' => 10, 'nombre' => 'ver despachos'  ],
            [ 'consecutivo' => 11, 'nombre' => 'crear despacho por lote completo'  ],
            [ 'consecutivo' => 12, 'nombre' => 'peso despacho'  ]
        ];

        $cat['compras'] = [
            [ 'consecutivo' => 13, 'nombre' => 'crear compra de mercancia'  ],
            [ 'consecutivo' => 14, 'nombre' => 'crear comprobante de egreso'  ]
        ];

        $cat['ordenes'] = [
            [ 'consecutivo' => 15, 'nombre' => 'ordenes de compra'  ]
        ];

        $cat['productos'] = [
            [ 'consecutivo' => 16, 'nombre' => 'crear producto'  ],
            [ 'consecutivo' => 17, 'nombre' => 'crear grupos'  ],
            [ 'consecutivo' => 18, 'nombre' => 'crear subgrupos'  ],
            [ 'consecutivo' => 19, 'nombre' => 'crear unidad'  ],
            [ 'consecutivo' => 20, 'nombre' => 'lista de precios'  ],
            [ 'consecutivo' => 21, 'nombre' => 'cambio rapido de precios'  ],
        ];

        $cat['vendedores'] = [
            [ 'consecutivo' => 22, 'nombre' => 'crear vendedores'  ]
        ];

        $cat['terceros'] = [
            [ 'consecutivo' => 23, 'nombre' => 'crear terceros'  ]
        ];

        $cat['impuestos'] = [
            [ 'consecutivo' => 24, 'nombre' => 'crear impuesto'  ],
            [ 'consecutivo' => 25, 'nombre' => 'crear iva'  ],
            [ 'consecutivo' => 26, 'nombre' => 'crear puc'  ]
        ];

        $cat['documentos'] = [
            [ 'consecutivo' => 27, 'nombre' => 'crear tipos de impuesto'  ],
            [ 'consecutivo' => 28, 'nombre' => 'crear tipos de documento'  ],
            [ 'consecutivo' => 29, 'nombre' => 'crear tipos de recibo de caja'  ],
            [ 'consecutivo' => 30, 'nombre' => 'crear tipos de compra'  ],
            [ 'consecutivo' => 31, 'nombre' => 'crear tipos de comprobante de egreso'  ],
            [ 'consecutivo' => 32, 'nombre' => 'crear tipos de ordenes'  ],
            [ 'consecutivo' => 33, 'nombre' => 'crear tipos de egresos'  ],
            [ 'consecutivo' => 34, 'nombre' => 'crear formas de pago'  ]
        ];

        $cat['general'] = [
            [ 'consecutivo' => 35, 'nombre' => 'impresora'  ],
            [ 'consecutivo' => 36, 'nombre' => 'bascula'  ],
            [ 'consecutivo' => 37, 'nombre' => 'usuarios'  ],
            [ 'consecutivo' => 38, 'nombre' => 'cambiar contraseña'  ],
            [ 'consecutivo' => 39, 'nombre' => 'empresa'  ],
            [ 'consecutivo' => 40, 'nombre' => 'generalidades'  ]
        ];

        $cat['usuarios'] = [
            [ 'consecutivo' => 41, 'nombre' => 'categoria permisos'  ],
            [ 'consecutivo' => 42, 'nombre' => 'permisos'  ],
            [ 'consecutivo' => 43, 'nombre' => 'roles'  ],
            [ 'consecutivo' => 44, 'nombre' => 'asociar permiso a rol'  ]
        ];

        $cat['reportes'] = [
            [ 'consecutivo' => 45, 'nombre' => 'inventario'  ],
            [ 'consecutivo' => 46, 'nombre' => 'inventario produccion'  ],
            [ 'consecutivo' => 47, 'nombre' => 'movimientos'  ],
            [ 'consecutivo' => 48, 'nombre' => 'recibos'  ],
            [ 'consecutivo' => 49, 'nombre' => 'compras'  ],
            [ 'consecutivo' => 50, 'nombre' => 'comprobantes de egreso'  ],
            [ 'consecutivo' => 51, 'nombre' => 'egresos'  ],
            [ 'consecutivo' => 52, 'nombre' => 'ordenes'  ],
            [ 'consecutivo' => 53, 'nombre' => 'cuadre z'  ],
            [ 'consecutivo' => 54, 'nombre' => 'tiquetes no facturados'  ],
            [ 'consecutivo' => 55, 'nombre' => 'reportes generados'  ],
            [ 'consecutivo' => 56, 'nombre' => 'informe peso planta'  ],
            [ 'consecutivo' => 57, 'nombre' => 'informe producto por lote'  ]
        ];

        $cat['lotes'] = [
            [ 'consecutivo' => 58, 'nombre' => 'crear lote'  ],
            [ 'consecutivo' => 59, 'nombre' => 'peso planta'  ],
            [ 'consecutivo' => 60, 'nombre' => 'empaque'  ],
            [ 'consecutivo' => 61, 'nombre' => 'empaque tercero'  ],
            [ 'consecutivo' => 62, 'nombre' => 'etiqueta interna'  ],
            [ 'consecutivo' => 63, 'nombre' => 'peso programacion'  ],
            [ 'consecutivo' => 64, 'nombre' => 'peso marinacion'  ],
        ];

        foreach($cat as $categoria => $permisos){

            $categoria = App\UserPermisosCategorias::create([
                'nombre' => $categoria,
            ]);

            foreach($permisos as $permiso) {
                $permiso = App\UserPermisos::create([
                    'nombre' => $permiso['nombre'],
                    'consecutivo' => $permiso['consecutivo'],
                    'permisos_categoria_id' => $categoria->id
                ]);
            }
        }
    }
}
