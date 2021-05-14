<template>
  <div>
    <q-page padding>
        <h3>Crear SubGrupo</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col">
                    <q-select
                      label="Seleccione Grupo"
                      v-model="storeItems.prodGrupo_id"
                      :options="grupos"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                    />
                </div>
                <div class="col">
                    <q-input v-model="storeItems.nombre" label="Nombre Subgrupo"/>
                </div>
                <div class="col-3">
                    <q-checkbox v-model="storeItems.encabezado_etiqueta" left-label label="Producto Carnico Comestible ?" />
                </div>
            </div>
            <div class="row q-mt-md">
              <div class="col-3">
                  <q-btn v-if="!showForUpdate" class="btn-azul" v-on:click="globalValidate('guardar')" label="Guardar" />
                  <q-btn v-if="showForUpdate" class="btn-naranja" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
                </div>
            </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
                title="Listado de Subgrupos de productos"
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
                    <q-btn v-if="goblalValidarEstado(props.value) == 0" class="q-ml-xs btn-azul" v-on:click="globalValidate('activar', props.value)">ACTIVAR</q-btn>
                    <q-btn v-if="goblalValidarEstado(props.value) == 1" class="q-ml-xs btn-coral" v-on:click="globalValidate('inactivar', props.value)">DESACTIVAR</q-btn>
                    <q-btn class="q-ml-xs btn-naranja" v-on:click="globalValidate('editar', props.value)">EDITAR</q-btn>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'ProdSubgrupo',
  data: function () {
    return {
      showForUpdate: false,
      urlAPI: 'api/productos/subgrupos',
      tableData: [],
      grupos: [],
      groupSelected: [],
      prodGrupo_nombre: null,
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'grupo', required: true, label: 'Grupo', align: 'left', field: 'grupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'grupo', 'actions'],
      separator: 'horizontal',
      filter: '',
      storeItems: {
        nombre: null,
        prodGrupo_id: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
      this.storeItems.prodGrupo_id = this.storeItems.prodGrupo_id.id
      if (this.storeItems.encabezado_etiqueta === true) {
        this.storeItems.encabezado_etiqueta = 1
      } else {
        this.storeItems.encabezado_etiqueta = 0
      }
    },
    postEdit () {
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
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
