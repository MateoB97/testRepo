<template>
  <div>
    <q-page padding>

        <h3>Recibos</h3>
        <div class="row q-mt-xl">
            <q-table
                title= 'Recibos de Caja'
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
                <q-td slot="body-cell-total" slot-scope="props" :props="props">
                    {{ props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/recibocaja/imprimir/'+ props.value +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn> </a>
                    <q-btn class="q-ml-xs" color="primary" @click="printPOS(props.value)"> POS </q-btn>
                    <q-btn v-if="validarEstado(props.value) && globalValidarPermiso('71')" class="q-ml-xs" color="negative" @click="anular(props.value)"> anular </q-btn>
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
  name: 'PagRecResumen',
  data: function () {
    return {
      storeItems: {
        nombre: null
      },
      urlAPI: 'api/facturacion/reciboscaja',
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'consecutivo', required: true, label: 'Consecutivo', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tipo', required: true, label: 'Tipo Mov', align: 'left', field: 'tipo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_recibo', required: true, label: 'Fecha', align: 'left', field: 'fecha_recibo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'total', required: true, label: 'Total', align: 'right', field: 'total', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' }
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
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/recibocaja/imprimirpos/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      )
    },
    anular (id) {
      var app = this
      this.$q.dialog({
        message: '¿ Quieres anular este recibo ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        axios.get(app.$store.state.jhsoft.url + 'api/facturacion/recibocaja/anular/' + id).then(
          function (response) {
            if (response.data === 'done') {
              app.$q.notify({ color: 'positive', message: 'Anulacion Exitosa.' })
              app.globalGetItems()
            } else {
              app.$q.notify({ color: 'negative', message: 'Error al anular.' })
            }
          }
        )
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    validarEstado (id) {
      var item = this.tableData.find(element => element.id === id)
      if (parseInt(item.estado) === 0) {
        return false
      } else {
        return true
      }
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
