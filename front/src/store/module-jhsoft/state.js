export default {
  // url: 'http://localhost/sgc/back/public/',
  url: 'http://localhost/sgcDonCerdo/back/public/',
  // url: 'http://192.168.1.168/sevillaErrLogs/back/public/',
  // url: 'http://sgc.estebangonzalez.xyz/back/',
  // url: 'http://192.168.1.169/sgcdev/back/public/',
  // url: 'http://192.168.0.100/sgc/back/public/',
  // url: 'http://192.168.1.82/sgc/back/public/',
  // url: 'http://desktop-caermcs/sgc/back/public/',
  // url: 'http://fusion.test/',
  tipo_licencia: 4,
  facturacion: true,
  lotes: true,
  despachos: true,
  recibos: true,
  compras: true,
  inventario: true,
  ordenes: true,
  // 1 pos lite (solo pos), 2 pos + cartera, 3 pos + cartera + inventario + facturacion electronica, 4 3 + produccion, 5 solo produccion
  token: localStorage.sgc_jwt_token
}
