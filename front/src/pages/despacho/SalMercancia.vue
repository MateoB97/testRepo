<template>
    <div>
        <q-page padding>
          <h3>Salida de mercancía <q-btn v-on:click="eliminarCache()" color="negative"> Eliminar caché</q-btn></h3>
          <!-- ver canastas -->
          <q-dialog v-model="showCanastas" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
              <q-header class="bg-primary">
                <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <h3>Canastas pesadas</h3>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div v-if="datos.listaCanastas.canastas !== null">
                        <div v-if="datos.listaCanastas.canastas.length > 0">
                          {{ datos.listaCanastas.canastas[0].producto }}
                        </div>
                      </div>
                      <div v-for="canasta in datos.listaCanastas.canastas" v-bind:key='canasta.id' class="row col-12">
                        <div class="col-4">
                          {{ canasta.id }}
                        </div>
                        <div class="col-4">
                          {{canasta.peso}}
                        </div>
                        <div class="col-4">
                          <q-btn icon="remove_circle" v-on:click="eliminarCanasta(canasta.producto, canasta.id)" color="negative"></q-btn>
                        </div>
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
          <!-- fin ver canastas -->
          <!-- inicio popup ingreso de productos manualmente -->
          <q-dialog v-model="openedAddPesoDespacho"  persistent :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
              <q-page-container>
                <q-page padding>
                  <h3>Agregar Peso {{ temp.producto }}</h3>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col-9">
                        <q-input color="primary" type="number" v-model="temp.cantidad" label="Cantidad" ref="cantidad" v-on:keyup.enter="() => $refs.precio.focus()">
                        </q-input>
                      </div>
                    </div>
                  </div>
                <q-btn class="q-mt-md"
                  color="primary"
                  label="Guardar"
                  @click="addPeso"
                />
                <q-btn class="q-mt-md q-ml-sm"
                  color="negative"
                  @click="closeAddProducto"
                  label="Cancelar"
                />
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup ingreso de productos manualmente -->
          <div class="row q-col-gutter-sm">
            <SelectTerceroSucursal v-model="storeItems.terceroSucursal_id" columnas='col-12 col-md-6' labelTercero='Cliente'/>
            <div class="col-3 col-md-3">
              <q-input color="primary" type="number" v-model="storeItems.temperatura" label="Temperatura en Refrigeración"></q-input>
            </div>
            <div class="col-3 col-md-3">
              <q-input color="primary" type="number" v-model="storeItems.temperatura_congelado" label="Temperatura en Congelación"></q-input>
              <!-- <q-input color="primary" type="number" v-model="storeItems.temperatura_congelado" label="Temperatura en Congelación" v-if="this.storeItems.temperatura_congelado <= this.base"></q-input>validateWeight -->
            </div>
            <div class="col-6 col-md-3">
              <q-input color="primary" type="text" v-model="storeItems.vehiculo" label="Placa Vehiculo"></q-input>
            </div>
            <div class="col-12">
              <q-select
                  label="Seleccione bascula"
                  v-model="datos.bascula"
                  :options="basculas"
                  option-value="ruta"
                  option-label="nombre"
                  option-disable="inactive"
                  emit-value
                  map-options
                />
            </div>
          </div>
          <div class="row q-mt-md text-center">
              <div class="col-12 q-mb-lg">
                <div class="col-6">
                  <q-input filled v-model="datos.barcode" v-on:keyup.enter="submitBarcode" label="Escanear..." />
                </div>
              </div>
          </div>
          <div class="row text-center">
            <div class="col-12">
              <q-btn color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
            </div>
          </div>
            <div class="row q-mt-lg">
              <div class="col-12 self-center">
                <q-table
                    title="Listado de productos"
                    :data="datos.items"
                    :columns="columns"
                    row-key="id"
                    :pagination.sync="pagination"
                    color="secondary"
                    table-style="width:100%"
                >
                  <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                      <q-btn icon="remove_red_eye" v-on:click="verCanastas(props.value)" color="positive"></q-btn>
                      <q-btn icon="edit" v-on:click="ingresarPeso(props.value)" color="warning"></q-btn>
                      <q-btn icon="delete" v-on:click="eliminarPeso(props.value)" color="negative"></q-btn>
                  </q-td>
                </q-table>
              </div>
            </div>
        </q-page>
    </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'

