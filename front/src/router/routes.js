
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
      { path: 'tipoimpuestos', component: () => import('pages/generales/GenTipoImpuesto.vue') },
      { path: 'impuestos', component: () => import('pages/generales/GenImpuesto.vue') },
      { path: 'iva', component: () => import('pages/generales/GenIva.vue') },
      { path: 'unidades', component: () => import('pages/generales/GenUnidades.vue') },
      { path: 'generalidades', component: () => import('pages/generales/Generalidades.vue') },
      { path: 'cuadrecaja', component: () => import('pages/generales/GenCuadreCaja.vue') },
      { path: 'vendedores', component: () => import('pages/generales/GenVendedores.vue') },
      { path: 'impresoras', component: () => import('pages/generales/GenImpresoras.vue') },
      { path: 'basculas', component: () => import('pages/generales/GenBasculas.vue') },
      { path: 'empresa', component: () => import('pages/generales/GenEmpresa.vue') },
      { path: 'tiquetesnofacturados', component: () => import('pages/generales/tiquetesnofacturados.vue') }
    ]
  },
  {
    path: '/reportes',
    component: () => import('layouts/MyLayout.vue'),
    meta: {
      auth: true
    },
    children: [
      { path: 'reportes-pdf', component: () => import('pages/reportes/ReportesPDF.vue') },
      { path: 'inventario-produccion', component: () => import('pages/reportes/InventarioProduccion.vue') },
      { path: 'inventario', component: () => import('pages/reportes/Inventario.vue') },
      { path: 'peso-planta', component: () => import('pages/reportes/pesoplanta.vue') },
      { path: 'productos-lote', component: () => import('pages/reportes/productosporlote.vue') }
    ]
  },
  {
    path: '/productos',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'grupos', component: () => import('pages/productos/ProdGrupo.vue') },
      { path: 'subgrupos', component: () => import('pages/productos/ProdSubgrupo.vue') },
      { path: '', component: () => import('pages/productos/Productos.vue') },
      { path: 'listadodeprecios', component: () => import('pages/productos/ProdListaPrecio.vue') },
      { path: 'cambioprecios', component: () => import('pages/productos/CambioPrecios.vue') }
    ]
  },
  {
    path: '/terceros',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', component: () => import('pages/terceros/Tercero.vue') }
    ]
  },
  {
    path: '/facturacion',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'tipo-documentos', component: () => import('pages/facturacion/facTipoDoc.vue') },
      { path: 'tipo-recibos-caja', component: () => import('pages/facturacion/facTipoRecCaja.vue') },
      { name: 'movimientos', path: 'movimientos/:id/:consecmov', props: true, component: () => import('pages/facturacion/facMovimiento.vue') },
      { path: 'movimientos', props: true, component: () => import('pages/facturacion/facMovResumen.vue') },
      { path: 'recibos', props: true, component: () => import('pages/facturacion/facRecResumen.vue') },
      { name: 'recibos', path: 'recibos/:id', props: true, component: () => import('pages/facturacion/facRecibosCaja.vue') },
      { path: 'formaspago', props: true, component: () => import('pages/facturacion/facFormaPago.vue') }
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
      { path: 'tipo-compras', component: () => import('pages/compras/ComTipoCompra.vue') },
      { path: 'tipo-compro-egresos', component: () => import('pages/compras/ComTipoComproEgreso.vue') },
      { name: 'compras', path: 'compras/:id', props: true, component: () => import('pages/compras/ComCompra.vue') },
      { path: 'items', props: true, component: () => import('pages/compras/ComComprasResumen.vue') },
      { path: 'compro-egresos', props: true, component: () => import('pages/compras/ComComproEgresoResumen.vue') },
      { name: 'compro-egresos', path: 'compro-egresos/:id', props: true, component: () => import('pages/compras/ComComproEgreso.vue') }
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
      { path: 'tipo-ordenes', component: () => import('pages/ordenes/OrdTipoOrden.vue') },
      { name: 'ordenes', path: 'ordenes/:id', props: true, component: () => import('pages/ordenes/OrdOrdenes.vue') },
      { path: 'items', props: true, component: () => import('pages/ordenes/OrdOrdenesResumen.vue') },
      { path: 'resumen', component: () => import('pages/ordenes/OrdOrdenesResumen.vue') }
    ],
    props: true
  },
  {
    path: '/egresos',
    meta: {
      auth: true
    },
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: 'tipos', component: () => import('pages/egresos/tipos.vue') },
      { path: 'items', component: () => import('pages/egresos/egresos.vue') },
      { path: 'resumen', component: () => import('pages/egresos/resumen.vue') }
    ],
    props: true
  },
  {
    path: '/lotes',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', component: () => import('pages/lotes/Lote.vue') },
      { path: 'empaque', component: () => import('pages/lotes/LotEmpaque.vue') },
      { path: 'empaque-terminado', component: () => import('pages/lotes/LotEmpaqueTerminado.vue') },
      { path: 'peso-planta', component: () => import('pages/lotes/PesoPlanta.vue') },
      { path: 'etiqueta-interna', component: () => import('pages/lotes/LotEtiquetaInterna.vue') },
      { path: 'peso-programacion', component: () => import('pages/lotes/PesoProgramacion.vue') },
      { path: 'peso-marinacion', component: () => import('pages/lotes/PesoMarinacion.vue') }
    ]
  },
  {
    path: '/despachos',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', component: () => import('pages/despacho/SalMercancia.vue') },
      { path: 'listado', component: () => import('pages/despacho/ListadoSalMercancia.vue') },
      { path: 'crearporlote', component: () => import('pages/despacho/DespachoPorLote.vue') },
      { path: 'pesodespacho', component: () => import('pages/despacho/PesoPorDespacho.vue') }
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
