<template>
  <div>
    <q-page padding>

        <h3>Crear IVA</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col-3">
                    <q-input v-model="storeItems.nombre" label="Nombre Impuesto"/>
                </div>
                <div class="col-3">
                    <q-input type="number" v-model="storeItems.valor_porcentaje" label="Valor Impuesto"/>
                </div>
                <div class="col-3">
                    <q-input type="number" v-model="storeItems.soenac_iva_api_id" label="id Fact Electronica"/>
                </div>
                <div class="col-3">
                    <q-input type="number" v-model="storeItems.cuenta_contable_venta" label="Cuenta Cont. Venta"/>
                </div>
                <div class="col-3">
                    <q-input type="number" v-model="storeItems.cuenta_contable_iva" label="Cuenta Cont. IVA"/>
                </div>
                <div class="col-3">
                    <q-btn v-if="!showForUpdate" color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
                    <q-btn v-if="showForUpdate" color="primary" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
                </div>
            </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
                title="Listado de impuestos"
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
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'GenImpuesto',
  data: function () {
    return {
      showForUpdate: false,
      urlAPI: 'api/generales/iva',
      tableData: [],
      tipos: [],
      groupSelected: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'valor_porcentaje', required: true, label: '%', align: 'left', field: 'valor_porcentaje', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'soenac_iva_api_id', required: true, label: 'Soenac API id', align: 'left', field: 'soenac_iva_api_id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cuenta_contable_venta', required: true, label: 'Cuenta cont. Venta', align: 'left', field: 'cuenta_contable_venta', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cuenta_contable_iva', required: true, label: 'Cuenta cont. IVA', align: 'left', field: 'cuenta_contable_iva', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'tipo', 'actions'],
      separator: 'horizontal',
      filter: '',
      storeItems: {
        nombre: null,
        valor_porcentaje: null,
        soenac_iva_api_id: null
      }
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
