<template>
  <div class="div-focus" tabindex="0" @keyup.35="openedAddPago = true" @keyup.36="() => $refs.scan.focus()" @keyup.45="openedAddProductoMethod">
    <q-page padding>
        <div class="row">
        <div class="row q-col-gutter-md col-md-4">
            <div class="row col-12">
              <h4 style="margin: 0px">Peso Marinacion</h4>
            </div>
            <div class="row col q-col-gutter-sm box-resumen-movimiento q-mt-md">
              <div class="col-12">
                <q-select
                class="w-100"
                v-model="storeItems.programacion"
                use-input
                hide-selected
                fill-input
                option-value="programacion_id"
                option-label="programacion_id"
                label="Programacion"
                option-disable="inactive"
                map-options
                input-debounce="0"
                :options="options.programaciones"
                @filter="filterProgramaciones"
                >
                <template v-slot:option="scope">
                <q-item
                    v-bind="scope.itemProps"
                    v-on="scope.itemEvents"
                >
                    <q-item-section>
                        <q-item-label v-html="scope.opt.programacion_id + ' - ' + scope.opt.tercero + ' - ' + scope.opt.sucursal" />
                    </q-item-section>
                </q-item>
                </template>
                <template v-if="storeItems.programacion" v-slot:selected>
                    {{ storeItems.programacion.programacion_id }} - {{ storeItems.programacion.tercero }} - {{ storeItems.programacion.sucursal }}
                </template>
                <template v-slot:no-option>
                    <q-item>
                    <q-item-section class="text-grey">
                        No results
                    </q-item-section>
                    </q-item>
                </template>
                </q-select>
              </div>
              <div v-if="storeItems.programacion" class="col-12">
                  Tercero: {{ storeItems.programacion.tercero }}
              </div>
              <div v-if="storeItems.programacion" class="col-12">
                  Sucursal: {{ storeItems.programacion.sucursal }}
              </div>
              <div class="col-12">
                <div class="col-12 w-100 q-mt-sm">
                  <AddProductoManual
                    :withPrice = 0
                    :listadoPrecios = []
                    @addProducto='addProductFromComponent'
                  />
                  <q-btn class="btn-azul w-100" v-on:click="globalValidate('guardar')" label="Guardar" />
                </div>
              </div>
            </div>
            <!-- inicio tabla con calculos -->
        </div>
        <div class="col-md-8">
            <div class="col-12">
              <div class="row">
                <div class="row col-12">
                  <h4 style="margin: 0px">Lista de productos</h4>
                </div>
                <div class="col-sm-12 q-mt-md">
                  <q-table
                    :data="dataResumen"
                    :columns="columns"
                    row-key="name"
                    class="my-sticky-header-table"
                    binary-state-sort
                    hide-bottom
                    table-style="max-height: 400px"
                    virtual-scroll
                    :pagination.sync="pagination"
                    :rows-per-page-options="[0]"
                  >
                    <template v-slot:body="props">
                      <q-tr :props="props">
                        <q-td key="producto_id" :props="props"><q-checkbox v-model="selected" :val="props.row.producto_id" /></q-td>
                        <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                        <q-td key="cantidad" :props="props">{{ parseFloat(props.row.cantidad).toFixed(3) }}</q-td>
                        <q-td key="acciones" :props="props">
                          <q-btn class="q-ml-xs" icon="edit" @click="ingresarPeso(props.row.producto_id)" color="warning"></q-btn>
                          <q-btn class="q-ml-xs" icon="delete" @click="eliminarPeso(props.row.producto_id)" color="negative"></q-btn>
                        </q-td>
                      </q-tr>
                    </template>
                  </q-table>
                </div>
              </div>
            </div>
        </div>
            <!-- fin tabla con calculos -->
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import AddProductoManual from 'components/productos/addProductoManual'

const axios = require('axios')

