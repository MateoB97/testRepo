<template>
  <div>
    <q-page padding>
      <!-- Inicio modal show item -->
      <q-dialog v-model="showItemModal" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" style="height: 400px; max-width: 800px" container class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <div class="layout-padding">
              <h3>{{ showItem.nombre }}</h3>
              <div class="overflow-hidden">
                <div class="row q-col-gutter-sm">
                  <div class="col-12">
                    <p><strong>Nombre Alterno:</strong> {{ showItem.nombre_alt }}</p>
                    <p v-if="showItem.naturaleza"><strong>naturaleza:</strong> {{ naturalezas.find(element => element.value === showItem.naturaleza).label }}</p>
                    <p><strong>Consec Inicio:</strong> {{ showItem.consec_inicio }}</p>
                    <p><strong>Prefijo:</strong> {{ showItem.prefijo }}</p>
                    <p><strong>Resolucion:</strong> {{ showItem.resolucion }}</p>
                    <p><strong>Fecha Resolucion:</strong> {{ showItem.fec_resolucion }}</p>
                    <p><strong>Inicio consec Resolucion:</strong> {{ showItem.ini_num_fac }}</p>
                    <p><strong>Fin consec Resolucion:</strong> {{ showItem.fin_num_fac }}</p>
                    <p v-if="showItem.formato_impresion"><strong>Formato de Impresion:</strong>
                     {{ formatos_impre.find(element => element.value === showItem.formato_impresion).label }}
                    </p>
                    <p><strong>Nota:</strong> {{ showItem.nota }}</p>
                  </div>
                </div>
              </div>
            </div>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>
      <!-- fin modal show item -->

        <h3>Tipos de Ordenes</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-md ">
                <div class="col">
                  <q-select
                        v-model="storeItems.com_tipo_compra_id"
                        use-input
                        autofocus
                        hide-selected
                        fill-input
                        option-value="id"
                        option-label="nombre"
                        label="Compra Relacionada"
                        option-disable="inactive"
                        input-debounce="0"
                        :options="options.compras"
                        @filter="filterCompras"
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
                <div class="col">
                  <q-select
                    v-model="storeItems.fac_tipo_doc_id"
                    use-input
                    autofocus
                    hide-selected
                    fill-input
                    option-value="id"
                    option-label="nombre"
                    label="Factura Relacionada"
                    option-disable="inactive"
                    input-debounce="0"
                    :options="options.facturas"
                    @filter="filterFacturas"
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
            </div>
            <div class="row q-col-gutter-md q-mt-xs gutter-x-sm gutter-y-l">
                <div class="col">
                    <q-input v-model="storeItems.nombre" label="Nombre"/>
                </div>
                <div class="col">
                    <q-input type="number" v-model="storeItems.consec_inicio" left-label label="Consecutivo inicial"></q-input>
                </div>
                <div class="col">
                    <q-select
                      label="Seleccione Formato Impresión"
                      v-model="storeItems.formato_impresion"
                      :options="formatos_impre"
                      option-value="value"
                      option-label="label"
                      option-disable="inactive"
                      emit-value
                      map-options
                    />
                </div>
            </div>
            <div class="row gutter-x-sm gutter-y-l q-mt-sm">
                <div class="col-3">
                  <q-btn v-if="!showForUpdate" color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
                  <q-btn v-if="showForUpdate" color="primary" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
                </div>
            </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
                title="Listado de tipos de ordenes"
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
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <q-btn icon="delete" v-on:click="globalValidate('eliminar', props.value)" color="negative"></q-btn>
                    <q-btn class="q-ml-xs" icon="remove_red_eye" v-on:click="getShowItem(props.value)" color="positive"></q-btn>
                    <q-btn class="q-ml-xs" icon="edit" v-on:click="globalValidate('editar', props.value)" color="warning"></q-btn>
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
  name: 'pageFacTipoDoc',
  data: function () {
    return {
      urlAPI: 'api/ordenes/tipos',
      storeItems: {
        nombre: null
      },
      formatos_impre: [
        { label: 'Factura',
          value: '1'
        },
        {
          label: 'Cuenta Cobro',
          value: '2'
        },
        {
          label: 'Traslado',
          value: '3'
        }
      ],
      facturas: [],
      compras: [],
      options: {
        facturas: this.facturas,
        compras: this.compras
      },
      tableData: [],
      showForUpdate: false,
      showItemModal: false,
      showItem: {},
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'consec_inicial', required: true, label: 'Consec. Inicial', align: 'left', field: 'consec_inicio', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'actions'],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
      this.storeItems = {
        nombre: null
      }
    },
    filterCompras (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.compras = this.compras.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterFacturas (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.facturas = this.facturas.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    preSave () {
      if (this.storeItems.com_tipo_compra_id) {
        this.storeItems.com_tipo_compra_id = this.storeItems.com_tipo_compra_id.id
      }
      if (this.storeItems.fac_tipo_doc_id) {
        this.storeItems.fac_tipo_doc_id = this.storeItems.fac_tipo_doc_id.id
      }
    },
    postEdit () {
      this.showForUpdate = true
      this.storeItems.doc_relacionado = this.tableData.find(item => parseInt(item.id) === parseInt(this.storeItems.doc_relacionado))
      if (parseInt(this.storeItems.habilitacion_fe) === 1) {
        this.storeItems.habilitacion_fe = true
      } else {
        this.storeItems.habilitacion_fe = false
      }
    },
    getShowItem (id) {
      this.$q.loading.show()
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/tipos/' + id).then(
        function (response) {
          app.showItem = response.data
          app.$q.loading.hide()
          app.showItemModal = true
        }
      ).catch(error => {
        console.log(error.response)
        app.$q.notify('Error al cargar los datos del producto')
      })
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/compras/tipos/filtro/compras', 'compras')
    this.globalGetForSelect('api/facturacion/tipos/filtro/facturas', 'facturas')
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
