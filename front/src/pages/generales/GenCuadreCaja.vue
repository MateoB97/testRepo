<template>
  <div>
    <q-page padding>

        <h3>Cuadres</h3>
        <div class="row q-mt-xl">
            <q-table
                title= 'Cuadres'
                :data="tableData"
                :columns="columns"
                :filter="filter"
                :visible-columns="visibleColumns"
                :separator="separator"
                row-key="id"
                color="secondary"
                table-style="width:100%"
            >
                <template slot="top-right" slot-scope="props">
                    <q-input
                        hide-underline
                        color="secondary"
                        v-model="filter"
                        class="col-6"
                        debounce="500"
                    >
                      <template v-slot:append>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                    <q-btn
                        flat round dense
                        :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                        @click="props.toggleFullscreen"
                    />
                </template>
                <q-td slot="body-cell-monto_apertura" slot-scope="props" :props="props">
                    {{ props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-monto_cierre" slot-scope="props" :props="props">
                    {{  props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                  <a target="_blank" :href="$store.state.jhsoft.url+'api/generales/cuadrecaja/imprimirpdf/'+ props.value +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs w-100" color="primary">Imprimir en Carta</q-btn> </a>
                    <q-btn class="q-ml-xs" color="primary" @click="print(props.value)">Imprimir Cuadre</q-btn>
                    <q-btn class="q-ml-xs" color="primary" @click="printReporteEgresos(props.value)">Imprimir Egresos</q-btn>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'PagMovResumen',
  data: function () {
    return {
      storeItems: {
        nombre: null
      },
      urlAPI: 'api/generales/cuadrecaja',
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'consecutivo', required: true, label: 'Consecutivo', align: 'left', field: 'cuadre_id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'monto_apertura', required: true, label: 'Apertura ($)', align: 'left', field: 'monto_apertura', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'monto_cierre', required: true, label: 'Cierre ($)', align: 'left', field: 'monto_cierre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha', required: true, label: 'Fecha Apertura', align: 'right', field: 'fecha_apertura', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'right', field: 'cuadre_id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'actions'],
      separator: 'horizontal',
      filter: ''
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
    print (id) {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/generales/cuadrecaja/imprimir/' + id).then(
        function (response) {
          app.$q.notify({ color: 'positive', message: 'Cuadre impreso.' })
        }
      ).catch(function (error) {
        console.log(error)
        app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
      })
    },
    printReporteEgresos (id) {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/egresos/reporteporcuadre/' + id).then(
        function (response) {
          app.$q.notify({ color: 'positive', message: 'Reporte impreso.' })
        }
      )
    }
  },
  created: function () {
    this.globalGetItems()
  },
  computed: {
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
</style>