export default {
  name: 'PageSalMercancia',
  components: {
    SelectTerceroSucursal
  },
  data () {
    return {
      urlAPI: 'api/despachos/items',
      left: true,
      right: true,
      showCanastas: false,
      openedAddPesoDespacho: false,
      basculas: [],
      base: -18,
      storeItems: {
        items: [],
        terceroSucursal_id: null,
        temperatura: '',
        vehiculo: null,
        temperatura_congelado: ''
      },
      datos: {
        items: [],
        listaCanastas: {
          canastas: null
        },
        bascula: null
      },
      temp: {
        producto: null,
        cantidad: null
      },
      columns: [
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso_certificado', required: true, label: 'Peso Certificado', align: 'left', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso_despacho', required: true, label: 'Peso Despacho', align: 'left', field: 'peso_despacho', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cantidad', required: true, label: 'Cantidad', align: 'left', field: 'cantidad', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      pagination: {
        rowsPerPage: 10
      },
      columnsCanastas: [
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Peso', align: 'left', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      show: {
        subgrupos: false,
        productos: false
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    preSave () {
      // this.validateWeight()
      this.storeItems.datos = this.datos.items
    },
    validateWeight () {
      var app = this
      var base = -18
      console.log(this.storeItems.temperatura_congelado)
      console.log(base)
      if (this.storeItems.temperatura_congelado <= base) {
        app.$q.notify({ color: 'positive', message: 'Done' })
        return true
      } else {
        app.$q.notify({ color: 'negative', message: 'Temperatura debe ser menor o igual a -18°' })
        return false
      }
    },
    postSave () {
      this.datos.tercero_id = null
    },
    submitBarcode () {
      var app = this
      var verify = this.storeItems.items.indexOf(this.datos.barcode)
      if (verify < 0) {
        axios.get(app.$store.state.jhsoft.url + 'api/inventario/idfilter/' + app.datos.barcode).then(
          function (response) {
            if (response.data === 'error') {
              app.$q.notify({ color: 'negative', message: 'Producto ya existente en otro despacho!' })
            } else {
              var tempData = response.data[0]
              // guardar en store items
              app.storeItems.items.push(tempData.id)
              tempData.peso_despacho = 0
              // guardar para visualizacion
              var index = null
              app.datos.items.forEach(function (element, i) {
                if (tempData.producto_id === element.producto_id) {
                  index = i
                }
              })
              var item
              tempData.peso = parseFloat(tempData.peso).toFixed(3)
              if (index !== null) {
                app.datos.items[index].peso = parseFloat(tempData.peso) + parseFloat(app.datos.items[index].peso)
                app.datos.items[index].peso = app.datos.items[index].peso.toFixed(3)
                app.datos.items[index].cantidad = app.datos.items[index].cantidad + 1
                item = { id: tempData.id, producto: tempData.producto, peso: tempData.peso }
                app.datos.items[index].canastas.push(item)
              } else {
                item = { id: tempData.id, producto: tempData.producto, peso: tempData.peso }
                tempData.canastas = [item]
                tempData.cantidad = 1
                app.datos.items.push(tempData)
              }
              app.datos.barcode = null
              // console.log(app.datos.items)
            }
          }
        )
      } else {
        app.$q.notify({ color: 'negative', message: 'Producto ya existente en este despacho!' })
      }
    },
    verCanastas (id) {
      this.datos.listaCanastas = this.datos.items.filter(v => v.id === id)
      this.datos.listaCanastas = this.datos.listaCanastas[0]
      this.showCanastas = true
    },
    eliminarCanasta (producto, id) {
      console.log(producto, id)
      var indexI, indexJ, indexK, pesoRestar
      this.$q.dialog({
        message: '¿ Quieres eliminar esta fila ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.storeItems.items.forEach(function (element, i) {
          if (id === element) {
            indexI = i
          }
        })
        this.storeItems.items.splice(indexI, 1)
        //
        this.datos.items.forEach(function (element, j) {
          if (producto === element.producto) {
            indexJ = j
          }
        })
        //
        this.datos.items[indexJ].cantidad = this.datos.items[indexJ].cantidad - 1
        this.datos.items[indexJ].canastas.forEach(function (element, k) {
          if (id === element.id) {
            indexK = k
            pesoRestar = element.peso
          }
        })
        this.datos.items[indexJ].canastas.splice(indexK, 1)
        this.datos.items[indexJ].peso -= pesoRestar
        this.datos.items[indexJ].peso = this.datos.items[indexJ].peso.toFixed(3)
        if (this.datos.items[indexJ].peso < 0.0001) {
          this.datos.items.splice(indexJ, 1)
        }
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    eliminarCache () {
      this.$q.dialog({
        message: '¿ Quieres eliminar la caché ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        localStorage.removeItem('despachoDatosItems')
        localStorage.removeItem('despachoStoreItemsItems')
        this.storeItems.items = []
        this.datos.items = []
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    closeAddProducto () {
      this.temp.cantidad = null
      this.openedAddPesoDespacho = false
      this.stopGetPeso()
    },
    addPeso () {
      var item = this.datos.items.find(v => parseInt(v.producto_id) === parseInt(this.temp.producto_id))
      if (item.peso_despacho) {
        item.peso_despacho = parseFloat(item.peso_despacho) + parseFloat(this.temp.cantidad)
      } else {
        item.peso_despacho = parseFloat(this.temp.cantidad)
      }
      this.temp.cantidad = null
      this.temp.producto = null
      this.temp.producto_id = null
      // console.log(this.datos.items)
      this.stopGetPeso()
      this.openedAddPesoDespacho = false
    },
    ingresarPeso (id) {
      this.openedAddPesoDespacho = true
      this.getPeso()
      const item = this.datos.items.find(v => parseFloat(v.id) === parseFloat(id))
      this.temp.cantidad = 0
      this.temp.producto = item.producto
      this.temp.producto_id = item.producto_id
    },
    eliminarPeso (id) {
      var item = this.datos.items.find(v => parseFloat(v.id) === parseFloat(id))
      item.peso_despacho = 0
    },
    getPeso () {
      var v = this
      this.interval = setInterval(function () {
        axios.get(v.datos.bascula).then(
          function (response) {
            v.temp.cantidad = parseFloat(response.data.substr(7, 8))
          }
        )
      }, 1000)
    },
    stopGetPeso () {
      clearInterval(this.interval)
    }
  },
  created: function () {
    this.globalGetForSelect('api/productos/grupos', 'grupos')
    this.globalGetForSelect('api/generales/basculas', 'basculas')
  },
  computed: {
  },
  watch: {
    datos: {
      // This will let Vue know to look inside the array
      deep: true,

      // We have to move our method to a handler field
      handler () {
        localStorage.despachoDatosItems = JSON.stringify(this.datos.items)
      }
    },
    storeItems: {
      // This will let Vue know to look inside the array
      deep: true,

      // We have to move our method to a handler field
      handler () {
        localStorage.despachoStoreItemsItems = JSON.stringify(this.storeItems.items)
      }
    }
  },
  mounted: function () {
    if (localStorage.despachoStoreItemsItems) {
      this.storeItems.items = JSON.parse(localStorage.despachoStoreItemsItems)
    }
    if (localStorage.despachoDatosItems) {
      this.datos.items = JSON.parse(localStorage.despachoDatosItems)
    }
  }
}
</script>

<style scoped>
  .my-card{
    width: 100%;
    max-width: 250px;
    cursor:pointer}
  .my-card:hover{
    background-color:greenyellow
  }
  .my-card-prog{
    cursor: pointer;
  }
  .my-card-prog:hover{
    background-color: aqua;
  }
  h5{
    width: 100%;
    margin: 5px;
  }

  .col-lotes {
    max-height: 600px;
    overflow-y: scroll;
  }
</style>
