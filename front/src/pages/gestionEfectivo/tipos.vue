<template>
  <div>
    <q-page padding>

        <h3>Crear Tipo de Egresos</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col">
                    <q-input v-model="storeItems.nombre" label="Nombre Tipo Egreso"/>
                </div>
                <div class="col">
                     <q-select
                      label="Seleccione naturaleza"
                      v-model="storeItems.naturaleza"
                      :options="naturalezas"
                      option-label="nombre"
                      option-value="id"
                      map-options
                      emit-value
                    />
                </div>
            </div>
            <div class="row q-mt-md">
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

                <q-td slot="body-cell-naturaleza" slot-scope="props" :props="props">
                    <span v-if="parseInt(props.value) === 1">
                        Gasto
                    </span>
                    <span v-if="parseInt(props.value) === 2">
                        Entrega de Efectivo
                    </span>
                </q-td>

                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <q-btn v-if="goblalValidarEstado(props.value) == 0" class="q-ml-xs" icon="add_circle" v-on:click="globalValidate('activar', props.value)" color="primary"></q-btn>
                    <q-btn v-if="goblalValidarEstado(props.value) == 1" class="q-ml-xs" icon="remove_circle" v-on:click="globalValidate('inactivar', props.value)" color="negative"></q-btn>
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
      urlAPI: 'api/egresos/tipos',
      tableData: [],
      tipos: [],
      groupSelected: [],
      naturalezas: [
        {
          nombre: 'Gasto',
          id: 1
        },
        {
          nombre: 'Entrega Efectivo',
          id: 2
        }
      ],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'naturaleza', required: true, label: 'Naturaleza', align: 'left', field: 'naturaleza', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'tipo', 'actions'],
      separator: 'horizontal',
      filter: '',
      storeItems: {
        nombre: null,
        naturaleza: null
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
