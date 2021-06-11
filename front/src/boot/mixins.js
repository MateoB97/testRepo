const axios = require('axios')

export const globalFunctions = {
  methods: {
    validarDocumentosNotas (id) {
      var app = this
      axios.get(`facturacion/movimientos/filtro/notaporid/${id}`).then(
        function (responseNota) {
          app.dataNotas = responseNota.data
          if (app.dataNotas.length > 0) {
            return app.$q.notify({ color: 'negative', message: 'La factura tiene documentos de notas asociados' })
          }
        }
      )
    },
    globalCapitalize: function (value) {
      if (!value) return ''
      value = value.toString()
      value = value.toLowerCase()
      return value.charAt(0).toUpperCase() + value.slice(1)
    },
    globalValidate (text, id = null, noValidate = 0) {
      this.$q.dialog({
        message: '¿ Quieres ' + text + ' este item ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        if (text === 'guardar') {
          this.globalStoreItem(noValidate)
        } else if (text === 'eliminar') {
          this.globalDeleteItem(id)
        } else if (text === 'editar') {
          this.globalGetItemInfo(id)
        } else if (text === 'inactivar') {
          this.globalEditEstateItem(id, 'inactivar')
        } else if (text === 'activar') {
          this.globalEditEstateItem(id, 'activar')
        } else if (text === 'guardar-edicion') {
          this.globalStoreItemEdit(noValidate, id)
        }
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    goblalValidarEstado (id) {
      const item = this.tableData.find(item => item.id === id)
      return item.activo
    },
    async globalEditEstateItem (id, text) {
      try {
        let response = await axios.get(this.$store.state.jhsoft.url + this.urlAPI + '/estado/' + id + '/' + text)
        if (response.data === true) {
          this.$q.notify({ color: 'primary', message: 'item modificado!' })
        } else {
          this.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo ' + text })
        }
      } catch (error) {
        this.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo ' + text })
      } finally {
        this.globalGetItems()
      }
    },
    async globalGetItemInfo (id) {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + this.urlAPI + '/' + id)
        var response = data.data
        this.storeItems = response
        this.showForUpdate = true
      } catch (error) {
      } finally {
        this.$q.loading.hide()
        this.postEdit()
      }
    },
    async globalDeleteItem (id) {
      let data
      try {
        data = await axios.delete(this.$store.state.jhsoft.url + this.urlAPI + '/' + id)
        if (data.data === 'done') {
          this.$q.notify({ color: 'positive', message: 'Item Eliminado!' })
        } else {
          this.$q.notify({ color: 'negative', message: data.data })
        }
      } catch (error) {
        this.$q.notify({ color: 'negative', message: 'Error de conexion!' })
      } finally {
        this.globalGetItems()
      }
    },
    async globalGetItems () {
      this.$q.loading.show()
      try {
        await axios.get(this.$store.state.jhsoft.url + this.urlAPI).then((response) => {
          this.tableData = response.data
        })
      } catch (error) {
      } finally {
        this.$q.loading.hide()
      }
    },
    async globalStoreItem (noValidate = 0) {
      this.$q.loading.show()
      this.preSave()
      var itemNull = 0
      var app = this
      var callback = 0
      for (const prop in this.storeItems) {
        if (typeof this.storeItems[prop] === 'string') {
          this.storeItems[prop] = this.globalCapitalize(this.storeItems[prop])
        }
        if ((this.storeItems[prop] == null) && (noValidate !== 1)) {
          this.$q.notify({ color: 'negative', message: 'El campo ' + prop + ' debe tener algun valor.' })
          itemNull = 1
        }
      }
      if (itemNull !== 1) {
        this.$q.notify({ color: 'warning', message: 'Guardando item...' })
        axios.post(this.$store.state.jhsoft.url + this.urlAPI, this.storeItems).then(
          function (response) {
            if (response.data === 'done') { // si se desea restaurar el formulario el api debe devolver "done"
              for (const prop in app.storeItems) {
                if (typeof app.storeItems[prop] === 'object') {
                  app.storeItems[prop] = []
                } else if (typeof app.storeItems[prop] === 'boolean') {
                  app.storeItems[prop] = false
                } else {
                  app.storeItems[prop] = null
                }
              }
              app.globalGetItems()
              app.$q.notify({ color: 'positive', message: 'item Guardado!' })
            } else if (response.data === 'doneNoRestore') { // si NO se desea restaurar el formulario el api debe devolver "doneNoRestore"
              app.globalGetItems()
              app.$q.notify({ color: 'positive', message: 'item Guardado!' })
            } else if (response.data.length > 1) { // si NO se desea restaurar el formulario el api debe devolver "doneNoRestore"
              if (response.data[0] === 'callback') {
                callback = response.data[1]
              } else {
                app.$q.notify({ color: 'negative', message: response.data })
              }
            }
            if (callback !== 0) {
              app.$q.notify({ color: 'positive', message: 'item Guardado!' })
              app.postSave(callback)
            } else {
              app.postSave()
            }
            app.$q.loading.hide()
          }
        ).catch(function (error) {
          app.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo guardar!!!' })
          console.log(error.message)
          app.$q.loading.hide()
        }).finally(function () {
          app.showForUpdate = false
        })
      } else {
        app.$q.loading.hide()
      }
    },
    globalStoreItemEdit (noValidate = 0, id) {
      this.preSave()
      var itemNull = 0
      var app = this
      var callback = 0
      for (const prop in this.storeItems) {
        if ((this.storeItems[prop] == null) && (noValidate !== 1)) {
          this.$q.notify({ color: 'negative', message: 'El campo ' + prop + ' debe tener algun valor.' })
          itemNull = 1
        }
      }
      if (itemNull !== 1) {
        this.$q.notify({ color: 'warning', message: 'Guardando item...' })
        axios.put(this.$store.state.jhsoft.url + this.urlAPI + '/' + id, this.storeItems).then(
          function (response) {
            if (response.data === 'done') { // si se desea restaurar el formulario el api debe devolver "done"
              for (const prop in app.storeItems) {
                if (typeof app.storeItems[prop] === 'object') {
                  app.storeItems[prop] = []
                } else if (typeof app.storeItems[prop] === 'boolean') {
                  app.storeItems[prop] = false
                } else {
                  app.storeItems[prop] = null
                }
              }
              app.globalGetItems()
              app.$q.notify({ color: 'positive', message: 'item Guardado!' })
            } else if (response.data === 'doneNoRestore') { // si NO se desea restaurar el formulario el api debe devolver "doneNoRestore"
              app.globalGetItems()
              app.$q.notify({ color: 'positive', message: 'item Guardado!' })
            } else if (response.data[1]) { // si NO se desea restaurar el formulario el api debe devolver "doneNoRestore"
              if (response.data[0] === 'callback') {
                callback = response.data[1]
              }
            } else {
              app.$q.notify({ color: 'negative', message: response.data })
            }
            if (callback !== 0) {
              app.$q.notify({ color: 'positive', message: 'item Modificado!' })
              app.postSave(callback)
            } else {
              app.postSave()
            }
          }
        ).catch(function (error) {
          app.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo guardar!' })
          console.log(error)
        }).finally(function () {
          app.showForUpdate = false
          if (callback !== 0) {
            app.postSave(callback)
          } else {
            app.postSave()
          }
        })
      }
    },
    globalGetForSelect (url, objeto, objUpdate) {
      this.$q.loading.show()
      var app = this
      if (app[objUpdate]) {
        app[objUpdate] = false
      }
      axios.get(this.$store.state.jhsoft.url + url).then(
        function (response) {
          app[objeto] = response.data
          app.$forceUpdate()
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
        if (!app[objUpdate]) {
          app[objUpdate] = true
        }
      })
    },
    globalFormatPeso (value) {
      let val = parseFloat(value).toFixed(3)
      return val
    },
    globalGetShowItem (id) {
      var app = this
      axios.get(this.$store.state.jhsoft.url + this.urlAPI + '/' + id).then(
        function (response) {
          app.show = response.data
          app.postGetShowItem()
        }
      )
    },
    globalEnviarFacturaElectronica (id) {
      // event.target.disabled
      var app = this
      this.$q.loading.show()
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/datafacturacionelectronica/' + id).then(
        function (response) {
          var lineas = []
          var objeto
          var restaBolsa = 0
          var restaTaxInclusiveAmount = 0
          var lineasAmountTotal = 0
          var ivaTotal = 0
          var token = '?api_token=' + app.empresa.token_fac_elect
          var url = 'https://supercarnes-jh.apifacturacionelectronica.xyz/api/ubl2.1/invoice'
          var testId = '/' + app.empresa.test_id_fe
          var urlCompleta = null
          response.data.lineas.forEach(function (element, j) {
            objeto = {
              'unit_measure_id': element.unit_measure_id,
              'invoiced_quantity': parseFloat(element.cantidad).toFixed(6),
              'line_extension_amount': (element.precio * element.cantidad).toFixed(2),
              'free_of_charge_indicator': false,
              'description': element.description,
              'code': element.code,
              'type_item_identification_id': 4,
              'price_amount': parseFloat(element.precio).toFixed(2),
              'base_quantity': 1.000000
            }
            // Verificacion iva excluido
            if (element.gen_iva_id !== app.empresa.gen_iva_excluido_id) {
              objeto.tax_totals = [{
                'tax_id': 1,
                'tax_amount': parseInt(((element.precio - (element.precio * (element.descporcentaje / 100))) * element.cantidad) * (element.iva / 100)).toFixed(2),
                'taxable_amount': ((element.precio * element.cantidad) - parseInt(((element.precio * element.cantidad) * (element.descporcentaje / 100)))).toFixed(2),
                'percent': parseFloat(element.iva).toFixed(2)
              }]
              ivaTotal = parseInt(ivaTotal) + parseInt(objeto.tax_totals[0].tax_amount)
            } else {
              restaTaxInclusiveAmount = restaTaxInclusiveAmount + parseFloat(element.precio * element.cantidad)
            }
            // Bolsa
            if (element.producto_id === app.empresa.producto_bolsa_id) {
              objeto.unit_measure_id = 886
              objeto.line_extension_amount = '0.000000'
              objeto.free_of_charge_indicator = true
              objeto.tax_totals = [{
                'tax_id': 10,
                'tax_amount': (element.precio * element.cantidad).toFixed(2),
                'taxable_amount': '0.00',
                'unit_measure_id': 886,
                'per_unit_amount': parseInt(element.precio).toFixed(2),
                'base_unit_measure': '1.000000'
              }]
              objeto.type_item_identification_id = 3
              objeto.reference_price_id = 1
              restaBolsa = restaBolsa + (element.precio * element.cantidad)
            }
            // Descuento
            if (parseInt(element.descporcentaje) > 0) {
              objeto.allowance_charges = [{
                'charge_indicator': false,
                'allowance_charge_reason': 'Discount',
                'amount': parseInt(((element.precio * element.cantidad) * (element.descporcentaje / 100))).toFixed(2),
                'base_amount': (element.precio * element.cantidad).toFixed(2)
              }]
              objeto.line_extension_amount = ((element.precio * element.cantidad) - parseInt(((element.precio * element.cantidad) * (element.descporcentaje / 100)))).toFixed(2)
            }
            lineasAmountTotal = lineasAmountTotal + parseFloat(objeto.line_extension_amount)
            lineas.push(objeto)
          })
          var FactElect = {
            'number': response.data.movimiento.consecutivo,
            'resolution_id': parseInt(response.data.tipoDoc.resolucion_soenac_id),
            'type_document_id': parseInt(response.data.tipoDoc.soenac_tipo_doc_api_id),
            'customer': {
              'identification_number': response.data.cliente.tercero.documento,
              'name': response.data.cliente.tercero.nombre,
              'email': response.data.cliente.email,
              'type_regime_id': response.data.cliente.soenac_regimen,
              'type_liability_id': response.data.cliente.soenac_responsabilidad,
              'type_organization_id': response.data.cliente.soenac_tipo_organizacion,
              'type_document_identification_id': response.data.cliente.soenac_tipo_documento,
              'merchant_registration': response.data.cliente.tercero.registro_mercantil,
              'municipality_id': 1
            },
            'legal_monetary_totals': {
              'line_extension_amount': parseFloat(lineasAmountTotal).toFixed(2),
              'tax_exclusive_amount': parseFloat(lineasAmountTotal - restaTaxInclusiveAmount).toFixed(2),
              'tax_inclusive_amount': parseFloat(lineasAmountTotal + ivaTotal + restaBolsa).toFixed(2),
              'allowance_total_amount': '0.00',
              'charge_total_amount': '0.00',
              'payable_amount': parseFloat(lineasAmountTotal + ivaTotal + restaBolsa).toFixed(2)
            },
            'invoice_lines': lineas
          }
          if (response.data.movimiento.nota) {
            FactElect.notes = [ { 'text': response.data.movimiento.nota } ]
          }
          // Nota credito
          if (parseInt(response.data.tipoDoc.soenac_tipo_doc_api_id) === 5) {
            url = 'https://supercarnes-jh.apifacturacionelectronica.xyz/api/ubl2.1/credit-note'
            FactElect.billing_reference = {
              'number': response.data.tipoDocPrimario.prefijo + response.data.movPrimario.consecutivo,
              'uuid': response.data.movPrimario.cufe,
              'issue_date': response.data.movimiento.fecha_facturacion
            }
            FactElect.credit_note_lines = FactElect.invoice_lines
            delete FactElect.invoice_lines
          }
          // Nota debito
          if (parseInt(response.data.tipoDoc.soenac_tipo_doc_api_id) === 6) {
            url = 'https://supercarnes-jh.apifacturacionelectronica.xyz/api/ubl2.1/debit-note'
            FactElect.billing_reference = {
              'number': response.data.tipoDocPrimario.prefijo + response.data.movPrimario.consecutivo,
              'uuid': response.data.movPrimario.cufe,
              'issue_date': response.data.movimiento.fecha_facturacion
            }
            FactElect.debit_note_lines = FactElect.invoice_lines
            FactElect.requested_monetary_totals = FactElect.legal_monetary_totals
            delete FactElect.invoice_lines
            delete FactElect.legal_monetary_totals
          }
          // habilitacion
          if (parseInt(response.data.tipoDoc.habilitacion_fe) === 1) {
            FactElect.sync = false
            urlCompleta = url + testId + token
          } else {
            FactElect.sync = true
            urlCompleta = url + token
          }
          console.log(FactElect)
          axios.post(app.$store.state.jhsoft.url + 'api/facturacion/enviarfacturasoenac', { url: urlCompleta, body: FactElect }).then(
            function (response1) {
              app.erroresFE = []
              app.$q.loading.hide()
              var dataFac = {
                cufe: response1.data.uuid,
                qr: response1.data.qr_data,
                zip_key: response1.data.zip_key,
                zip_name: response1.data.zip_name,
                url_acceptance: response1.data.url_acceptance,
                url_rejection: response1.data.url_rejection,
                pdf_base64_bytes: response1.data.pdf_base64_bytes,
                dian_response_base64_bytes: response1.data.dian_response_base64_bytes,
                application_response_base64_bytes: response1.data.application_response_base64_bytes
              }
              // si es valido -> envia correo
              if (response1.data.is_valid === true) {
                app.$q.notify({ color: 'positive', message: response1.data.status_message })
                dataFac.estado_fe = 1
                var dataCorreo = {
                  'to': [
                    {
                      'email': FactElect.customer.email
                    }
                  ],
                  'bcc': [
                    {
                      'email': app.empresa.email_backup_fact_elect
                    }
                  ]
                }
                axios.post(app.$store.state.jhsoft.url + 'api/facturacion/enviarfacturasoenac', { url: 'https://supercarnes-jh.apifacturacionelectronica.xyz/api/ubl2.1/mail/send/' + response1.data.uuid + token, body: dataCorreo }).then(
                  function (response3) {
                    if (parseInt(response3.data.is_valid) === 1) {
                      app.$q.notify({ color: 'positive', message: 'Notificacion enviada al correo ' + FactElect.customer.email })
                    } else {
                      app.$q.notify({ color: 'negative', message: 'Error al enviar el email.' })
                    }
                  }
                )
              } else if (response1.data.is_valid === null) {
                app.$q.notify({ color: 'positive', message: 'Factura Enviada, Se debe consultar su estado.' })
                dataFac.estado_fe = 2
              } else {
                app.$q.notify({ color: 'negative', message: response1.data.status_message })
                dataFac.estado_fe = 0
                app.erroresFE = response1.data.errors_messages
                app.openedErrores = true
              }
              if (response1.data.is_valid !== null && response1.data.zip_key !== null) {
                app.globalAgregarCufe(id, dataFac)
              }
              if (response1.data.is_valid === null && FactElect.sync === false) {
                app.globalAgregarCufe(id, dataFac)
              }
            }
          ).catch(function (error) {
            app.$q.loading.hide()
            app.$q.notify({ color: 'positive', message: 'Documento no enviado.' })
            var dataFac = {
              estado_fe: 3
            }
            app.globalAgregarCufe(id, dataFac)
            app.erroresFE[0] = error.request.response
            app.openedErrores = true
          })
        }
      )
    },
    globalAgregarCufe (id, dataFac) {
      var app = this
      axios.post(app.$store.state.jhsoft.url + 'api/facturacion/agregarcufe/' + id, dataFac).then(
        function (response2) {
          if (response2.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'QR y CUFE guardados.' })
            app.globalGetItems()
          }
        }
      )
    },
    globalValidarPermiso (permiso) {
      var user = this.$auth.user().permisos.permisos.split(',')
      var pos = user.find(element => parseInt(element) === parseInt(permiso))
      if (pos > 0) {
        return true
      } else {
        return false
      }
    }
  },
  filters: {
    toMoney: function (value) {
      return parseInt(value).toLocaleString('de-DE')
    }
  }
}
