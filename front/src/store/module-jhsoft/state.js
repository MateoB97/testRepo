export default {
  // url: 'http://localhost/sgc/back/public/',
  // url: 'http://localhost/sgcdev/back/public/',
  url: 'http://192.168.1.100/sgc/back/public/',
  // url: 'http://sgc.estebangonzalez.xyz/back/',
  // url: 'http://192.168.1.1/sgcdev/back/public/',
  // url: 'http://192.168.1.184/sgc/back/public/',
  // url: 'http://192.168.1.82/sgc/back/public/',
  // url: 'http://desktop-caermcs/sgc/back/public/',
  // url: 'http://fusion.test/',
  tipo_licencia: 3,
  facturacion: true,
  lotes: false,
  despachos: false,
  recibos: true,
  compras: false,
  inventario: false,
  ordenes: false,
  // 1 pos lite (solo pos), 2 pos + cartera, 3 pos + cartera + inventario + facturacion electronica, 4 3 + produccion, 5 solo produccion
  token: localStorage.sgc_jwt_token
}
