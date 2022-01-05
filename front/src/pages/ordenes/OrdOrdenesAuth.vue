<template>
  <div>
    <q-page padding>

        <h3>Ordenes</h3>
          <div>
            <q-select
              class="w-100"
              v-model="ord_orden_id"
              use-input
              hide-selected
              fill-input
              option-value="ord_orden_id"
              option-label="nombre"
              label="Tipo Orden Mercancia"
              option-disable="inactive"
              map-options
              input-debounce="0"
              :options="options.tipoOrden"
              @filter="filterCompras"
              @input="getdataportipo"
            >
              <template v-slot:no-option>
                <q-item>
                  <q-item-section class="text-grey">
                    No results
                  </q-item-section>
                </q-item>
              </template>
            </q-select>
          </div>
        <div v-if="this.orden_id !== null" class="row q-col-gutter-md">
          <div class="q-pa-md">
            <q-btn-dropdown color="primary" label="Seleccione Estado De Orden De Compra">
              <q-list>
                <q-item clickable v-close-popup @click="getTodos">
                  <q-item-section>
                    <q-item-label>Todos</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item clickable v-close-popup @click="getPorAuth(2)">
                  <q-item-section>
                    <q-item-label>Pendientes</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item clickable v-close-popup @click="getPorAuth(1)">
                  <q-item-section>
                    <q-item-label>Aceptados</q-item-label>
                  </q-item-section>
                </q-item>

                <q-item clickable v-close-popup @click="getPorAuth(3)">
                  <q-item-section>
                    <q-item-label>Rechazados</q-item-label>
                  </q-item-section>
                </q-item>
              </q-list>
            </q-btn-dropdown>
          </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
                title= 'Movimientos'
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
                <q-td slot="body-cell-estado" slot-scope="props" :props="props">
                    <div v-if="props.value === '0'">Saldada</div>
                    <div v-if="props.value === '1'">Pendiente</div>
                    <div v-if="props.value === '3'">Devolución</div>
                </q-td>
                <q-td slot="body-cell-total" slot-scope="props" :props="props">
                    {{ props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-saldo" slot-scope="props" :props="props">
                    {{  props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/ordenes/imprimir/'+ props.value +'?token='+ $store.state.jhsoft.token "><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn></a>
                    <q-btn v-if="validarAuth(props.value)" class="q-ml-xs" color="positive" @click="cambiarEstadoOrden(props.value, 1)" > Autorizar</q-btn>
                    <q-btn v-if="validarPend(props.value)" class="q-ml-xs" color="primary"  @click="cambiarEstadoOrden(props.value, 2)"> Pendiente</q-btn>
                    <q-btn v-if="validarRech(props.value)" class="q-ml-xs" color="negative" @click="cambiarEstadoOrden(props.value, 3)"> Rechazar</q-btn>
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
  name: 'PageOrdenesResumenAuth',
  data: function () {
    return {
      storeItems: {
      },
      orden_id: null,
      ord_orden_id: null,
      options: {
        tipoOrden: this.tipoOrdenes
      },
      classActive: 'Todos',
      urlAPI: 'api/ordenes/items',
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'consecutivo', required: true, label: 'Consecutivo', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tipo', required: true, label: 'Tipo Mov', align: 'left', field: 'tipo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        // { name: 'estado', required: true, label: 'Estado', align: 'left', field: 'estado', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_compra', required: true, label: 'Fecha Orden', align: 'left', field: 'fecha_orden', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'total', required: true, label: 'Total', align: 'right', field: 'total', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'saldo', required: true, label: 'Saldo', align: 'right', field: 'saldo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'autorizacion', required: true, label: 'Autorizacion', align: 'right', field: 'autorizacion', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
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
    printPOS (id) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/imprimirpos/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      )
    },
    async getTodos () {
      this.tableData = []
      this.globalGetForSelect(this.urlAPI, 'tableData')
      // console.log('DataGETTODOS: ' + this.programaciones)
    },
    validarAuth (id) {
      var item = this.tableData.find(element => element.id === id)
      if (parseInt(item.autorizacion_id) === 1) {
        return false
      } else {
        return true
      }
    },
    cambiarEstadoOrden (ordId, authId) {
      var app = this
      axios.put(this.$store.state.jhsoft.url + 'api/ordenes/cambiarauth/' + ordId + '/' + authId).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Orden modificada correctamente' })
            app.globalGetItems()
          } else {
            app.$q.notify({ color: 'negative', message: 'Orden no fue modificada' })
          }
        }
      )
    },
    validarPend (id) {
      var item = this.tableData.find(element => element.id === id)
      if (parseInt(item.autorizacion_id) === 2) {
        return false
      } else {
        return true
      }
    },
    validarRech (id) {
      var item = this.tableData.find(element => element.id === id)
      if (parseInt(item.autorizacion_id) === 3) {
        return false
      } else {
        return true
      }
    },
    async getPorAuth (id) {
      this.tableData = []
      this.$q.loading.show()
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/ordenes/porautorizacion/' + id)
        this.tableData = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las ordenes!' })
      } finally {
        this.$q.loading.hide()
        this.$forceUpdate()
      }
    },
    async getdataportipo () {
      this.orden_id = this.ord_orden_id.id
      this.$q.loading.show()
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/ordenes/portipoorden/' + this.orden_id)
        this.tableData = []
        this.tableData = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las Ordenes!' })
      } finally {
        this.$q.loading.hide()
        this.$forceUpdate()
        this.ord_orden_id = null
      }
    },
    filterCompras (val, update, abort) {
      update(() => {
        this.tableData = []
        const needle = val.toLowerCase()
        this.options.tipoOrden = this.tipoOrdenes.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/ordenes/tipos', 'tipoOrdenes')
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
