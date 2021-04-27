const axios = require('axios')

export const helperFacturacionScanerLineas = {
  methods: {
    buscarLineasTiqueteDibal () {
      this.$q.loading.show()
      var app = this
      var tiqueteLeido = false
      app.num_tiquete = parseInt(app.num_tiquete.substr(0, 12))
      var tiquetesLeidos = []
      if (app.dataResumen.length !== 0) {
        tiquetesLeidos = app.dataResumen.filter(v => parseInt(v.num_tiquete) === parseInt(app.num_tiquete))
        if (tiquetesLeidos.length > 0) {
          tiqueteLeido = true
        }
      }
      if (!tiqueteLeido) {
        axios.get(app.$store.state.jhsoft.url + 'api/basculas/readtiquetedibal/' + app.num_tiquete).then(
          function (response) {
            if (response.data.length > 0) {
              var vendedor = null
              response.data.forEach(function (element, j) {
                const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element[0]))
                if (productoImpuesto !== undefined) {
                  if (parseInt(element[1]) > 50) {
                    element[1] = element[1] / 1000
                  }
                  var newProduct = {
                    id: app.itemsCounter,
                    producto: productoImpuesto.nombre,
                    producto_id: productoImpuesto.id,
                    producto_codigo: productoImpuesto.codigo,
                    cantidad: element[1],
                    precio: parseInt(element[2] / element[1]) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
                    iva: productoImpuesto.impuesto,
                    gen_iva_id: productoImpuesto.gen_iva_id,
                    desc: 0.00,
                    descporcentaje: 0.00,
                    despacho: false,
                    num_tiquete: element[3],
                    num_linea_tiquete: element[4]
                  }
                  app.dataResumen.push(newProduct)
                  vendedor = element[5]
                  app.itemsCounter = app.itemsCounter + 1
                  app.numLineas = app.numLineas + 1
                } else {
                  app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element[0]) + ' no esta creado.' })
                }
              })
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
              if (app.storeItems.gen_vendedor_id === undefined) {
                app.$q.notify({ color: 'negative', message: 'Error vendedor con codigo ' + vendedor + ' no existe, se cargara el vendedor por defecto.' })
                app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
              }
            } else {
              app.$q.notify({ color: 'negative', message: 'Error al leer el tiquete  o todos los elementos ya fueron facturados.' })
            }
            app.num_tiquete = null
            app.$q.loading.hide()
          }
        )
      } else {
        app.$q.notify({ color: 'negative', message: 'El tiquete ya esta cargado.' })
        app.num_tiquete = null
        app.$q.loading.hide()
      }
    },
    buscarLineasDespacho () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/readdespacho/' + app.num_tiquete).then(
        function (response) {
          if (response.data[0].length > 0) {
            app.sucursal = response.data[1]
            var vendedor = null
            response.data[0].forEach(function (element, j) {
              const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element.codigo))
              if (productoImpuesto !== undefined) {
                var newProduct = {
                  id: app.itemsCounter,
                  producto: productoImpuesto.nombre,
                  producto_id: productoImpuesto.id,
                  producto_codigo: productoImpuesto.codigo,
                  cantidad: element.peso,
                  precio: parseInt(element.precio) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
                  iva: productoImpuesto.impuesto,
                  gen_iva_id: productoImpuesto.gen_iva_id,
                  desc: 0.00,
                  descporcentaje: 0.00,
                  despacho: false,
                  num_tiquete: app.num_tiquete,
                  num_linea_tiquete: 0
                }
                app.dataResumen.push(newProduct)
                vendedor = 1
                app.itemsCounter = app.itemsCounter + 1
                app.numLineas = app.numLineas + 1
              } else {
                app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element.codigo) + ' no esta creado.' })
              }
            })
            app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
            if (app.storeItems.gen_vendedor_id === undefined) {
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
            }
            app.storeItems.guiaTransporte = app.num_tiquete
          } else { // Aqui o Antes?
            app.$q.notify({ color: 'negative', message: 'Error al leer el despac.' })
          }
          app.num_tiquete = null
          app.$q.loading.hide()
        }
      )
    },
    buscarLineasOrden () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/ordenes/readordenfactura/' + app.orden + '/' + app.tipoDoc.id).then(
        function (response) {
          if (response.data.length > 0) {
            var vendedor = null
            response.data.forEach(function (element, j) {
              const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.id) === parseInt(element.producto_id))
              if (productoImpuesto !== undefined) {
                var newProduct = {
                  id: app.itemsCounter,
                  producto: productoImpuesto.nombre,
                  producto_id: productoImpuesto.id,
                  producto_codigo: productoImpuesto.codigo,
                  cantidad: element.cantidad,
                  precio: element.precio,
                  iva: element.iva,
                  gen_iva_id: productoImpuesto.gen_iva_id,
                  desc: 0.00,
                  descporcentaje: 0.00,
                  despacho: false,
                  num_tiquete: app.num_tiquete,
                  num_linea_tiquete: 0
                }
                app.dataResumen.push(newProduct)
                vendedor = 1
                app.itemsCounter = app.itemsCounter + 1
                app.numLineas = app.numLineas + 1
              } else {
                app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element.codigo) + ' no esta creado.' })
              }
            })
            app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
            if (app.storeItems.gen_vendedor_id === undefined) {
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
            }
          } else {
            app.$q.notify({ color: 'negative', message: 'Error al leer la orden.' })
          }
          app.num_tiquete = null
          app.$q.loading.hide()
        }
      )
      app.orden = null
    },
    buscarLineasCodigoBarras () {
      this.$q.loading.show()
      var app = this
      const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.ean13) === parseInt(app.num_tiquete))
      if (productoImpuesto !== undefined) {
        var valExist = app.dataResumen.find(v => parseInt(v.producto_id) === parseInt(productoImpuesto.id))
        if (valExist !== undefined) {
          valExist.cantidad = valExist.cantidad + 1
        } else {
          const objectPrecio = this.listadoPrecios.find(v => parseInt(v.producto_id) === parseInt(productoImpuesto.id))
          var newProduct = {
            id: app.itemsCounter,
            producto: productoImpuesto.nombre,
            producto_id: productoImpuesto.id,
            producto_codigo: productoImpuesto.codigo,
            cantidad: 1,
            precio: objectPrecio.precio,
            iva: productoImpuesto.impuesto,
            gen_iva_id: productoImpuesto.gen_iva_id,
            desc: 0.00,
            descporcentaje: 0.00,
            despacho: false
          }
          app.dataResumen.push(newProduct)
          app.itemsCounter = app.itemsCounter + 1
          app.numLineas = app.numLineas + 1
        }
      } else {
        app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(app.num_tiquete) + ' no esta creado.' })
      }
      app.num_tiquete = null
      this.$q.loading.hide()
    },
    buscarLineasTiqueteEpelsa () {
      this.$q.loading.show()
      var app = this
      var tiqueteLeido = false
      app.num_tiquete = parseInt(app.num_tiquete.substr(0, 11))
      var tiquetesLeidos = []
      if (app.dataResumen.length !== 0) {
        tiquetesLeidos = app.dataResumen.filter(v => parseInt(v.num_tiquete) === parseInt(app.num_tiquete))
        if (tiquetesLeidos.length > 0) {
          tiqueteLeido = true
        }
      }
      if (!tiqueteLeido) {
        axios.get(app.$store.state.jhsoft.url + 'api/basculas/readtiqueteepelsa/' + app.num_tiquete).then(
          function (response) {
            if (response.data.length > 0) {
              var vendedor = null
              response.data.forEach(function (element, j) {
                const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element[0]))
                if (productoImpuesto !== undefined) {
                  if (parseInt(element[1]) > 50) {
                    element[1] = element[1] / 1000
                  }
                  var newProduct = {
                    id: app.itemsCounter,
                    producto: productoImpuesto.nombre,
                    producto_id: productoImpuesto.id,
                    producto_codigo: productoImpuesto.codigo,
                    cantidad: element[1],
                    precio: parseInt(element[2] / element[1]) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
                    iva: productoImpuesto.impuesto,
                    gen_iva_id: productoImpuesto.gen_iva_id,
                    desc: 0.00,
                    descporcentaje: 0.00,
                    despacho: false,
                    num_tiquete: element[3],
                    num_linea_tiquete: element[4]
                  }
                  app.dataResumen.push(newProduct)
                  vendedor = element[5]
                  app.itemsCounter = app.itemsCounter + 1
                  app.numLineas = app.numLineas + 1
                } else {
                  app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element[0]) + ' no esta creado.' })
                }
              })
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
              if (app.storeItems.gen_vendedor_id === undefined) {
                app.$q.notify({ color: 'negative', message: 'Error vendedor con codigo ' + vendedor + ' no existe, se cargara el vendedor por defecto.' })
                app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
              }
            } else {
              app.$q.notify({ color: 'negative', message: 'Error al leer el tiquete.' })
            }
            app.num_tiquete = null
            app.$q.loading.hide()
          }
        )
      } else {
        app.$q.notify({ color: 'negative', message: 'El tiquete ya esta cargado.' })
        app.num_tiquete = null
        app.$q.loading.hide()
      }
    },
    buscarLineasTiqueteMarques () {
      this.$q.loading.show()
      var app = this
      var tiqueteLeido = false
      var tiquetesLeidos = []
      if (app.dataResumen.length !== 0) {
        tiquetesLeidos = app.dataResumen.filter(v => parseInt(v.num_tiquete) === parseInt(app.num_tiquete.substr(6, 6)))
        if (tiquetesLeidos.length > 0) {
          tiqueteLeido = true
        }
      }
      if (!tiqueteLeido) {
        axios.get(app.$store.state.jhsoft.url + 'api/basculas/verificartiquetemarques/' + parseInt(app.num_tiquete.substr(6, 6)) + '/' + parseInt(app.num_tiquete.substr(4, 2)) + '/' + app.diaHoy).then(
          function (response) {
            if (response.data.length > 0) {
              app.$q.notify({ color: 'negative', message: 'El tiquete ya fue facturado.' })
              app.num_tiquete = null
              app.$q.loading.hide()
            } else {
              var ipMarques = null
              let ipMarquesArray = app.empresa.ruta_ip_marques.split('&')
              ipMarquesArray.forEach(function (element, j) {
                let itemMarques = element.split('-')
                if (parseInt(itemMarques[1]) === parseInt(app.num_tiquete.substr(4, 2))) {
                  ipMarques = itemMarques[0]
                }
              })
              if (ipMarques !== null) {
                axios.get('http://' + ipMarques + '/year/documentos?seek={"tipo_doc":1,"posto":' + parseInt(app.num_tiquete.substr(4, 2)) + ',"numero":' + parseInt(app.num_tiquete.substr(6, 6)) + '}&limit=1').then(
                  function (response) {
                    let cantLineas = response.data[0]['nr_parcelas']
                    axios.get('http://' + ipMarques + '/year/documentos_lnh?seek={"tipo_doc":1,"posto":' + parseInt(app.num_tiquete.substr(4, 2)) + ',"numero":' + parseInt(app.num_tiquete.substr(6, 6)) + ',"linha_f":0}&limit=' + cantLineas).then(
                      function (response) {
                        if (response.data.length > 0) {
                          var vendedor = null
                          response.data.forEach(function (element, j) {
                            // console.log(element.numero)
                            if (parseInt(element.numero) === parseInt(app.num_tiquete.substr(6, 6))) {
                              const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element.codigo))
                              if (productoImpuesto !== undefined) {
                                var newProduct = {
                                  id: app.itemsCounter,
                                  producto: productoImpuesto.nombre,
                                  producto_id: productoImpuesto.id,
                                  producto_codigo: productoImpuesto.codigo,
                                  cantidad: element.quantidade,
                                  precio: parseInt(element.preco_unit / (1 + (parseInt(productoImpuesto.impuesto) / 100))),
                                  iva: productoImpuesto.impuesto,
                                  gen_iva_id: productoImpuesto.gen_iva_id,
                                  desc: 0.00,
                                  descporcentaje: 0.00,
                                  despacho: false,
                                  num_tiquete: element.numero,
                                  puesto_tiquete: element.posto,
                                  num_linea_tiquete: element.linha_f
                                }
                                app.dataResumen.push(newProduct)
                                vendedor = 1
                                app.itemsCounter = app.itemsCounter + 1
                                app.numLineas = app.numLineas + 1
                              } else {
                                app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element.codigo) + ' no esta creado.' })
                              }
                            }
                          })
                          app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
                          if (app.storeItems.gen_vendedor_id === undefined) {
                            app.$q.notify({ color: 'negative', message: 'Error vendedor con codigo ' + vendedor + ' no existe, se cargara el vendedor por defecto.' })
                            app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
                          }
                        } else {
                          app.$q.notify({ color: 'negative', message: 'Error al leer el tiquete ó verifique la conexion a las basculas.' })
                        }
                        app.num_tiquete = null
                        app.$q.loading.hide()
                      }
                    )
                  }
                )
              } else {
                app.$q.notify({ color: 'negative', message: 'Bascula ' + parseInt(app.num_tiquete.substr(4, 2)) + ' no configurada.' })
              }
            }
          }
        )
      }
    }
  }

}
