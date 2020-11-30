<template>
  <div>
    <q-page padding>

        <h3>Cambio de precios por lista</h3>
        <div class="row">
            <div class="col-6">
                <q-select
                    label="Seleccione la lista de precios"
                    v-model="prod_lista_precio"
                    :options="listaprecios"
                    option-value="id"
                    option-label="nombre"
                    emit-value
                    map-options
                    @input="selectedLista()"
                    />
            </div>
        </div>
        <div v-if="tableVisible" class="row q-mt-xl">
            <div class="col-12">
                <q-table
                    title="Productos"
                    :data="dataResumen"
                    :columns="columns"
                    :filter="filter"
                    row-key="name"
                    class="my-sticky-header-table"
                    binary-state-sort
                    table-style="max-height: 400px"
                    virtual-scroll
                    :pagination.sync="pagination"
                    :rows-per-page-options="[50]"
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

                    <template v-slot:body="props">
                      <q-tr :props="props">
                        <q-td key="codigo" :props="props">{{ props.row.codigo }}</q-td>
                        <q-td key="nombre" :props="props">{{ props.row.nombre }}</q-td>
                        <q-td key="precio" :props="props">
                          {{ props.row.precio | toMoney }}
                          <q-popup-edit v-model="props.row.precio" @hide="modificarPrecio(props.row.codigo, props.row.precio)" title="Precio ($)" buttons>
                            <!-- <money autofocus v-model="props.row.precio" v-bind="money" class="v-money"></money>
                             -->
                            <q-input v-model.lazy="props.row.precio" v-money="money" dense autofocus/>
                          </q-popup-edit>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')
import { VMoney } from 'v-money'

export default {
  name: 'prodCambioPrecios',
  data: function () {
    return {
      money: {
        decimal: ',',
        thousands: '.',
        prefix: '',
        suffix: '',
        precision: 0,
        masked: false
      },
      showForUpdate: false,
      dataResumen: [],
      prod_lista_precio: null,
      listaprecios: [],
      tableVisible: false,
      columns: [
        { name: 'codigo', required: true, label: 'Producto id', align: 'left', field: 'codigo', sortable: true, classes: 'my-class', style: 'width: 80px' },
        { name: 'nombre', required: true, label: 'Producto', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'precio', required: true, label: 'Precio', align: 'right', field: 'precio', sortable: false, classes: 'my-class', style: 'width: 200px' }
      ],
      separator: 'horizontal',
      filter: '',
      pagination: {
        rowsPerPage: 50
      }
    }
  },
  directives: { money: VMoney },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
    },
    postEdit () {
    },
    selectedLista () {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/productos/listadodeproductosporlista/' + this.prod_lista_precio).then(
        function (response) {
          app.dataResumen = response.data
          app.tableVisible = true
        }
      )
    },
    modificarPrecio (codigo, precio) {
      var app = this
      precio = precio.replace('.', '').replace('.', '')
      const itemPrecio = app.dataResumen.find(v => v.codigo === codigo)
      itemPrecio.precio = precio
      axios.get(this.$store.state.jhsoft.url + 'api/productos/modificarprecio/' + this.prod_lista_precio + '/' + codigo + '/' + precio).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Precio Modificado.' })
          }
        }
      )
    }
  },
  created: function () {
    this.globalGetForSelect('api/productos/listadeprecios/estado/activos', 'listaprecios')
  },
  computed: {
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
    .v-money{
      padding: 17px;
      border: none;
      border-bottom: 1px solid rgba(0,0,0,0.24);
      width: 100%;
    }
    .v-money:focus{
      outline: none;
      border-bottom: 1px solid #027be3;
    }
    .v-money-label{
      color: rgba(0,0,0,0.6);
      font-size: 16px;
      line-height: 20px;
      font-weight: 400;
      letter-spacing: 0.00937em;
      position: absolute;
      top: 16px;
    }
</style>
