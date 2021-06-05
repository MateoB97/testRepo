
const routes = [
  {
    path: '/',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', name: 'home', component: () => import('pages/Index.vue') },
      { path: 'register', name: 'register', component: () => import('pages/Register.vue') },
      { path: 'changepass', name: 'changepass', component: () => import('pages/user/cambiarPass.vue'), meta: { auth: true } }
    ]
  },
  {
    path: '/generales',
    component: () => import('layouts/MyLayout.vue'),
    meta: {
      auth: true
    },
    children: [
      { path: 'tipoimpuestos', meta: { permisoRequerido: '20' }, component: () => import('pages/generales/GenTipoImpuesto.vue') },
      { path: 'impuestos', meta: { permisoRequerido: '20' }, component: () => import('pages/generales/GenImpuesto.vue') },
      { path: 'iva', meta: { permisoRequerido: '21' }, component: () => import('pages/generales/GenIva.vue') },
      { path: 'puc', meta: { permisoRequerido: '22' }, component: () => import('pages/generales/GenPuc.vue') },
      { path: 'unidades', meta: { permisoRequerido: '15' }, component: () => import('pages/generales/GenUnidades.vue') },
      { path: 'generalidades', meta: { permisoRequerido: '36' }, component: () => import('pages/generales/Generalidades.vue') },
      { path: 'cuadrecaja', meta: { permisoRequerido: '1' }, component: () => import('pages/generales/GenCuadreCaja.vue') },
      { path: 'vendedores', meta: { permisoRequerido: '18' }, component: () => import('pages/generales/GenVendedores.vue') },
      { path: 'impresoras', meta: { permisoRequerido: '31' }, component: () => import('pages/generales/GenImpresoras.vue') },
      { path: 'basculas', meta: { permisoRequerido: '32' }, component: () => import('pages/generales/GenBasculas.vue') },
      { path: 'empresa', meta: { permisoRequerido: '35' }, component: () => import('pages/generales/GenEmpresa.vue') },
      { path: 'tiquetesnofacturados', meta: { permisoRequerido: '50' }, component: () => import('pages/generales/tiquetesnofacturados.vue') }
    ]
  },
  {
    path: '/reportes',
    component: () => import('layouts/MyLayout.vue'),
    meta: {
      auth: true
    },
    children: [
      { path: 'reportes-pdf', meta: { permisoRequerido: '68' }, component: () => import('pages/reportes/ReportesPDF.vue') },
      { path: 'inventario-produccion', meta: { permisoRequerido: '42' }, component: () => import('pages/reportes/InventarioProduccion.vue') },
      { path: 'inventario', meta: { permisoRequerido: '41' }, component: () => import('pages/reportes/Inventario.vue') },
      { path: 'peso-planta', meta: { permisoRequerido: '52' }, component: () => import('pages/reportes/pesoplanta.vue') },
      { path: 'productos-lote', meta: { permisoRequerido: '53' }, component: () => import('pages/reportes/productosporlote.vue') }
    ]
  },
  {
    path: '/productos',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'grupos', meta: { permisoRequerido: '13' }, component: () => import('pages/productos/ProdGrupo.vue') },
      { path: 'subgrupos', meta: { permisoRequerido: '14' }, component: () => import('pages/productos/ProdSubgrupo.vue') },
      { path: '', meta: { permisoRequerido: '12' }, component: () => import('pages/productos/Productos.vue') },
      { path: 'listadodeprecios', meta: { permisoRequerido: '16' }, component: () => import('pages/productos/ProdListaPrecio.vue') },
      { path: 'cambioprecios', meta: { permisoRequerido: '17' }, component: () => import('pages/productos/CambioPrecios.vue') }
    ]
  },
  {
    path: '/terceros',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', meta: { permisoRequerido: '19' }, component: () => import('pages/terceros/Tercero.vue') }
    ]
  },
  {
    path: '/facturacion',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'tipo-documentos', meta: { permisoRequerido: '24' }, component: () => import('pages/facturacion/facTipoDoc.vue') },
      { path: 'tipo-recibos-caja', meta: { permisoRequerido: '17' }, component: () => import('pages/facturacion/facTipoRecCaja.vue') },
      { name: 'movimientos', meta: { permisoRequerido: '1' }, path: 'movimientos/:id/:consecmov', props: true, component: () => import('pages/facturacion/facMovimiento.vue') },
      { path: 'movimientos', meta: { permisoRequerido: '43' }, props: true, component: () => import('pages/facturacion/facMovResumen.vue') },
      { path: 'recibos', meta: { permisoRequerido: '4' }, props: true, component: () => import('pages/facturacion/facRecResumen.vue') },
      { name: 'recibos', meta: { permisoRequerido: '4' }, path: 'recibos/:id', props: true, component: () => import('pages/facturacion/facRecibosCaja.vue') },
      { path: 'formaspago', meta: { permisoRequerido: '30' }, props: true, component: () => import('pages/facturacion/facFormaPago.vue') }
    ],
    props: true
  },
  {
    path: '/compras',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'tipo-compras', meta: { permisoRequerido: '26' }, component: () => import('pages/compras/ComTipoCompra.vue') },
      { path: 'tipo-compro-egresos', meta: { permisoRequerido: '29' }, component: () => import('pages/compras/ComTipoComproEgreso.vue') },
      { name: 'compras', path: 'compras/:id', meta: { permisoRequerido: '9' }, props: true, component: () => import('pages/compras/ComCompra.vue') },
      { path: 'items', meta: { permisoRequerido: '64' }, props: true, component: () => import('pages/compras/ComComprasResumen.vue') },
      { path: 'compro-egresos', meta: { permisoRequerido: '27' }, props: true, component: () => import('pages/compras/ComComproEgresoResumen.vue') },
      { name: 'compro-egresos', path: 'compro-egresos/:id', meta: { permisoRequerido: '27' }, props: true, component: () => import('pages/compras/ComComproEgreso.vue') }
    ],
    props: true
  },
  {
    path: '/ordenes',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'tipo-ordenes', meta: { permisoRequerido: '28' }, component: () => import('pages/ordenes/OrdTipoOrden.vue') },
      { name: 'ordenes', path: 'ordenes/:id', meta: { permisoRequerido: '11' }, props: true, component: () => import('pages/ordenes/OrdOrdenes.vue') },
      { path: 'items', meta: { permisoRequerido: '48' }, props: true, component: () => import('pages/ordenes/OrdOrdenesResumen.vue') },
      { path: 'resumen', meta: { permisoRequerido: '48' }, component: () => import('pages/ordenes/OrdOrdenesResumen.vue') }
    ],
    props: true
  },
  {
    path: '/gestion-efectivo',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'tipos', meta: { permisoRequerido: '29' }, component: () => import('pages/gestionEfectivo/tipos.vue') },
      { path: 'egresos', meta: { permisoRequerido: '3' }, component: () => import('pages/gestionEfectivo/egresos.vue') },
      { path: 'resumen', meta: { permisoRequerido: '47' }, component: () => import('pages/gestionEfectivo/resumen.vue') },
      { path: 'ingresos', meta: { permisoRequerido: '2' }, component: () => import('pages/gestionEfectivo/ingresos.vue') }
    ],
    props: true
  },
  {
    path: '/lotes',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', meta: { permisoRequerido: '54' }, component: () => import('pages/lotes/Lote.vue') },
      { path: 'empaque', meta: { permisoRequerido: '56' }, component: () => import('pages/lotes/LotEmpaque.vue') },
      { path: 'empaque-terminado', meta: { permisoRequerido: '57' }, component: () => import('pages/lotes/LotEmpaqueTerminado.vue') },
      { path: 'peso-planta', meta: { permisoRequerido: '55' }, component: () => import('pages/lotes/PesoPlanta.vue') },
      { path: 'etiqueta-interna', meta: { permisoRequerido: '58' }, component: () => import('pages/lotes/LotEtiquetaInterna.vue') },
      { path: 'peso-programacion', meta: { permisoRequerido: '59' }, component: () => import('pages/lotes/PesoProgramacion.vue') },
      { path: 'peso-marinacion', meta: { permisoRequerido: '60' }, component: () => import('pages/lotes/PesoMarinacion.vue') }
    ]
  },
  {
    path: '/usuarios',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'categorias-permisos', meta: { permisoRequerido: '37' }, component: () => import('pages/user/CategoriasPermisos.vue') },
      { path: 'permisos', meta: { permisoRequerido: '38' }, component: () => import('pages/user/PermisosUsuarios.vue') },
      { path: 'roles', meta: { permisoRequerido: '39' }, component: () => import('pages/user/RolesUsuarios.vue') },
      { path: 'asociar-permisos-rol', meta: { permisoRequerido: '40' }, component: () => import('pages/user/AsociarPermisosRol.vue') }
    ]
  },
  {
    path: '/despachos',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', meta: { permisoRequerido: '5' }, component: () => import('pages/despacho/SalMercancia.vue') },
      { path: 'listado', meta: { permisoRequerido: '6' }, component: () => import('pages/despacho/ListadoSalMercancia.vue') },
      { path: 'crearporlote', meta: { permisoRequerido: '7' }, component: () => import('pages/despacho/DespachoPorLote.vue') },
      { path: 'pesodespacho', meta: { permisoRequerido: '8' }, component: () => import('pages/despacho/PesoPorDespacho.vue') }
    ]
  },
  {
    path: '/login',
    name: 'login',
    component: () => import('pages/Login.vue'),
    meta: {
      auth: false
    }
  }
]

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue')
  })
}

export default routes
