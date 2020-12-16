<template>
    <div class="row q-col-gutter-md">
        <div class="col">
            <q-btn class="btn-limon" icon="skip_previous" @click="consultarConsecutivo('-')"></q-btn>
        </div>
        <div class="col">
            <q-btn class="btn-limon" icon="skip_next" @click="consultarConsecutivo('+')"></q-btn>
        </div>
        <div class="col">
            <q-input v-model="consecToGo" class="input-consec"></q-input>
        </div>
        <div class="col">
            <q-btn class=" btn-azul" @click="irAConsecutivo()">ir</q-btn>
        </div>
        <div class="col">
            <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/imprimir/'+ movActualId +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn></a>
        </div>
        <div class="col">
            <q-btn class="q-ml-xs" color="primary" @click="printPOS(movActualId, 1)"> POS </q-btn>
        </div>
    </div>
</template>
<script>

import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  props: [],
  name: 'navMovComponent',
  data () {
    return {
      consecToGo: null,
      movActualId: null
    }
  },
  mixins: [globalFunctions],
  methods: {
    consultarConsecutivo (tipo) {
      this.numLineas = 0
      var app = this
      if (tipo === '-') {
        if (app.$route.params.consecmov === 'nuevo') {
          axios.get(app.$store.state.jhsoft.url + 'api/facturacion/consultarultimo/' + app.$route.params.id).then(
            function (response) {
              if (response.data !== 'error') {
                app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: response.data } })
              } else {
                app.$q.notify({ color: 'negative', message: 'Este es el ultimo documento.' })
              }
            }
          )
        } else {
          app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: parseInt(app.$route.params.consecmov) - 1 } })
        }
      } else {
        if (app.$route.params.consecmov === 'nuevo') {
        } else {
          axios.get(app.$store.state.jhsoft.url + 'api/facturacion/consultarultimo/' + app.$route.params.id).then(
            function (response) {
              if (response.data !== 'error') {
                if (response.data > app.$route.params.consecmov) {
                  app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: parseInt(app.$route.params.consecmov) + 1 } })
                } else {
                  app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: 'nuevo' } })
                  app.$q.notify({ color: 'negative', message: 'Este es el ultimo documento.' })
                }
              }
            }
          )
        }
      }
    },
    irAConsecutivo () {
      var app = this
      app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: this.consecToGo } })
      this.consecToGo = 0
    },
    printPOS (id, copia) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/imprimirpos/' + id + '/' + copia).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      ).catch(function (error) {
        app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
        console.log(error)
      })
    },
    loadFactura () {
      var app = this
      app.updateMode = true
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/editarfactura/' + app.$route.params.id + '/' + app.$route.params.consecmov).then(
        function (response2) {
          if (response2.data.lineas) {
            app.movActualId = response2.data.mov.id
            // cargar productos
            response2.data.lineas.forEach(function (element, j) {
              var newProduct = {
                id: app.itemsCounter,
                producto: element.producto_nombre,
                producto_id: element.producto_id,
                producto_codigo: element.producto_codigo,
                cantidad: element.cantidad,
                precio: element.precio,
                iva: element.impuesto,
                gen_iva_id: element.gen_iva_id,
                desc: ((element.precio) * (element.descporcentaje / 100)).toFixed(2),
                descporcentaje: element.descporcentaje,
                num_tiquete: element.num_tiquete,
                num_linea_tiquete: element.num_linea_tiquete,
                puesto_tiquete: element.puesto_tiquete
              }
              app.$emit('insertLine', newProduct)
              console.log('add product')
            })
            app.movActualId = response2.data.mov.id
            // cargar tercero
            app.$emit('setFactData', response2.data)
          } else {
            app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: 'nuevo' } })
            app.$q.notify({ color: 'negative', message: response2.data })
          }
        }
      )
    }
  },
  computed: {
  },
  created: function () {
  },
  watch: {
    $route: {
      deep: true,
      handler () {
        if (this.$route.params.consecmov !== 'nuevo') {
          this.loadFactura()
        }
      }
    }
  }
}
</script>

<style scoped>

</style>>
