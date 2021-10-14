<template>
  <div>
    <q-page padding>
        <h3>Crear Permisos</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col">
                    <q-select
                      label="Seleccione Categoria"
                      v-model="storeItems.permisos_categoria_id"
                      :options="categorias"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                    />
                </div>
                <div class="col">
                    <q-input v-model="storeItems.nombre" label="Nombre Permiso"/>
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
                title="Listado de Permisos"
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
  name: 'PageUserPermisos',
  data: function () {
    return {
      showForUpdate: false,
      urlAPI: 'api/users/permisos/items',
      tableData: [],
      categorias: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'categoria', required: true, label: 'categoria', align: 'left', field: 'categoria', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'categoria', 'actions'],
      separator: 'horizontal',
      filter: '',
      storeItems: {
        nombre: null,
        permisos_categoria_id: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
      this.storeItems.permisos_categoria_id = this.storeItems.permisos_categoria_id.id
    },
    postEdit () {
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/users/permisos/categorias-permisos', 'categorias')
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
