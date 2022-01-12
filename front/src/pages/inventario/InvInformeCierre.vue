<template>
  <div>
    <q-page padding>

        <h3>Resumen Cierre de inventario</h3>
        <div class="row">
          <div>
            <GlobalFiltersComponent
              titleBtn="Exportar Variación"
              url="api/inventario/exportcierrevariacion"
              :datesFilter="true"
              :dateUnique="0"
            />
            </div>
          <div>
            <GlobalFiltersComponent
                titleBtn="Exportar Cierre - Pesadas"
                url="api/inventario/exportcierrepesadas"
                :datesFilter="true"
                :dateUnique="1"
              />
          </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
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
                <q-td slot="body-cell-Costo" slot-scope="props" :props="props">
                    {{  props.value | toMoney }}
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import GlobalFiltersComponent from 'components/filters/globalFiltersComponent.vue'

export default {
  name: 'PagInformeCierreInventario',
  components: {
    GlobalFiltersComponent
  },
  data: function () {
    return {
      storeItems: {
        nombre: null
      },
      urlAPI: 'api/inventario/inv-informe-cierre',
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'fecha_cierre', required: true, label: 'Fecha Cierre', align: 'left', field: 'fecha_cierre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'SubGrupo', required: true, label: 'Sub-Grupo', align: 'left', field: 'SubGrupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'Producto', required: true, label: 'Producto', align: 'left', field: 'Producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'CantidadPesada', required: true, label: 'Cantidad Pesada', align: 'left', field: 'CantidadPesada', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'UM', required: true, label: 'UM', align: 'left', field: 'UM', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'Costo', required: true, label: 'Costo Promedio', align: 'left', field: 'Costo', sortable: true, classes: 'my-class', style: 'width: 200px' }
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
