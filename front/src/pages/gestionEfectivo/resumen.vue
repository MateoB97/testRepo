<template>
  <div>
    <q-page padding>
      <div>
        <h3>Ingresos</h3>
        <div class="row q-mt-xl">
            <q-table
                title= 'Ingresos'
                :data="tableIngresos"
                :columns="columnsIngresos"
                :filter="filterIngresos"
                row-key="id"
                color="secondary"
                table-style="width:100%"
            >
                <template slot="top-right" slot-scope="props">
                    <q-input
                        hide-underline
                        color="secondary"
                        v-model="filterIngresos"
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
                <q-td slot="body-cell-valor" slot-scope="props" :props="props">
                  {{ props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <q-btn class="q-ml-xs" icon="assignment" color="primary" @click="printIngreso(props.value)"></q-btn>
                </q-td>
            </q-table>
        </div>
      </div>
      <div>
        <h3>Egresos</h3>
        <div class="row q-mt-xl">
            <q-table
                title= 'Egresos'
                :data="tableEgresos"
                :columns="columns"
                :filter="filter"
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
                <q-td slot="body-cell-valor" slot-scope="props" :props="props">
                  {{ props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <q-btn class="q-ml-xs" icon="assignment" color="primary" @click="printEgresos(props.value)"></q-btn>
                </q-td>
            </q-table>
        </div>
      </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'PagEgreResumen',
  data: function () {
    return {
      storeItems: {
        nombre: null
      },
      urlAPI: 'api/egresos/items',
      showForUpdate: false,
      tableEgresos: [],
      tableIngresos: [],
      columns: [
        { name: 'consecutivo', required: true, label: 'Consec', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tipo_egreso', required: true, label: 'Tipo Mov', align: 'left', field: 'tipo_egreso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'valor', required: true, label: 'Valor', align: 'right', field: 'valor', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      columnsIngresos: [
        { name: 'consecutivo', required: true, label: 'Consec', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'valor', required: true, label: 'Valor', align: 'right', field: 'valor', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      separator: 'horizontal',
      filter: '',
      filterIngresos: ''
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
    printEgresos (id) {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/egresos/imprimir/' + id).then(
        function (response) {
          app.$q.notify({ color: 'positive', message: 'Egreso impreso.' })
        }
      )
    },
    printIngreso (id) {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/ingresos/imprimir/' + id).then(
        function (response) {
          app.$q.notify({ color: 'positive', message: 'Ingreso impreso.' })
        }
      )
    }
  },
  created: function () {
    this.globalGetForSelect('api/ingresos/items', 'tableIngresos')
    this.globalGetForSelect('api/egresos/items', 'tableEgresos')
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