export default {
  components: { AddProductoManual },
  name: 'PesoDespacho',
  params: 'doc',
  data: function () {
    return {
      urlAPI: 'api/despachos/guardarpesodespacho',
      storeItems: {
      },
      productos: [],
      datos: {
      },
      selected: [],
      callback: [],
      codigo_producto: null,
      programaciones: [],
      interval: null,
      openedAddProducto: false,
      producto_selected: null,
      temp: {
        cantidad: null
      },
      options: {
        programaciones: this.programaciones,
        productos: this.productos
      },
      columns: [
        { name: 'producto_id', required: true, label: 'Producto id', align: 'left', field: 'producto_id', sortable: true, classes: 'my-class', style: 'width: 80px' },
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cantidad', required: true, label: 'Peso antes de Marinacion', align: 'right', field: 'cantidad', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'acciones', required: true, label: 'Acciones', align: 'right', field: 'producto_id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      itemsCounter: 1,
      dataResumen: [],
      pagination: {
        rowsPerPage: 0
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave (callback = null) {
      this.$q.loading.hide()
      this.dataResumen = []
      this.storeItems = {}
    },
    preSave () {
      this.storeItems.lineas = this.dataResumen
      this.storeItems.sal_mercancia_id = this.storeItems.despacho.id
    },
    postEdit () {
    },
    filterProgramaciones (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.programaciones = this.programaciones.filter(v => v.programacion_id.toLowerCase().indexOf(needle) > -1)
      })
    },
    openedAddProductoMethod () {
      this.openedAddProducto = true
    },
    closeAddProducto () {
      this.producto_selected = null
      this.temp.cantidad = null
      this.openedAddProducto = false
    },
    eliminarSelected () {
      var app = this
      this.selected.forEach(function (elementSelected, j) {
        app.dataResumen.forEach(function (element, i) {
          if (elementSelected === element.producto_id) {
            app.dataResumen.splice(i, 1)
          }
        })
      })
      this.selected = []
    },
    addPeso () {
      var item = this.dataResumen.find(v => parseInt(v.producto_id) === parseInt(this.temp.producto_id))
      item.cantidad = parseFloat(item.cantidad) + parseFloat(this.temp.cantidad)
      this.temp.cantidad = null
      this.temp.producto = null
      this.temp.producto_id = null
      this.stopGetPeso()
      this.openedAddProducto = false
    },
    ingresarPeso (productoId) {
      this.openedAddProducto = true
      this.getPeso()
      const item = this.dataResumen.find(v => parseFloat(v.producto_id) === parseFloat(productoId))
      this.temp.cantidad = 0
      this.temp.producto = item.producto
      this.temp.producto_id = item.producto_id
    },
    eliminarPeso (productoId) {
      var item = this.dataResumen.find(v => parseFloat(v.producto_id) === parseFloat(productoId))
      item.cantidad = 0
    },
    getPeso () {
      var v = this
      this.interval = setInterval(function () {
        axios.get('http://127.0.0.1:5002/basculas').then(
          function (response) {
            v.temp.cantidad = parseFloat(response.data.substr(7, 8))
          }
        )
      }, 1000)
    },
    stopGetPeso () {
      clearInterval(this.interval)
    },
    addProductFromComponent (newProduct) {
      this.dataResumen.push(newProduct)
    }
  },
  created: function () {
    this.globalGetForSelect('api/lotes/programaciones/abiertas/' + 0, 'programaciones')
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
  },
  computed: {
  },
  watch: {
  },
  filters: {
  },
  mounted () {
  }
}
</script>

<style>
    .div-focus:focus{
      outline: none;
    }
    .text-danger{
      color: #db2828;
    }
    .text-succes{
      color: #21ba45;
    }
    .q-table__container{
        width: 100%;
    }
    .my-sticky-header-table .q-table__middle{
        max-height: 200px;
    }
    .my-sticky-header-table thead tr th{
      position: sticky;
      z-index: 1
    }

    .my-sticky-header-table thead tr:first-child th{
      top: 0
    }
    /* this is when the loading indicator appears */
    .my-sticky-header-table.q-table--loading thead tr:last-child th{
      top: 100px
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
