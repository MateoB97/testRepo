<template>
  <div>
    <q-page padding>

        <h3>Tiquetes no facturados</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col-4">
                    <q-input label="Fecha de Movimiento" v-model="fecha" mask="date" :rules="['date']">
                    <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                        <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                            <q-date v-model="fecha" @input="() => $refs.qDateProxy1.hide()" />
                        </q-popup-proxy>
                        </q-icon>
                    </template>
                    </q-input>
                </div>
                <div v-if="fecha !== null" class="col-3">
                    <q-btn color="primary" v-on:click="generar()" label="POS" />
                    <a v-if="parseInt(empresa.tipo_escaner) === 2" target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/tiquetenofacturadosmarquespdf/' + fecha.substring(8, 10) + '-' + fecha.substring(5, 7) + '-' + fecha.substring(0, 4) + '?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" color="primary">Enviar Marques</q-btn> </a>
                    <a v-if="parseInt(empresa.tipo_escaner) !== 2" target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/tiquetenofacturadospdf/' + fecha.substring(8, 10) + '-' + fecha.substring(5, 7) + '-' + fecha.substring(0, 4) + '?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" color="primary">Enviar</q-btn> </a>
                </div>
            </div>
            {{ tiquetesNoFactArray }}
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'GenImpuesto',
  data: function () {
    return {
      fecha: null,
      tiquetesNoFactArrayTotal: []
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
    },
    postEdit () {
    },
    generar () {
      var app = this
      var counter = 0
      var tiquetesNoFactArray = []
      if (app.fecha !== null) {
        // marques
        if (parseInt(this.empresa.tipo_escaner) === 2) {
          let ipMarquesArray = this.empresa.ruta_ip_marques.split('&')
          console.log(ipMarquesArray.length)
          ipMarquesArray.forEach(function (element, j) {
            let itemMarques = element.split('-')
            // tiquetes facturados
            axios.get(app.$store.state.jhsoft.url + 'api/facturacion/tiquetesbasculadia/' + itemMarques[1] + '/' + app.fecha.substring(8, 10) + '-' + app.fecha.substring(5, 7) + '-' + app.fecha.substring(0, 4)).then(
              function (response) {
                var tiquetesFacturados = response.data
                let numTiquete = parseInt(response.data[0].num_tiquete) - 30
                // tiquetes en bascula
                axios.get('http://' + itemMarques[0] + '/year/documentos?seek={"tipo_doc":1,"posto":' + itemMarques[1] + ',"numero":' + numTiquete + '}&limit=100').then(
                  function (response1) {
                    let tiquetesBascula = response1.data
                    tiquetesBascula.forEach(function (elementTiquete, k) {
                      if ((elementTiquete.d_doc === app.fecha.replaceAll('/', '-')) && (parseInt(elementTiquete.posto) === parseInt(itemMarques[1]))) {
                        var tiqueteFacturado = tiquetesFacturados.find(v => parseInt(v.num_tiquete) === parseInt(elementTiquete.numero))
                        if (tiqueteFacturado === undefined) {
                          axios.get('http://' + itemMarques[0] + '/year/documentos_lnh?seek={"tipo_doc":1,"posto":' + itemMarques[1] + ',"numero":' + elementTiquete.numero + ',"linha_f":0}&limit=' + elementTiquete.nr_parcelas).then(
                            function (response2) {
                              response2.data.forEach(function (lineaTiquete, j) {
                                tiquetesNoFactArray.push({
                                  bascula: lineaTiquete.posto,
                                  tiquete: lineaTiquete.numero,
                                  vendedor: elementTiquete.num_vendedor,
                                  linea_tiquete: lineaTiquete.linha_f,
                                  codigo: lineaTiquete.codigo,
                                  producto: lineaTiquete.designacao,
                                  total: lineaTiquete.valor,
                                  cantidad: lineaTiquete.quantidade,
                                  unidades: lineaTiquete.unidade,
                                  precio: lineaTiquete.preco_unit
                                })
                              })
                            }
                          )
                        }
                      }
                    })
                    counter = counter + 1
                    if (counter === ipMarquesArray.length) {
                      console.log(tiquetesNoFactArray)
                      setTimeout(function () {
                        axios.post(app.$store.state.jhsoft.url + 'api/facturacion/guardartempmarques/' + app.fecha.substring(8, 10) + '-' + app.fecha.substring(5, 7) + '-' + app.fecha.substring(0, 4), { data: tiquetesNoFactArray }).then(
                          function (response3) {
                            app.$q.notify({ color: 'positive', message: 'Ya puede darle click en el boton de enviar.' })
                          }
                        )
                      }, 3000)
                    }
                  }
                )
              }
            )
          })
        } else {
          axios.get(app.$store.state.jhsoft.url + 'api/facturacion/tiquetenofacturados/' + app.fecha.substring(8, 10) + '-' + app.fecha.substring(5, 7) + '-' + app.fecha.substring(0, 4)).then(
            function (response) {
              if (response.data === 'done') {
                app.$q.notify({ color: 'positive', message: 'Reporte Impreso.' })
              } else {
                app.$q.notify({ color: 'negative', message: 'Error al imprimir, revise las fechas.' })
              }
            }
          )
        }
      } else {
        app.$q.notify({ color: 'negative', message: 'Ingrese la fecha' })
      }
    }
  },
  created: function () {
    this.globalGetForSelect('api/generales/empresa', 'empresa')
  },
  computed: {
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
</style>
