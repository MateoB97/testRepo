<template>
  <div class="div-focus" tabindex="0" @keyup.35="openedAddPago = true" @keyup.36="() => $refs.scan.focus()" @keyup.45="openedAddProductoMethod">
    <q-page padding>

        <!-- inicio popup impresion al guardar -->
          <q-dialog v-model="openedPrintFactura" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 250px; max-width: 800px; background-color: #7E7EF4; color: #FFFFFF">
              <q-header >
                <q-toolbar style="background-color: #7E7EF4!important;">
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12 text-center">
                        <h4 class="no-margin">¡{{ tipoOrden.nombre }} Guardada! </h4>
                      </div>
                      <div class="col-12 text-center">
                        <h5 class="no-margin">Numero: {{ callback[0] }}</h5>
                      </div>
                    </div>
                    <div class="row col-12 q-col-gutter-md q-mt-md">
                      <div class="col-6 text-center">
                        <a target="_blank" :href="$store.state.jhsoft.url+'api/ordenes/imprimir/'+ callback[1]+'?token='+ $store.state.jhsoft.token "><q-btn class="q-ml-xs btn-naranja"> Imprimir Carta</q-btn> </a>
                      </div>
                      <div class="col-6 text-center">
                        <q-btn class="q-ml-xs btn-limon" @click="printPOS(callback[1])"> Imprimir POS </q-btn>
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup impresion al guardar -->

        <!-- inicio popup ingreso de productos manualmente -->
          <q-dialog v-model="openedAddProducto"  persistent :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
              <q-page-container>
                <q-page padding>
                  <h3>Agregar Producto</h3>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div  class="col-1">
                        <q-input v-model="codigo_producto" label="Cod." readonly>
                        </q-input>
                      </div>
                      <div class="col">
                        <q-select
                          ref="selectProductoManual"
                          v-model="producto_selected"
                          use-input
                          hide-selected
                          fill-input
                          option-value="id"
                          option-label="nombre"
                          label="Producto"
                          option-disable="inactive"
                          input-debounce="0"
                          :options="options.productos"
                          @filter="filterProductoManual"
                        >
                          <template v-slot:no-option>
                            <q-item>
                              <q-item-section class="text-grey">
                                No results
                              </q-item-section>
                            </q-item>
                          </template>
                          <template v-slot:option="scope">
                            <q-item
                              v-bind="scope.itemProps"
                              v-on="scope.itemEvents"
                            >
                              <q-item-section>
                                <q-item-label v-html="scope.opt.codigo + ' - ' + scope.opt.nombre" />
                              </q-item-section>
                            </q-item>
                          </template>
                        </q-select>
                      </div>
                      <div class="col">
                        <q-input color="primary" type="number" v-model="temp.cantidad" label="Cantidad" ref="cantidad" v-on:keyup.enter="() => $refs.precio.focus()">
                        </q-input>
                      </div>
                      <div class="col" style="position:relative">
                        <p class="v-money-label" style="top:25px"> Valor: </p>
                        <money v-model="precio_producto" v-bind="money" class="v-money"></money>
                      </div>
                    </div>
                  </div>
                <q-btn class="q-mt-md"
                  color="primary"
                  label="Guardar"
                  @click="addProducto"
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

        <div class="row">
        <div class="row q-col-gutter-md col-4">
            <h4 style="margin: 0px">{{ tipoOrden.nombre }}</h4>
            <div v-if="viewTerceros" class="col-12 row q-col-gutter-md">
              <SelectTerceroSucursal v-model="sucursal" :editor="sucursal" columnas='col-12' labelTercero='Tercero'/>
            </div>
            <div class="col-12 row q-col-gutter-md">
              <div class="col-6">
                <q-input label="Fecha de Movimiento" v-model="storeItems.fecha_orden" class="date-field" mask="date" :rules="['date']">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                        <q-date v-model="storeItems.fecha_orden" @input="() => $refs.qDateProxy1.hide()" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <!-- // -->
            </div>
            <div class="row col-12 q-col-gutter-sm box-resumen-movimiento q-mt-md">
              <div class="col-6">
                  <div class="col-12">
                    Subtotal General: $ {{ subtotal | toMoney }}
                  </div>
                  <div class="col-12">
                    Descuento: $ {{ descuento | toMoney }}
                  </div>
                  <div v-for="impuesto in arrayImpuestos" :key="impuesto[0]" class="col-12">
                    -- IVA {{ impuesto[0] }}: $ {{ parseInt(impuesto[1]) * ((parseInt(impuesto[0])/100)) | toMoney }} - Base: {{ impuesto[1] | toMoney}}
                  </div>
                  <div class="col-12">
                    Total IVA: $ {{ ivatotal | toMoney }}
                  </div>
                  <div class="col-12">
                    <span style="font-size:28px">Total: $ {{ total | toMoney }}</span>
                  </div>
              </div>
              <div class="col-6">
                <div class="col-12">
                  <q-btn class="btn-naranja w-100" v-on:click="openedAddProductoMethod" label="Agregar Producto" />
                </div>
                <div class="col-12 w-100 q-mt-sm">
                  <q-btn class="btn-azul w-100" v-on:click="globalValidate('guardar')" label="Guardar" />
                </div>
              </div>
            </div>
            <!-- inicio tabla con calculos -->
        </div>
        <div class="col-8">
            <div class="col-12">
              <div class="row">
                <h4 style="margin: 0px">Lista de productos</h4>
                <div class="col-12 q-mt-md">
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
                        <q-td key="peso" :props="props">{{ parseFloat(props.row.cantidad).toFixed(3) }}</q-td>
                        <q-td key="precio" :props="props">{{ Math.round(props.row.precio) | toMoney }}</q-td>
                        <q-td key="desc" :props="props">
                          {{ props.row.desc | toMoney }}
                          <q-popup-edit v-model="props.row.desc" @hide="setPorcentaje(props.row.id)" title="Descuento ($)" buttons>
                            <money v-model="props.row.desc" v-bind="money" class="v-money"></money>
                          </q-popup-edit>
                        </q-td>
                        <q-td key="descporcentaje" :props="props">
                          {{ props.row.descporcentaje }}
                          <q-popup-edit v-model="props.row.descporcentaje" @hide="setDescuento(props.row.id)" title="Descuento (%)" buttons>
                            <q-input type="number" v-model="props.row.descporcentaje" dense autofocus />
                          </q-popup-edit>
                        </q-td>
                        <q-td key="precioDescuento" :props="props">{{ (props.row.precio - props.row.desc) | toMoney  }}</q-td>
                        <q-td key="iva" :props="props">
                          {{ props.row.iva }}
                          <q-popup-edit v-model="props.row.iva" title="iva" buttons>
                            <q-input type="number" v-model="props.row.iva" dense autofocus />
                          </q-popup-edit>
                        </q-td>
                        <q-td key="ivapesosunit" :props="props">{{ ivaUnitRow(props.row) | toMoney }}</q-td>
                        <q-td key="preciototalunit" :props="props">
                          {{ Math.round(props.row.precio - props.row.desc + ivaUnitRow(props.row)) | toMoney }}
                          <q-popup-edit v-model="temp.precioLinea" @hide="setPrecio(props.row.id)" title="Precio Total Unit." buttons>
                            <money v-model="temp.precioLinea" v-bind="money" class="v-money"></money>
                          </q-popup-edit>
                        </q-td>
                        <q-td key="subtotal" :props="props">{{ Math.round(( Math.round(props.row.precio)) * props.row.cantidad) | toMoney }}</q-td>
                        <q-td key="ivatotal" :props="props">{{ ivaUnitRow(props.row) * props.row.cantidad | toMoney }}</q-td>
                        <q-td key="total" :props="props">{{ Math.round((( Math.round(props.row.precio) - props.row.desc + ivaUnitRow(props.row)) * props.row.cantidad)).toLocaleString('de-DE') }}</q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <div class="col-12 q-mt-md text-center">
                    <q-btn class="btn-coral" style="width:50%" v-on:click="eliminarSelected()" label="Eliminar Seleccionados" />
                  </div>
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
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'
import { Money } from 'v-money'
// import { parse } from 'path'
const axios = require('axios')

