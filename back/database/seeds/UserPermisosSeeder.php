<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class UserPermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $allPermisos = '';

        DB::statement('delete from user_permisos');
        DB::statement('delete from user_permisos_categorias');
        DB::statement('DBCC CHECKIDENT (user_permisos, RESEED, 0)');
        DB::statement('DBCC CHECKIDENT (user_permisos_categorias, RESEED, 0)');

        $cat = array();

        $cat['facturacion'] = [
            [ 'consecutivo' => 1, 'nombre' => 'crear factura'  ],
            [ 'consecutivo' => 2, 'nombre' => 'crear ingreso efectivo'  ],
            [ 'consecutivo' => 3, 'nombre' => 'crear egreso de efectivo'  ]
        ];

        $cat['recibos_de_caja'] = [
            [ 'consecutivo' => 4, 'nombre' => 'crear recibo de caja'  ]
        ];

        $cat['despachos'] = [
            [ 'consecutivo' => 5, 'nombre' => 'salida de mercancia'  ],
            [ 'consecutivo' => 6, 'nombre' => 'ver despachos'  ],
            [ 'consecutivo' => 7, 'nombre' => 'crear despacho por lote completo'  ],
            [ 'consecutivo' => 8, 'nombre' => 'peso despacho'  ]
        ];

        $cat['compras'] = [
            [ 'consecutivo' => 9, 'nombre' => 'crear compra de mercancia'  ],
            [ 'consecutivo' => 10, 'nombre' => 'crear comprobante de egreso'  ]
        ];

        $cat['ordenes'] = [
            [ 'consecutivo' => 11, 'nombre' => 'ordenes de compra'  ]
        ];

        $cat['productos'] = [
            [ 'consecutivo' => 12, 'nombre' => 'crear producto'  ],
            [ 'consecutivo' => 13, 'nombre' => 'crear grupos'  ],
            [ 'consecutivo' => 14, 'nombre' => 'crear subgrupos'  ],
            [ 'consecutivo' => 15, 'nombre' => 'crear unidad'  ],
            [ 'consecutivo' => 16, 'nombre' => 'lista de precios'  ],
            [ 'consecutivo' => 17, 'nombre' => 'cambio rapido de precios'  ],
        ];

        $cat['vendedores'] = [
            [ 'consecutivo' => 18, 'nombre' => 'crear vendedores'  ]
        ];

        $cat['terceros'] = [
            [ 'consecutivo' => 19, 'nombre' => 'crear terceros'  ]
        ];

        $cat['impuestos'] = [
            [ 'consecutivo' => 20, 'nombre' => 'crear impuesto'  ],
            [ 'consecutivo' => 21, 'nombre' => 'crear iva'  ],
            [ 'consecutivo' => 22, 'nombre' => 'crear puc'  ]
        ];

        $cat['documentos'] = [
            [ 'consecutivo' => 23, 'nombre' => 'crear tipos de impuesto'  ],
            [ 'consecutivo' => 24, 'nombre' => 'crear tipos de documento'  ],
            [ 'consecutivo' => 25, 'nombre' => 'crear tipos de recibo de caja'  ],
            [ 'consecutivo' => 26, 'nombre' => 'crear tipos de compra'  ],
            [ 'consecutivo' => 27, 'nombre' => 'crear tipos de comprobante de egreso'  ],
            [ 'consecutivo' => 28, 'nombre' => 'crear tipos de ordenes'  ],
            [ 'consecutivo' => 29, 'nombre' => 'crear tipos de egresos'  ],
            [ 'consecutivo' => 30, 'nombre' => 'crear formas de pago'  ]
        ];

        $cat['general'] = [
            [ 'consecutivo' => 31, 'nombre' => 'impresora'  ],
            [ 'consecutivo' => 32, 'nombre' => 'bascula'  ],
            [ 'consecutivo' => 33, 'nombre' => 'usuarios'  ],
            [ 'consecutivo' => 34, 'nombre' => 'cambiar contraseña'  ],
            [ 'consecutivo' => 35, 'nombre' => 'empresa'  ],
            [ 'consecutivo' => 36, 'nombre' => 'generalidades'  ]
        ];

        $cat['usuarios'] = [
            [ 'consecutivo' => 37, 'nombre' => 'categoria permisos'  ],
            [ 'consecutivo' => 38, 'nombre' => 'permisos'  ],
            [ 'consecutivo' => 39, 'nombre' => 'roles'  ],
            [ 'consecutivo' => 40, 'nombre' => 'asociar permiso a rol'  ]
        ];

        $cat['reportes'] = [
            [ 'consecutivo' => 41, 'nombre' => 'inventario'  ],
            [ 'consecutivo' => 42, 'nombre' => 'inventario produccion'  ],
            [ 'consecutivo' => 43, 'nombre' => 'movimientos'  ],
            [ 'consecutivo' => 44, 'nombre' => 'recibos'  ],
            [ 'consecutivo' => 45, 'nombre' => 'compras'  ],
            [ 'consecutivo' => 46, 'nombre' => 'comprobantes de egreso'  ],
            [ 'consecutivo' => 47, 'nombre' => 'egresos'  ],
            [ 'consecutivo' => 48, 'nombre' => 'ordenes'  ],
            [ 'consecutivo' => 49, 'nombre' => 'cuadre z'  ],
            [ 'consecutivo' => 50, 'nombre' => 'tiquetes no facturados'  ],
            [ 'consecutivo' => 51, 'nombre' => 'reportes generados'  ],
            [ 'consecutivo' => 52, 'nombre' => 'informe peso planta'  ],
            [ 'consecutivo' => 53, 'nombre' => 'informe producto por lote'  ]
        ];

        $cat['lotes'] = [
            [ 'consecutivo' => 54, 'nombre' => 'crear lote'  ],
            [ 'consecutivo' => 55, 'nombre' => 'peso planta'  ],
            [ 'consecutivo' => 56, 'nombre' => 'empaque'  ],
            [ 'consecutivo' => 57, 'nombre' => 'empaque tercero'  ],
            [ 'consecutivo' => 58, 'nombre' => 'etiqueta interna'  ],
            [ 'consecutivo' => 59, 'nombre' => 'peso programacion'  ],
            [ 'consecutivo' => 60, 'nombre' => 'peso marinacion'  ],
        ];

        $cat['menu_lotes'] = [
            [ 'consecutivo' => 61, 'nombre' => 'facturacion'  ],
            [ 'consecutivo' => 62, 'nombre' => 'despachos'  ],
            [ 'consecutivo' => 63, 'nombre' => 'recibos de caja'  ],
            [ 'consecutivo' => 64, 'nombre' => 'compras'  ],
            [ 'consecutivo' => 65, 'nombre' => 'ordenes'  ],
            [ 'consecutivo' => 66, 'nombre' => 'general'  ],
            [ 'consecutivo' => 67, 'nombre' => 'configuracion'  ],
            [ 'consecutivo' => 68, 'nombre' => 'reportes'  ],
            [ 'consecutivo' => 69, 'nombre' => 'lotes'  ]
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

                $allPermisos .= $permiso['consecutivo'].',';
            }
        }

        $rol = App\UserRoles::find(1);

        if (!$rol) {
            App\UserRoles::create([
                'nombre' => 'Administrador',
                'permisos' => $allPermisos
            ]);
        } else {
            $rol->permisos = $allPermisos;
            $rol->save();
        }

        $user = App\User::find(1);
        $user->user_rol_id = $rol->id;
        $user->save();

    }
}

