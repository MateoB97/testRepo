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
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/tiquetenofacturadospdf/' + fecha.substring(8, 10) + '-' + fecha.substring(5, 7) + '-' + fecha.substring(0, 4) + '?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" color="primary">Enviar</q-btn> </a>
                </div>
            </div>
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
      fecha: null
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
      if (app.fecha !== null) {
        var url
        if (parseInt(app.empresa.tipo_escaner) === 2) {
          url = 'tiquetenofacturadosmarques'
        } else {
          url = 'tiquetenofacturados'
        }
        axios.get(app.$store.state.jhsoft.url + 'api/facturacion/' + url + '/' + app.fecha.substring(8, 10) + '-' + app.fecha.substring(5, 7) + '-' + app.fecha.substring(0, 4)).then(
          function (response) {
            if (response.data === 'done') {
              app.$q.notify({ color: 'positive', message: 'Reporte Impreso.' })
            } else {
              app.$q.notify({ color: 'negative', message: 'Error al imprimir, revise las fechas.' })
            }
          }
        )
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