export default {
  name: 'CreateOrden',
  params: 'doc',
  components: {
    SelectTerceroSucursal,
    Money
  },
  data: function () {
    return {
      money: {
        decimal: ',',
        thousands: '.',
        prefix: '          $ ',
        suffix: '',
        precision: 0,
        masked: false
      },
      urlAPI: 'api/ordenes/items',
      tipoOrden: {},
      storeItems: {
      },
      datos: {
      },
      arrayImpuestos: [],
      selected: [],
      callback: [],
      descuentoGnal: 0,
      descripImpuestos: [],
      listadoPrecios: [],
      productosImpuestos: [],
      showDocReferencia: false,
      showReferencia: false,
      openedAddProducto: false,
      precio_producto: 0,
      producto_selected: null,
      viewDespacho: true,
      viewTerceros: true,
      pagos: [],
      openedAddPago: false,
      vendedores: [],
      formasPago: [],
      options: {
        productos: this.productos
      },
      temp: {
        cantidad: null,
        precioLinea: 0
      },
      sucursal: null,
      columns: [
        { name: 'producto_id', required: true, label: 'Producto id', align: 'left', field: 'producto_id', sortable: true, classes: 'my-class', style: 'width: 80px' },
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Cantidad', align: 'right', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'precio', required: true, label: 'Precio', align: 'right', field: 'precio', sortable: true, classes: 'my-class', style: 'width: 200px' },
        // { name: 'desc', required: true, label: 'Descuento', align: 'right', field: 'desc', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'descporcentaje', required: true, label: 'Descuento (%)', align: 'right', field: 'descporcentaje', sortable: true, classes: 'my-class', style: 'width: 200px' },
        // { name: 'precioDescuento', required: true, label: 'Precio Con Descuento', align: 'right', field: 'precioDescuento', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'iva', required: true, label: 'IVA (%)', align: 'right', field: 'iva', sortable: true, classes: 'my-class', style: 'width: 200px' },
        // { name: 'ivapesosunit', required: true, label: 'IVA ($) unit', align: 'right', field: 'ivapesosunit', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'preciototalunit', required: true, label: 'Precio Tot Unit', align: 'right', field: 'preciototalunit', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'subtotal', required: true, label: 'Subtotal', align: 'right', field: 'subtotal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'ivatotal', required: true, label: 'IVA total', align: 'right', field: 'ivatotal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'total', required: true, label: 'Total', align: 'right', field: 'total', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      grupos: [],
      openedPrintFactura: false,
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
      this.openedAddPago = false
      this.dataResumen = []
      this.descuentoGnal = 0
      this.datos.despacho = null
      this.sucursal = null
      this.datos.tercero_id = null
      if (callback !== null) {
        this.itemsCounter = 1
        this.callback = callback
        this.openedPrintFactura = true
      }
      this.storeItems = {}
      var today = new Date()
      var dd = String(today.getDate()).padStart(2, '0')
      var mm = String(today.getMonth() + 1).padStart(2, '0')
      var yyyy = today.getFullYear()
      today = yyyy + '/' + mm + '/' + dd
      this.storeItems.fecha_orden = today
    },
    preSave () {
      this.storeItems.lineas = this.dataResumen
      this.storeItems.tercero_sucursal_id = this.sucursal
      this.storeItems.total = parseInt(this.total)
      this.storeItems.subtotal = parseInt(this.subtotal)
      this.storeItems.descuento = parseInt(this.descuento)
      this.storeItems.ivatotal = parseInt(this.ivatotal)
      this.storeItems.ord_tipo_orden_id = this.tipoOrden.id
    },
    postEdit () {
    },
    filterProductoManual (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productos = this.productos.filter(v => v.codigo.toLowerCase().indexOf(needle) > -1)
        if (this.options.productos.length < 1) {
          this.options.productos = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        }
      })
    },
    openedAddProductoMethod () {
      this.openedAddProducto = true
    },
    closeAddProducto () {
      this.producto_selected = null
      this.temp.cantidad = null
      this.precio_producto = parseInt(0)
      this.openedAddProducto = false
    },
    ivaUnitRow (row) {
      return ((Math.round(row.precio) - row.desc) * (row.iva / 100))
    },
    addProducto () {
      var app = this
      if (app.temp.cantidad === null) {
        app.$q.notify({ color: 'negative', message: 'La cantidad debe ser diferente de 0.' })
        app.$refs.cantidad.focus()
      } else {
        if (app.listadoPrecios.length === 0) {
          app.$q.notify({ color: 'negative', message: 'Se debe seleccionar un cliente o un despacho para cargar el listado de precios.' })
        } else {
          const productoImpuesto = app.productosImpuestos.find(v => v.id === app.producto_selected.id)
          var newProduct = {
            id: app.itemsCounter,
            producto: app.producto_selected.nombre,
            producto_id: app.producto_selected.id,
            producto_codigo: productoImpuesto.codigo,
            cantidad: app.temp.cantidad,
            precio: parseInt(app.precio_producto) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
            iva: productoImpuesto.impuesto,
            desc: 0.00,
            descporcentaje: 0.00,
            despacho: false
          }
          app.dataResumen.push(newProduct)
          app.itemsCounter = app.itemsCounter + 1
          app.producto_selected = null
          app.precio_producto = parseInt(0)
          app.temp.cantidad = null
          app.$refs.selectProductoManual.focus()
        }
      }
    },
    setPorcentaje (v) {
      var index
      this.dataResumen.forEach(function (element, i) {
        if (v === element.id) {
          index = i
        }
      })
      this.dataResumen[index].descporcentaje = (this.dataResumen[index].desc / (this.dataResumen[index].precio) * 100).toFixed(2)
    },
    setPrecio (v) {
      var index
      this.dataResumen.forEach(function (element, i) {
        if (v === element.id) {
          index = i
        }
      })
      this.dataResumen[index].precio = parseInt(this.temp.precioLinea) / (1 + (parseInt(this.dataResumen[index].iva) / 100))
      this.dataResumen[index].desc = 0
      this.temp.precioLinea = 0
    },
    setDescuento (v) {
      var index
      this.dataResumen.forEach(function (element, i) {
        if (v === element.id) {
          index = i
        }
      })
      this.dataResumen[index].desc = ((this.dataResumen[index].precio) * (this.dataResumen[index].descporcentaje / 100)).toFixed(2)
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
    verificarTipoOrden () {
      this.$q.loading.show()
      this.sucursal = null
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/ordenes/tipos/' + this.$route.params.id).then(
        function (response) {
          app.tipoOrden = response.data
          app.dataResumen = []
          app.pagos = []
          app.viewTerceros = true
          app.$q.loading.hide()
        }
      )
    },
    printPOS (id) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/ordenes/imprimirpos/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      )
    }
  },
  created: function () {
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productosImpuestos')
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
    var today = new Date()
    var dd = String(today.getDate()).padStart(2, '0')
    var mm = String(today.getMonth() + 1).padStart(2, '0')
    var yyyy = today.getFullYear()
    this.storeItems.fecha_orden = yyyy + '/' + mm + '/' + dd
  },
  computed: {
    codigo_producto: function () {
      if (this.producto_selected) {
        return this.producto_selected.codigo
      } else {
        return null
      }
    },
    subtotal: function () {
      var response = 0
      this.dataResumen.forEach(function (element, i) {
        response = (Math.round(element.precio) * element.cantidad) + parseInt(response)
      })
      return response
    },
    descuento: function () {
      var response = 0
      this.dataResumen.forEach(function (element, i) {
        response = (element.desc * element.cantidad) + parseInt(response)
      })
      return response
    },
    ivatotal: function () {
      var responseIVA = 0
      this.arrayImpuestos.forEach(function (impuesto, i) {
        responseIVA = parseInt(impuesto[1]) * ((parseInt(impuesto[0]) / 100)) + parseInt(responseIVA)
      })
      return responseIVA
    },
    total: function () {
      return this.subtotal - this.descuento + this.ivatotal
    },
    diaHoy: function () {
      var today = new Date()
      var dd = String(today.getDate()).padStart(2, '0')
      var mm = String(today.getMonth() + 1).padStart(2, '0')
      var yyyy = today.getFullYear()
      today = dd + '-' + mm + '-' + yyyy
      return today
    },
    classDevolucion: function () {
      if (this.total > this.totalAbono) {
        return 'text-danger'
      } else {
        return 'text-succes'
      }
    }
  },
  watch: {
    producto_selected: {
      deep: true,
      handler () {
        var app = this
        if (this.producto_selected) {
          const objectPrecio = this.listadoPrecios.find(v => v.producto_id === this.producto_selected.id)
          app.precio_producto = objectPrecio.precio
        }
      }
    },
    descuentoGnal: {
      deep: true,
      handler () {
        var app = this
        this.dataResumen.forEach(function (element, i) {
          element.descporcentaje = parseInt(app.descuentoGnal)
          element.desc = ((element.precio) * (element.descporcentaje / 100)).toFixed(2)
        })
      }
    },
    dataResumen: {
      deep: true,
      handler () {
        var app = this
        var arrayIvas = []
        app.arrayImpuestos = []
        this.dataResumen.forEach(function (item, i) {
          if (arrayIvas.find(element => element === item.iva) === undefined) {
            arrayIvas.push(item.iva)
          }
        })
        arrayIvas.forEach(function (item, i) {
          const list = app.dataResumen.filter(v => v.iva === item)
          var subtotal = 0
          list.forEach(function (itemList, i) {
            subtotal = subtotal + ((Math.round(itemList.precio) - parseInt(itemList.desc)) * parseFloat(itemList.cantidad))
          })
          app.arrayImpuestos.push([item, subtotal])
        })
      }
    },
    sucursal: {
      handler () {
        if (this.sucursal !== null) {
          var app = this
          axios.get(this.$store.state.jhsoft.url + 'api/productos/preciosporsucursal/' + this.sucursal).then(
            function (response) {
              app.listadoPrecios = response.data
            }
          )
        }
      }
    },
    $route: {
      deep: true,
      handler () {
        this.verificarTipoOrden()
      }
    }
  },
  filters: {
    toMoney: function (value) {
      return parseInt(value).toLocaleString('de-DE')
    }
  },
  mounted () {
    this.verificarTipoOrden()
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
