<template>
  <div>
    <q-page padding>

        <h3>Informes Peso Planta</h3>
        <div class="row q-mt-xl">
            <q-table
                title= 'Peso en planta por lotes'
                :data="tableData"
                :columns="columns"
                :filter="filter"
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
                <q-td slot="body-cell-pesototal" slot-scope="props" :props="props">
                    {{ globalFormatPeso(props.value) }}
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'InformePesoPlanta',
  data: function () {
    return {
      urlAPI: 'api/lotes/pesoplanta',
      storeItems: {
        nombre: null
      },
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'lote', required: true, label: 'Lote', align: 'left', field: 'lote', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'num_animales', required: true, label: 'Numero de animales', align: 'left', field: 'num_animales', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'grupo', required: true, label: 'Grupo', align: 'left', field: 'grupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'programacion', required: true, label: 'Programacion', align: 'left', field: 'programacion', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'marca', required: true, label: 'Marca', align: 'left', field: 'marca', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'pesototal', required: true, label: 'Peso Total', align: 'right', field: 'pesototal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_entrada', required: true, label: 'Fecha entrada', align: 'right', field: 'fecha_entrada', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
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
