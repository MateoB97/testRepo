<template>
  <div>
    <q-page padding>

        <h3>Tipos de Comprobantes de Egresos</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-md gutter-x-sm gutter-y-l">
                <div class="col-4">
                    <q-input v-model="storeItems.nombre" label="Nombre"/>
                </div>
                <div class="col-4">
                    <q-input type="number" v-model="storeItems.consec_inicio" left-label label="Consecutivo inicial"></q-input>
                </div>
                <div class="col-4">
                    <q-select
                      class="w-100"
                      v-model="storeItems.com_tipo_compras_id"
                      use-input
                      hide-selected
                      fill-input
                      option-value="id"
                      option-label="nombre"
                      label="Tipo Compra Referencia"
                      option-disable="inactive"
                      map-options
                      emit-value
                      input-debounce="0"
                      :options="options.compras"
                      @filter="filterCompras"
                    >
                    </q-select>
                </div>
            </div>
            <div class="row gutter-x-sm gutter-y-l q-mt-sm">
                <div class="col-3">
                    <q-btn color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
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
                    <q-btn icon="delete" v-on:click="globalValidate('eliminar', props.value)" color="negative"></q-btn>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'pageComTipoComproEgreso',
  data: function () {
    return {
      urlAPI: 'api/compras/tiposcomproegreso',
      storeItems: {
        nombre: null,
        consec_inicio: null,
        com_tipo_compras_id: null
      },
      compras: [],
      options: {
        compras: this.compras
      },
      tableData: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'consec_inicio', required: true, label: 'Consec. Inicial', align: 'left', field: 'consec_inicio', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'tipo_compra_nombre', required: true, label: 'Compra asociada', align: 'left', field: 'tipo_compra_nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
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
    filterCompras (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.compras = this.compras.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
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
    .q-table-container{
        width: 100%;
    }
</style>
