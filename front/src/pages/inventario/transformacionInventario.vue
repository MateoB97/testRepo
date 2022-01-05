<template>
  <div>
    <q-page padding>

        <h3>Transformacion Productos - Inventario</h3>
        <div class="row q-mt-xl">
            <div class="col-6">
                <q-select
                  class="w-100"
                  v-model="storeItems.producto_id_sal"
                  use-input
                  hide-selected
                  fill-input
                  option-value="id"
                  option-label="nombre"
                  label="Producto - Sacar inventario"
                  option-disable="inactive"
                  map-options
                  input-debounce="0"
                  :options="options.productOut"
                  @filter="filterProductOut"
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
              <div class="col-6">
                <q-input label="Cantidad a reducir" v-model="storeItems.cantidad" class="date-field" :rules="['int'] " />
              </div>
        </div>
        <div class="row q-mt-xl">
            <div class="col-6">
                <q-select
                  class="w-100"
                  v-model="storeItems.producto_id_ent"
                  use-input
                  hide-selected
                  fill-input
                  option-value="id"
                  option-label="nombre"
                  label="Producto - Ingresar inventario"
                  option-disable="inactive"
                  map-options
                  input-debounce="0"
                  :options="options.productIn"
                  @filter="filterProductIn"
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
        <div class="row q-mt-sm">
            <div class="col-2">
                <q-btn color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
            </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'PagTransformacion',
  data: function () {
    return {
      storeItems: {
      },
      options: {
        productOut: this.productos,
        productIn: this.productos
      },
      urlAPI: 'api/transformacion/items',
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'codigo', required: true, label: 'Codigo', align: 'left', field: 'codigo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Producto', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'subgrupo', required: true, label: 'Subgrupo', align: 'left', field: 'subgrupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'grupo', required: true, label: 'Grupo', align: 'left', field: 'grupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cantidad', required: true, label: 'Cantidad', align: 'left', field: 'cantidad', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'costo_promedio', required: true, label: 'Costo Promedio', align: 'left', field: 'costo_promedio', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'actions'],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
      // this.options.productOut = []
      // this.options.productIn = []
    },
    preSave () {
    },
    postEdit () {
    },
    filterProductOut (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productOut = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        console.log(this.options.productOut)
      })
    },
    filterProductIn (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productIn = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    }
  },
  created: function () {
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
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
