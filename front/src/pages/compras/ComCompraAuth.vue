<template>
  <div>
    <q-page padding>

        <h3>Compras</h3>
        <div>
          <div>
            <q-select
              class="w-100"
              v-model="com_compra_id"
              use-input
              hide-selected
              fill-input
              option-value="com_compra_id"
              option-label="nombre"
              label="Tipo Compra Mercancia"
              option-disable="inactive"
              map-options
              input-debounce="0"
              :options="options.tipoCompra"
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
            <div v-if="this.compra_id !== null" class="q-pa-md">
              <q-btn-dropdown color="primary" label="Seleccione Estado De Compra de mercancia">
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
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/compras/imprimir/'+ props.value +'?token='+ $store.state.jhsoft.token "><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn></a>
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
  name: 'PageComprasResumen',
  data: function () {
    return {
      storeItems: {
        nombre: null
      },
      compra_id: null,
      com_compra_id: null,
      options: {
        tipoCompra: this.tipoCompras
      },
      urlAPI: 'api/compras/items',
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'consecutivo', required: true, label: 'Consecutivo', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'factura', required: true, label: 'Factura Referencia', align: 'left', field: 'doc_referencia', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tipo', required: true, label: 'Tipo Mov', align: 'left', field: 'tipo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'estado', required: true, label: 'Estado', align: 'left', field: 'estado', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_compra', required: true, label: 'Fecha Compra', align: 'left', field: 'fecha_compra', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_vencimiento', required: true, label: 'Fecha vencimiento', align: 'left', field: 'fecha_vencimiento', sortable: true, classes: 'my-class', style: 'width: 200px' },
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
    async getdataportipo () {
      this.compra_id = this.com_compra_id.id
      this.tableData = []
      this.$q.loading.show()
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/compras/tipos/filtro/compras/' + this.compra_id)
        this.tableData = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las Compras!' })
      } finally {
        this.$q.loading.hide()
        this.$forceUpdate()
        this.com_compra_id = null
      }
    },
    async getTodos () {
      this.tableData = []
      this.globalGetForSelect(this.urlAPI, 'tableData')
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
      axios.put(this.$store.state.jhsoft.url + 'api/compras/cambiarauth/' + ordId + '/' + authId).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Compra modificada correctamente' })
            app.globalGetItems()
          } else {
            app.$q.notify({ color: 'negative', message: 'Compra no fue modificada' })
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
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/compras/porautorizacion/' + this.compra_id + '/' + id)
        this.tableData = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las Compras!' })
      } finally {
        this.$q.loading.hide()
        this.$forceUpdate()
      }
    },
    filterCompras (val, update, abort) {
      update(() => {
        this.tableData = []
        const needle = val.toLowerCase()
        this.options.tipoCompra = this.compras.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/compras/tipos/filtro/compras', 'compras')
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
