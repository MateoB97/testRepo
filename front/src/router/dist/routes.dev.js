"use strict";

Object.defineProperty(exports, "__esModule", {
  value: true
});
exports["default"] = void 0;

function _typeof(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _getRequireWildcardCache() { if (typeof WeakMap !== "function") return null; var cache = new WeakMap(); _getRequireWildcardCache = function _getRequireWildcardCache() { return cache; }; return cache; }

function _interopRequireWildcard(obj) { if (obj && obj.__esModule) { return obj; } if (obj === null || _typeof(obj) !== "object" && typeof obj !== "function") { return { "default": obj }; } var cache = _getRequireWildcardCache(); if (cache && cache.has(obj)) { return cache.get(obj); } var newObj = {}; var hasPropertyDescriptor = Object.defineProperty && Object.getOwnPropertyDescriptor; for (var key in obj) { if (Object.prototype.hasOwnProperty.call(obj, key)) { var desc = hasPropertyDescriptor ? Object.getOwnPropertyDescriptor(obj, key) : null; if (desc && (desc.get || desc.set)) { Object.defineProperty(newObj, key, desc); } else { newObj[key] = obj[key]; } } } newObj["default"] = obj; if (cache) { cache.set(obj, newObj); } return newObj; }

var routes = [{
  path: '/',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: '',
    name: 'home',
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/Index.vue'));
      });
    }
  }, {
    path: 'register',
    name: 'register',
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/Register.vue'));
      });
    }
  }, {
    path: 'changepass',
    name: 'changepass',
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/user/cambiarPass.vue'));
      });
    },
    meta: {
      auth: true
    }
  }]
}, {
  path: '/generales',
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  meta: {
    auth: true
  },
  children: [{
    path: 'tipoimpuestos',
    meta: {
      permisoRequerido: '20'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenTipoImpuesto.vue'));
      });
    }
  }, {
    path: 'impuestos',
    meta: {
      permisoRequerido: '20'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenImpuesto.vue'));
      });
    }
  }, {
    path: 'iva',
    meta: {
      permisoRequerido: '21'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenIva.vue'));
      });
    }
  }, {
    path: 'puc',
    meta: {
      permisoRequerido: '22'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenPuc.vue'));
      });
    }
  }, {
    path: 'unidades',
    meta: {
      permisoRequerido: '15'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenUnidades.vue'));
      });
    }
  }, {
    path: 'generalidades',
    meta: {
      permisoRequerido: '36'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/Generalidades.vue'));
      });
    }
  }, {
    path: 'cuadrecaja',
    meta: {
      permisoRequerido: '1'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenCuadreCaja.vue'));
      });
    }
  }, {
    path: 'vendedores',
    meta: {
      permisoRequerido: '18'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenVendedores.vue'));
      });
    }
  }, {
    path: 'impresoras',
    meta: {
      permisoRequerido: '31'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenImpresoras.vue'));
      });
    }
  }, {
    path: 'basculas',
    meta: {
      permisoRequerido: '32'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenBasculas.vue'));
      });
    }
  }, {
    path: 'empresa',
    meta: {
      permisoRequerido: '35'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/GenEmpresa.vue'));
      });
    }
  }, {
    path: 'tiquetesnofacturados',
    meta: {
      permisoRequerido: '50'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/generales/tiquetesnofacturados.vue'));
      });
    }
  }]
}, {
  path: '/reportes',
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  meta: {
    auth: true
  },
  children: [{
    path: 'reportes-pdf',
    meta: {
      permisoRequerido: '68'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/reportes/ReportesPDF.vue'));
      });
    }
  }, {
    path: 'inventario-produccion',
    meta: {
      permisoRequerido: '42'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/reportes/InventarioProduccion.vue'));
      });
    }
  }, {
    path: 'inventario',
    meta: {
      permisoRequerido: '41'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/reportes/Inventario.vue'));
      });
    }
  }, {
    path: 'peso-planta',
    meta: {
      permisoRequerido: '52'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/reportes/pesoplanta.vue'));
      });
    }
  }, {
    path: 'productos-lote',
    meta: {
      permisoRequerido: '53'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/reportes/productosporlote.vue'));
      });
    }
  }]
}, {
  path: '/productos',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: 'grupos',
    meta: {
      permisoRequerido: '13'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/productos/ProdGrupo.vue'));
      });
    }
  }, {
    path: 'subgrupos',
    meta: {
      permisoRequerido: '14'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/productos/ProdSubgrupo.vue'));
      });
    }
  }, {
    path: '',
    meta: {
      permisoRequerido: '12'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/productos/Productos.vue'));
      });
    }
  }, {
    path: 'listadodeprecios',
    meta: {
      permisoRequerido: '16'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/productos/ProdListaPrecio.vue'));
      });
    }
  }, {
    path: 'cambioprecios',
    meta: {
      permisoRequerido: '17'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/productos/CambioPrecios.vue'));
      });
    }
  }]
}, {
  path: '/terceros',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: '',
    meta: {
      permisoRequerido: '19'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/terceros/Tercero.vue'));
      });
    }
  }]
}, {
  path: '/facturacion',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: 'tipo-documentos',
    meta: {
      permisoRequerido: '24'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facTipoDoc.vue'));
      });
    }
  }, {
    path: 'tipo-recibos-caja',
    meta: {
      permisoRequerido: '17'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facTipoRecCaja.vue'));
      });
    }
  }, {
    name: 'movimientos',
    meta: {
      permisoRequerido: '1'
    },
    path: 'movimientos/:id/:consecmov',
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facMovimiento.vue'));
      });
    }
  }, {
    path: 'movimientos',
    meta: {
      permisoRequerido: '43'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facMovResumen.vue'));
      });
    }
  }, {
    path: 'recibos',
    meta: {
      permisoRequerido: '4'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facRecResumen.vue'));
      });
    }
  }, {
    name: 'recibos',
    meta: {
      permisoRequerido: '4'
    },
    path: 'recibos/:id',
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facRecibosCaja.vue'));
      });
    }
  }, {
    path: 'formaspago',
    meta: {
      permisoRequerido: '30'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/facturacion/facFormaPago.vue'));
      });
    }
  }],
  props: true
}, {
  path: '/compras',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: 'tipo-compras',
    meta: {
      permisoRequerido: '26'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/compras/ComTipoCompra.vue'));
      });
    }
  }, {
    path: 'tipo-compro-egresos',
    meta: {
      permisoRequerido: '29'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/compras/ComTipoComproEgreso.vue'));
      });
    }
  }, {
    name: 'compras',
    path: 'compras/:id',
    meta: {
      permisoRequerido: '9'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/compras/ComCompra.vue'));
      });
    }
  }, {
    path: 'items',
    meta: {
      permisoRequerido: '64'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/compras/ComComprasResumen.vue'));
      });
    }
  }, {
    path: 'compro-egresos',
    meta: {
      permisoRequerido: '27'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/compras/ComComproEgresoResumen.vue'));
      });
    }
  }, {
    name: 'compro-egresos',
    path: 'compro-egresos/:id',
    meta: {
      permisoRequerido: '27'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/compras/ComComproEgreso.vue'));
      });
    }
  }],
  props: true
}, {
  path: '/ordenes',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: 'tipo-ordenes',
    meta: {
      permisoRequerido: '28'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/ordenes/OrdTipoOrden.vue'));
      });
    }
  }, {
    name: 'ordenes',
    path: 'ordenes/:id',
    meta: {
      permisoRequerido: '11'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/ordenes/OrdOrdenes.vue'));
      });
    }
  }, {
    path: 'items',
    meta: {
      permisoRequerido: '48'
    },
    props: true,
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/ordenes/OrdOrdenesResumen.vue'));
      });
    }
  }, {
    path: 'resumen',
    meta: {
      permisoRequerido: '48'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/ordenes/OrdOrdenesResumen.vue'));
      });
    }
  }],
  props: true
}, {
  path: '/gestion-efectivo',
  meta: {
    auth: true
  },
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: 'tipos',
    meta: {
      permisoRequerido: '29'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/gestionEfectivo/tipos.vue'));
      });
    }
  }, {
    path: 'egresos',
    meta: {
      permisoRequerido: '3'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/gestionEfectivo/egresos.vue'));
      });
    }
  }, {
    path: 'resumen',
    meta: {
      permisoRequerido: '47'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/gestionEfectivo/resumen.vue'));
      });
    }
  }, {
    path: 'ingresos',
    meta: {
      permisoRequerido: '2'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/gestionEfectivo/ingresos.vue'));
      });
    }
  }],
  props: true
}, {
  path: '/lotes',
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: '',
    meta: {
      permisoRequerido: '54'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/Lote.vue'));
      });
    }
  }, {
    path: 'empaque',
    meta: {
      permisoRequerido: '56'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/LotEmpaque.vue'));
      });
    }
  }, {
    path: 'empaque-terminado',
    meta: {
      permisoRequerido: '57'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/LotEmpaqueTerminado.vue'));
      });
    }
  }, {
    path: 'peso-planta',
    meta: {
      permisoRequerido: '55'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/PesoPlanta.vue'));
      });
    }
  }, {
    path: 'etiqueta-interna',
    meta: {
      permisoRequerido: '58'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/LotEtiquetaInterna.vue'));
      });
    }
  }, {
    path: 'peso-programacion',
    meta: {
      permisoRequerido: '59'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/PesoProgramacion.vue'));
      });
    }
  }, {
    path: 'peso-marinacion',
    meta: {
      permisoRequerido: '60'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/lotes/PesoMarinacion.vue'));
      });
    }
  }]
}, {
  path: '/usuarios',
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: 'categorias-permisos',
    meta: {
      permisoRequerido: '37'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/user/CategoriasPermisos.vue'));
      });
    }
  }, {
    path: 'permisos',
    meta: {
      permisoRequerido: '38'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/user/PermisosUsuarios.vue'));
      });
    }
  }, {
    path: 'roles',
    meta: {
      permisoRequerido: '39'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/user/RolesUsuarios.vue'));
      });
    }
  }, {
    path: 'asociar-permisos-rol',
    meta: {
      permisoRequerido: '40'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/user/AsociarPermisosRol.vue'));
      });
    }
  }]
}, {
  path: '/despachos',
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('layouts/MyLayout.vue'));
    });
  },
  children: [{
    path: '',
    meta: {
      permisoRequerido: '5'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/despacho/SalMercancia.vue'));
      });
    }
  }, {
    path: 'listado',
    meta: {
      permisoRequerido: '6'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/despacho/ListadoSalMercancia.vue'));
      });
    }
  }, {
    path: 'crearporlote',
    meta: {
      permisoRequerido: '7'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/despacho/DespachoPorLote.vue'));
      });
    }
  }, {
    path: 'pesodespacho',
    meta: {
      permisoRequerido: '8'
    },
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/despacho/PesoPorDespacho.vue'));
      });
    }
  }]
}, {
  path: '/login',
  name: 'login',
  component: function component() {
    return Promise.resolve().then(function () {
      return _interopRequireWildcard(require('pages/Login.vue'));
    });
  },
  meta: {
    auth: false
  }
}]; // Always leave this as last one

if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: function component() {
      return Promise.resolve().then(function () {
        return _interopRequireWildcard(require('pages/Error404.vue'));
      });
    }
  });
}

var _default = routes;
exports["default"] = _default;