<template>
  <div>
    <q-page padding>

        <h3>Tipos de recibos de caja</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-md gutter-x-sm gutter-y-l">
                <div class="col-4">
                    <q-input v-model="storeItems.nombre" label="Nombre"/>
                </div>
                <div class="col-4">
                    <q-input type="number" v-model="storeItems.consec_inicio" left-label label="Consecutivo inicial"></q-input>
                </div>
                <div class="col-4">
                    <q-option-group
                      :options="options.facturas"
                      label="label"
                      value="id"
                      type="checkbox"
                      v-model="storeItems.tipos_doc"
                    ></q-option-group>
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
                title="Listado de tipos de documentos"
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
                    <q-btn class="q-ml-xs" icon="edit" v-on:click="globalValidate('editar', props.value)" color="warning"></q-btn>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'
// import { parse } from 'path'

export default {
  name: 'pageFacTipoDoc',
  data: function () {
    return {
      urlAPI: 'api/facturacion/tiposrecibocaja',
      storeItems: {
        nombre: null,
        consec_inicio: null,
        tipos_doc: []
      },
      showForUpdate: false,
      facturas: [],
      options: {
        facturas: []
      },
      tableData: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'consec_inicio', required: true, label: 'Consec. Inicial', align: 'left', field: 'consec_inicio', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tipo_doc_nombre', required: true, label: 'Factura asociada', align: 'left', field: 'tipo_doc_nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
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
    },
    preSave () {
    },
    postEdit () {
    }
  },
  created: function () {
    var app = this
    axios.get(this.$store.state.jhsoft.url + 'api/facturacion/tipos/filtro/facturas').then(
      function (response) {
        response.data.forEach(element => {
          app.options.facturas.push({ label: element.nombre, value: String(element.id) })
        })
      }
    )
    this.globalGetItems()
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
