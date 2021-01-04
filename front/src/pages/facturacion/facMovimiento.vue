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
                        <h4 class="no-margin">¡{{ tipoDoc.nombre }} Guardada! </h4>
                      </div>
                      <div class="col-12 text-center">
                        <h5 class="no-margin">Numero: {{ callback[0] }}</h5>
                      </div>
                    </div>
                    <div class="row col-12 q-col-gutter-md q-mt-md">
                      <div class="col text-center">
                        <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/imprimir/'+ callback[1] +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs btn-naranja"> Imprimir Carta</q-btn> </a>
                      </div>
                      <div class="col text-center">
                        <q-btn class="q-ml-xs btn-limon" @click="printPOS(callback[1], 0)"> Imprimir POS </q-btn>
                      </div>
                      <div v-if="callback[2]" class="col text-center">
                        <q-btn class="q-ml-xs" color="primary" @click="globalEnviarFacturaElectronica(callback[1])"> Factura Electronica </q-btn>
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup impresion al guardar -->
        <!-- inicio popup ingresar pago -->
          <q-dialog tabindex="0" @keyup.enter="localValidate()" v-model="openedAddPago" :content-css="{minWidth: '80vw', minHeight: '50vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 50vh; max-width: 800px" class="bg-white">

              <q-page-container>
                  <q-page padding>
                  <h4>Total: $ {{ total | toMoney }} - <span class="text-succes">Cambio: $ {{ totalAbono - total | toMoney }}</span></h4>
                  <div class="overflow-hidden">
                      <div class="row col-12 q-col-gutter-sm">
                        <div class="row col-12" v-for="pago in pagos" :key="pago.id">
                          <div class="col-2">
                            <p class="q-mt-md">{{ pago.nombre }}</p>
                          </div>
                          <div class="col-10" style="position:relative">
                            <p class="v-money-label"> Valor: </p>
                            <money v-model="pago.valor" v-bind="money" class="v-money"></money>
                          </div>
                        </div>
                      </div>
                  </div>
                  <q-btn class="q-mt-sm"
                      color="primary"
                      v-close-popup
                      label="Guardar"
                  />
                  </q-page>
              </q-page-container>
              </q-layout>
          </q-dialog>
        <!-- fin popup ingresar pago -->

        <div class="row q-col-gutter-md">
        <div class="row q-col-gutter-md col-12 col-md-4">
            <div class="col-12">
              <h4 style="margin: 0px">{{ tipoDoc.nombre }} - {{ $route.params.consecmov }}</h4>
            </div>
            <div class="row q-col-gutter-md">
                <NavMovComponent @insertLine="insertLineFromNav" @setFactData="setFactDataFromNav" />
            </div>
            <div v-if="viewTerceros" class="col-12 row q-col-gutter-md">
              <SelectTerceroSucursal v-model="sucursal" :editor="sucursal" columnas='col-12' labelTercero='Cliente'/>
            </div>
            <div class="col-12 row q-col-gutter-md">
              <!-- // -->
              <!-- // -->
              <!-- inicio Datos solo para notas -->
              <div v-if="tipoDoc.naturaleza != '1' && tipoDoc.naturaleza != '4'" class="col-6">
                <q-select
                  class="w-100"
                  v-model="storeItems.docReferencia"
                  use-input
                  hide-selected
                  fill-input
                  option-value="id"
                  option-label="consecutivo"
                  label="Facturas"
                  option-disable="inactive"
                  map-options
                  input-debounce="0"
                  :options="options.movimientos"
                  @filter="filterMovimientos"
                  @input="selectMovimiento"
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
              <!-- fin Datos solo para notas -->
              <!-- // -->
              <div class="col-6">
                <q-input label="Fecha de Movimiento" v-model="storeItems.fecha_facturacion" class="date-field" mask="date" :rules="['date']">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                        <q-date v-model="storeItems.fecha_facturacion" @input="() => $refs.qDateProxy1.hide()" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <!-- // -->
              <!-- inicio datos solo para facturas -->
              <div class="col-6" v-if="tipoDoc.naturaleza === '1'">
                <q-input label="Fecha de Vencimiento" v-model="storeItems.fecha_vencimiento" class="date-field" mask="date" :rules="['date']">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="storeItems.fecha_vencimiento" @input="() => $refs.qDateProxy.hide()" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
              <div  v-if="tipoDoc.naturaleza === '1' || tipoDoc.naturaleza === '4'" class="col-6">
                <q-input v-model="descuentoGnal" label="Descuento General (%)"/>
              </div>
              <div  v-if="tipoDoc.naturaleza === '1' || tipoDoc.naturaleza === '4'" class="col-6">
                <q-select
                  class="w-100"
                  v-model="storeItems.gen_vendedor_id"
                  use-input
                  hide-selected
                  fill-input
                  option-value="id"
                  option-label="nombre"
                  label="Vendedor"
                  option-disable="inactive"
                  map-options
                  input-debounce="0"
                  :options="options.vendedores"
                  @filter="filterVendedores"
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
              <div  v-if="tipoDoc.naturaleza === '1' || tipoDoc.naturaleza === '4'" class="col-6">
                <q-input v-if="empresa.tipo_escaner == 1" filled v-model="num_tiquete" ref="scan" v-on:keyup.enter="buscarLineasTiqueteDibal" label="Escanear..."   />
                <q-input v-if="empresa.tipo_escaner == 2" filled v-model="num_tiquete" ref="scan" v-on:keyup.enter="buscarLineasTiqueteMarques" label="Escanear..."  />
                <q-input v-if="empresa.tipo_escaner == 3" filled v-model="num_tiquete" ref="scan" v-on:keyup.enter="buscarLineasCodigoBarras" label="Escanear..."  />
                <q-input v-if="empresa.tipo_escaner == 4" filled v-model="num_tiquete" ref="scan" v-on:keyup.enter="buscarLineasTiqueteEpelsa" label="Escanear..."  />
                <q-input v-if="empresa.tipo_escaner == 5" filled v-model="num_tiquete" ref="scan" v-on:keyup.enter="buscarLineasDespacho" label="Escanear..." />
              </div>
              <div  v-if="tipoDoc.naturaleza === '1' || tipoDoc.naturaleza === '4'" class="col-6">
                <q-input filled v-model="orden" ref="scan" v-on:keyup.enter="buscarLineasOrden" label="Orden..." />
              </div>
              <div  v-if="empresa.fact_grupo" class="col-12">
                <q-select
                  class="w-100"
                  v-model="storeItems.prod_grupo_id"
                  use-input
                  hide-selected
                  fill-input
                  option-value="id"
                  option-label="nombre"
                  label="Grupo"
                  option-disable="inactive"
                  map-options
                  input-debounce="0"
                  :options="options.grupos"
                  @filter="filterGrupos"
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
              <!-- Fin datos solo para facturas -->
              <!-- // -->
            </div>
            <div v-if="tipoDoc.naturaleza === '4'" class="row col-12 q-col-gutter-sm q-mt-sm">
                <div class="col-6 box-resumen-pagos">
                  <p>Resumen de pagos</p>
                    <div v-for="pago in pagos" :key="pago.id" class="row">
                      <div v-if="pago.valor != 0" class="col-6">
                        <span> {{ pago.nombre }} :</span>
                      </div>
                      <div v-if="pago.valor != 0" class="col-6 text-right">
                        <span> ${{ parseInt(pago.valor).toLocaleString('de-DE') }} || <span>
                          <q-btn class="q-mt-sm boton-delete"
                            round
                            color="negative"
                            size="xs"
                            icon="clear"
                            @click="deletePago(pago.id)"
                          />
                          </span>
                        </span>
                      </div>
                    </div>
                </div>
                <div class="col-6 text-center">
                  <q-btn class="btn-limon w-100" icon-right="attach_money" v-on:click="openedAddPago = true" label="PAGO" />
                </div>
            </div>
            <div class="row col-12 box-resumen-movimiento q-col-gutter-sm q-mt-md">
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
                  <div class="col-12" v-if="tipoDoc.naturaleza === '4'">
                    <span style="font-size:28px">Pago: $ {{ totalAbono | toMoney }}</span>
                  </div>
                  <div :class="classDevolucion" v-if="tipoDoc.naturaleza === '4'" class="col-12">
                    <span style="font-size:28px"> Cambio: $ {{ totalAbono - total | toMoney }} </span>
                  </div>
              </div>
              <div class="col-6">
                <div v-if="tipoDoc.naturaleza != 0" class="col-12">
                  <AddProductoManual
                    withPrice=1
                    withSubstrac=0
                    :listadoPrecios="listadoPrecios"
                    @addProducto='addProductFromComponent'
                  />
                </div>
                <div class="col-12 w-100 q-mt-sm">
                  <q-btn  v-if="tipoDoc.naturaleza != 4 && updateMode == false" class="btn-azul w-100" v-on:click="globalValidate('guardar')" label="Guardar" />
                  <q-btn  v-if="tipoDoc.naturaleza == 1 && updateMode == true" class="btn-coral w-100" v-on:click="globalValidate('guardar-edicion', movActualId)" label="Guardar Edicion" />
                  <q-btn  v-if="tipoDoc.naturaleza == 4 && updateMode == false" class="btn-azul w-100" v-on:click="localValidate()" label="Guardar" />
                </div>
                <div v-if="tipoDoc.naturaleza == 4" class="col-12 text-center q-mt-md">
                    <q-toggle v-model="valueToggle" label="AutoImpresión"/>
                </div>
              </div>
            </div>
            <!-- inicio tabla con calculos -->
        </div>
        <div class="col-12 col-md-8">
            <div class="col-12">
              <div class="row">
                <h4 style="margin: 0px">Lista de productos - N° Lineas: {{ numLineas }}</h4>
                <div class="row col-12 q-mt-md">
                  <q-table
                    v-if="verTabla"
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
                        <q-td key="id" :props="props"><q-checkbox v-if="tipoDoc.naturaleza != 0" v-model="selected" :val="props.row.id" /></q-td>
                        <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                        <q-td key="peso" :props="props">{{ parseFloat(props.row.cantidad).toFixed(3) }}</q-td>
                        <q-td key="precio" :props="props">{{ Math.round(props.row.precio) | toMoney }}</q-td>
                        <q-td key="descporcentaje" :props="props">
                          {{ props.row.descporcentaje }}
                          <q-popup-edit v-if="tipoDoc.naturaleza != 0" v-model="props.row.descporcentaje" @hide="setDescuento(props.row.id)" title="Descuento (%)" buttons>
                            <q-input type="number" v-model="props.row.descporcentaje" dense autofocus />
                          </q-popup-edit>
                        </q-td>
                        <q-td key="precioDescuento" :props="props">{{ (props.row.precio - props.row.desc) | toMoney  }}</q-td>
                        <q-td key="iva" :props="props">
                          {{ props.row.iva }}
                          <q-popup-edit v-if="tipoDoc.naturaleza != 0" v-model="props.row.iva" title="iva" buttons>
                            <q-input type="number" v-model="props.row.iva" dense autofocus />
                          </q-popup-edit>
                        </q-td>
                        <q-td key="ivapesosunit" :props="props">{{ ivaUnitRow(props.row) | toMoney }}</q-td>
                        <q-td key="preciototalunit" :props="props">
                          {{ Math.round(props.row.precio - props.row.desc + ivaUnitRow(props.row)) | toMoney }}
                          <q-popup-edit v-if="tipoDoc.naturaleza != 0" v-model="temp.precioLinea" @hide="setPrecio(props.row.id)" title="Precio Total Unit." buttons>
                            <money v-model="temp.precioLinea" v-bind="money" class="v-money"></money>
                          </q-popup-edit>
                        </q-td>
                        <q-td key="subtotal" :props="props">{{ Math.round(( Math.round(props.row.precio)) * props.row.cantidad) | toMoney }}</q-td>
                        <q-td key="desc" :props="props">
                          {{ props.row.desc | toMoney }}
                          <q-popup-edit v-if="tipoDoc.naturaleza != 0" v-model="props.row.desc" @hide="setPorcentaje(props.row.id)" title="Descuento ($)" buttons>
                            <money v-model="props.row.desc" v-bind="money" class="v-money"></money>
                          </q-popup-edit>
                        </q-td>
                        <q-td key="ivatotal" :props="props">{{ ivaUnitRow(props.row) * props.row.cantidad | toMoney }}</q-td>
                        <q-td key="total" :props="props">{{ Math.round((( Math.round(props.row.precio) - props.row.desc + ivaUnitRow(props.row)) * props.row.cantidad)).toLocaleString('de-DE') }}</q-td>
                      </q-tr>
                    </template>
                  </q-table>
                  <div v-if="tipoDoc.naturaleza != 0" class="col-12 q-mt-md text-center">
                    <q-btn class="btn-coral" style="width:50%" v-on:click="eliminarSelected()" label="Eliminar Seleccionados" />
                  </div>
                </div>
                <div class="col-12 q-mt-md">
                  <div style="width:100%">
                    <q-input
                      ref="observaciones"
                      v-model="storeItems.nota"
                      type="textarea"
                      label="Observaciones del ajuste"
                    />
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
import AddProductoManual from 'components/productos/addProductoManual'
import NavMovComponent from 'components/facturacion/navegador.vue'
import { Money } from 'v-money'
// import { parse } from 'querystring'
// import { parse } from 'path'
const axios = require('axios')

export default {
  name: 'CreateMovimiento',
  params: 'doc',
  components: {
    SelectTerceroSucursal,
    Money,
    AddProductoManual,
    NavMovComponent
  },
  data: function () {
    return {
      valueToggle: true,
      money: {
        decimal: ',',
        thousands: '.',
        prefix: '          $ ',
        suffix: '',
        precision: 0,
        masked: false
      },
      urlAPI: 'api/facturacion/movimientos',
      tipoDoc: {},
      storeItems: {
      },
      datos: {
        despacho: null
      },
      dataNotas: [],
      orden: null,
      movActualId: null,
      num_tiquete: null,
      arrayImpuestos: [],
      consecToGo: null,
      selected: [],
      callback: [],
      numLineas: 0,
      descuentoGnal: 0,
      despachos: [],
      empresa: [],
      movimientos: [],
      tiposDoc: [],
      descripImpuestos: [],
      listadoPrecios: [],
      productosImpuestos: [],
      sendingCheck: 0,
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
      updateMode: false,
      options: {
        despachos: this.despachos,
        tiposDoc: this.tiposDoc,
        movimientos: this.movimientos,
        productos: this.productos,
        vendedores: this.vendedores,
        formasPago: this.formasPago,
        grupos: this.grupos
      },
      verTabla: true,
      interval: null,
      temp: {
        cantidad: null,
        precioLinea: 0,
        cantidad_unid: null
      },
      sucursal: null,
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 30px' },
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Cantidad', align: 'right', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'precio', required: true, label: 'Precio', align: 'right', field: 'precio', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'descporcentaje', required: true, label: 'Descuento (%)', align: 'right', field: 'descporcentaje', sortable: true, classes: 'my-class', style: 'width: 100px' },
        // { name: 'precioDescuento', required: true, label: 'Precio Con Descuento', align: 'right', field: 'precioDescuento', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'iva', required: true, label: 'IVA (%)', align: 'right', field: 'iva', sortable: true, classes: 'my-class', style: 'width: 100px' },
        // { name: 'ivapesosunit', required: true, label: 'IVA ($) unit', align: 'right', field: 'ivapesosunit', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'preciototalunit', required: true, label: 'Precio Tot Unit', align: 'right', field: 'preciototalunit', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'subtotal', required: true, label: 'Subtotal', align: 'right', field: 'subtotal', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'desc', required: true, label: 'Descuento', align: 'right', field: 'desc', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'ivatotal', required: true, label: 'IVA total', align: 'right', field: 'ivatotal', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'total', required: true, label: 'Total', align: 'right', field: 'total', sortable: true, classes: 'my-class', style: 'width: 100px' }
      ],
      grupos: [],
      openedPrintFactura: false,
      itemsCounter: 0,
      dataResumen: [],
      pagination: {
        rowsPerPage: 0
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave (callback = null) {
      this.$router.push({ name: 'movimientos', params: { id: this.$route.params.id, consecmov: 'nuevo' } })
      this.$q.loading.hide()
      this.updateMode = false
      this.movActualId = null
      this.openedAddPago = false
      this.dataResumen = []
      this.descuentoGnal = 0
      this.datos.despacho = null
      this.sucursal = null
      this.datos.tercero_id = null
      this.pagos = []
      if (callback !== null && this.tipoDoc.naturaleza !== '4') {
        this.itemsCounter = 1
        this.callback = callback
        this.openedPrintFactura = true
      }
      if (this.tipoDoc.naturaleza === '4') {
        if (this.valueToggle) {
          this.printPOS(callback[1], 0)
        }
        this.getTerceroPOS()
        this.prepareFormaspago()
      }
      this.storeItems = {}
      this.sendingCheck = 0
      this.numLineas = 0
      var today = new Date()
      var dd = String(today.getDate()).padStart(2, '0')
      var mm = String(today.getMonth() + 1).padStart(2, '0')
      var yyyy = today.getFullYear()
      today = yyyy + '/' + mm + '/' + dd
      this.storeItems.fecha_facturacion = today
      this.storeItems.fecha_vencimiento = today
      this.$refs.scan.focus()
    },
    preSave () {
      if (this.tipoDoc.naturaleza === '1' || this.tipoDoc.naturaleza === '4') {
        this.storeItems.lineas = this.dataResumen
        this.storeItems.cliente_id = this.sucursal
        if (this.storeItems.gen_vendedor_id) {
          this.storeItems.gen_vendedor_id = this.storeItems.gen_vendedor_id.id
        }
        if (this.storeItems.prod_grupo_id) {
          this.storeItems.prod_grupo_id = this.storeItems.prod_grupo_id.id
        }
        this.storeItems.total = parseInt(this.total)
        this.storeItems.subtotal = parseInt(this.subtotal)
        this.storeItems.descuento = parseInt(this.descuento)
        this.storeItems.ivatotal = parseInt(this.ivatotal)
        this.storeItems.fac_tipo_doc_id = this.tipoDoc.id
        if (this.tipoDoc.naturaleza === '4') {
          this.storeItems.pagos = this.pagos
          this.storeItems.devuelta = parseInt(this.totalAbono - this.total)
        }
      } else {
        this.storeItems.lineas = this.dataResumen
        this.storeItems.total = parseInt(this.total)
        this.storeItems.subtotal = parseInt(this.subtotal)
        this.storeItems.descuento = parseInt(this.descuento)
        this.storeItems.ivatotal = parseInt(this.ivatotal)
        this.storeItems.fac_tipo_doc_id = this.tipoDoc.id
      }
    },
    postEdit () {
    },
    filterVendedores (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.vendedores = this.vendedores.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterDespachos (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.despachos = this.despachos.filter(v => v.id.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterMovimientos (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.movimientos = this.movimientos.filter(v => v.consecutivo.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterTiposDoc (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.tiposDoc = this.tiposDoc.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterFormasPago (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.formasPago = this.formasPago.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterGrupos (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.grupos = this.grupos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    getPeso () {
      var v = this
      this.interval = setInterval(function () {
        axios.get('http://127.0.0.1:5002/basculas').then(
          function (response) {
            v.temp.cantidad = response.data.substr(7, 7)
          }
        )
      }, 1000)
    },
    stopGetPeso () {
      clearInterval(this.interval)
    },
    getPesoData () {
      var app = this
      axios.get('http://127.0.0.1:5002/basculas').then(
        function (response) {
          app.temp.cantidad = response.data.substr(7, 7)
        }
      )
    },
    openedAddProductoMethod () {
      this.openedAddProducto = true
      this.getPeso()
    },
    closeAddProducto () {
      this.producto_selected = null
      this.temp.cantidad = null
      this.precio_producto = parseInt(0)
      this.stopGetPeso()
      this.openedAddProducto = false
    },
    addProductFromComponent (newProduct) {
      // let item = this.dataResumen.find(v => v.producto_codigo === newProduct.producto_codigo)
      // if (item !== undefined) {
      //   item.cantidad = parseFloat(item.cantidad) + parseFloat(newProduct.cantidad)
      //   if (item.cantidad < 1) {
      //     var index = this.dataResumen.findIndex(v => v.producto_codigo === newProduct.producto_codigo)
      //     this.dataResumen.splice(index, 1)
      //   }
      // } else {
      //   this.dataResumen.push(newProduct)
      // }
      this.dataResumen.push(newProduct)
      this.numLineas += 1
    },
    ivaUnitRow (row) {
      return ((Math.round(row.precio) - row.desc) * (row.iva / 100))
    },
    localValidate () {
      if (parseInt(this.totalAbono) < parseInt(this.total)) {
        this.$q.notify({ color: 'negative', message: 'El pago debe ser mayor a la venta.' })
      } else if (parseInt(this.total) < 1) {
        this.$q.notify({ color: 'negative', message: 'No se han agregado productos.' })
      } else {
        if (this.sendingCheck === 0) {
          this.openedAddPago = false
          if (this.updateMode === true) {
            this.globalValidate('guardar-edicion', this.movActualId)
          } else {
            this.globalValidate('guardar')
          }
          this.sendingCheck = 1
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
      this.dataResumen[index].descporcentaje = 0
      this.temp.precioLinea = 0
    },
    setDescuento (v) {
      this.verTabla = false
      var index
      this.dataResumen.forEach(function (element, i) {
        if (v === element.id) {
          index = i
        }
      })
      this.dataResumen[index].desc = ((this.dataResumen[index].precio) * (this.dataResumen[index].descporcentaje / 100)).toFixed(2)
      this.verTabla = true
      // console.log(this.dataResumen[index])
    },
    eliminarSelected () {
      var app = this
      this.selected.forEach(function (elementSelected, j) {
        app.numLineas = app.numLineas - 1
        app.dataResumen.forEach(function (element, i) {
          if (elementSelected === element.id) {
            app.dataResumen.splice(i, 1)
          }
        })
      })
      this.selected = []
    },
    selectMovimiento () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/movimientositems/filtro/pormovimiento/' + this.storeItems.docReferencia.id).then(
        function (response) {
          response.data.forEach(function (element, i) {
            var newProduct = {
              id: i,
              producto: element.producto,
              producto_id: element.producto_id,
              cantidad: parseFloat(element.cantidad),
              precio: parseInt(element.precio),
              iva: parseInt(element.iva),
              desc: ((parseInt(element.precio)) * (parseInt(element.descporcentaje) / 100)),
              descporcentaje: parseInt(element.descporcentaje)
            }
            app.dataResumen.push(newProduct)
            app.itemsCounter = app.itemsCounter + 1
          })
        }
      )
    },
    buscarLineasTiqueteDibal () {
      this.$q.loading.show()
      var app = this
      var tiqueteLeido = false
      app.num_tiquete = parseInt(app.num_tiquete.substr(0, 11))
      var tiquetesLeidos = []
      if (app.dataResumen.length !== 0) {
        tiquetesLeidos = app.dataResumen.filter(v => parseInt(v.num_tiquete) === parseInt(app.num_tiquete))
        if (tiquetesLeidos.length > 0) {
          tiqueteLeido = true
        }
      }
      if (!tiqueteLeido) {
        axios.get(app.$store.state.jhsoft.url + 'api/facturacion/readtiquetedibal/' + app.num_tiquete).then(
          function (response) {
            if (response.data.length > 0) {
              var vendedor = null
              response.data.forEach(function (element, j) {
                const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element[0]))
                if (productoImpuesto !== undefined) {
                  if (parseInt(element[1]) > 50) {
                    element[1] = element[1] / 1000
                  }
                  var newProduct = {
                    id: app.itemsCounter,
                    producto: productoImpuesto.nombre,
                    producto_id: productoImpuesto.id,
                    producto_codigo: productoImpuesto.codigo,
                    cantidad: element[1],
                    precio: parseInt(element[2] / element[1]) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
                    iva: productoImpuesto.impuesto,
                    gen_iva_id: productoImpuesto.gen_iva_id,
                    desc: 0.00,
                    descporcentaje: 0.00,
                    despacho: false,
                    num_tiquete: element[3],
                    num_linea_tiquete: element[4]
                  }
                  app.dataResumen.push(newProduct)
                  vendedor = element[5]
                  app.itemsCounter = app.itemsCounter + 1
                  app.numLineas = app.numLineas + 1
                } else {
                  app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element[0]) + ' no esta creado.' })
                }
              })
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
              if (app.storeItems.gen_vendedor_id === undefined) {
                app.$q.notify({ color: 'negative', message: 'Error vendedor con codigo ' + vendedor + ' no existe, se cargara el vendedor por defecto.' })
                app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
              }
            } else {
              app.$q.notify({ color: 'negative', message: 'Error al leer el tiquete  o todos los elementos ya fueron facturados.' })
            }
            app.num_tiquete = null
            app.$q.loading.hide()
          }
        )
      } else {
        app.$q.notify({ color: 'negative', message: 'El tiquete ya esta cargado.' })
        app.num_tiquete = null
        app.$q.loading.hide()
      }
    },
    buscarLineasDespacho () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/readdespacho/' + app.num_tiquete).then(
        function (response) {
          if (response.data[0].length > 0) {
            app.sucursal = response.data[1]
            var vendedor = null
            response.data[0].forEach(function (element, j) {
              const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element.codigo))
              if (productoImpuesto !== undefined) {
                var newProduct = {
                  id: app.itemsCounter,
                  producto: productoImpuesto.nombre,
                  producto_id: productoImpuesto.id,
                  producto_codigo: productoImpuesto.codigo,
                  cantidad: element.peso,
                  precio: parseInt(element.precio) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
                  iva: productoImpuesto.impuesto,
                  gen_iva_id: productoImpuesto.gen_iva_id,
                  desc: 0.00,
                  descporcentaje: 0.00,
                  despacho: false,
                  num_tiquete: app.num_tiquete,
                  num_linea_tiquete: 0
                }
                app.dataResumen.push(newProduct)
                vendedor = 1
                app.itemsCounter = app.itemsCounter + 1
                app.numLineas = app.numLineas + 1
              } else {
                app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element.codigo) + ' no esta creado.' })
              }
            })
            app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
            if (app.storeItems.gen_vendedor_id === undefined) {
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
            }
          } else {
            app.$q.notify({ color: 'negative', message: 'Error al leer el despac.' })
          }
          app.num_tiquete = null
          app.$q.loading.hide()
        }
      )
    },
    buscarLineasOrden () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/ordenes/readordenfactura/' + app.orden + '/' + app.tipoDoc.id).then(
        function (response) {
          if (response.data.length > 0) {
            var vendedor = null
            response.data.forEach(function (element, j) {
              const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.id) === parseInt(element.producto_id))
              if (productoImpuesto !== undefined) {
                var newProduct = {
                  id: app.itemsCounter,
                  producto: productoImpuesto.nombre,
                  producto_id: productoImpuesto.id,
                  producto_codigo: productoImpuesto.codigo,
                  cantidad: element.cantidad,
                  precio: element.precio,
                  iva: element.iva,
                  gen_iva_id: productoImpuesto.gen_iva_id,
                  desc: 0.00,
                  descporcentaje: 0.00,
                  despacho: false,
                  num_tiquete: app.num_tiquete,
                  num_linea_tiquete: 0
                }
                app.dataResumen.push(newProduct)
                vendedor = 1
                app.itemsCounter = app.itemsCounter + 1
                app.numLineas = app.numLineas + 1
              } else {
                app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element.codigo) + ' no esta creado.' })
              }
            })
            app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
            if (app.storeItems.gen_vendedor_id === undefined) {
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
            }
          } else {
            app.$q.notify({ color: 'negative', message: 'Error al leer la orden.' })
          }
          app.num_tiquete = null
          app.$q.loading.hide()
        }
      )
      app.orden = null
    },
    buscarLineasCodigoBarras () {
      this.$q.loading.show()
      var app = this
      const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.ean13) === parseInt(app.num_tiquete))
      if (productoImpuesto !== undefined) {
        var valExist = app.dataResumen.find(v => parseInt(v.producto_id) === parseInt(productoImpuesto.id))
        if (valExist !== undefined) {
          valExist.cantidad = valExist.cantidad + 1
        } else {
          const objectPrecio = this.listadoPrecios.find(v => parseInt(v.producto_id) === parseInt(productoImpuesto.id))
          var newProduct = {
            id: app.itemsCounter,
            producto: productoImpuesto.nombre,
            producto_id: productoImpuesto.id,
            producto_codigo: productoImpuesto.codigo,
            cantidad: 1,
            precio: objectPrecio.precio,
            iva: productoImpuesto.impuesto,
            gen_iva_id: productoImpuesto.gen_iva_id,
            desc: 0.00,
            descporcentaje: 0.00,
            despacho: false
          }
          app.dataResumen.push(newProduct)
          app.itemsCounter = app.itemsCounter + 1
          app.numLineas = app.numLineas + 1
        }
      } else {
        app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(app.num_tiquete) + ' no esta creado.' })
      }
      app.num_tiquete = null
      this.$q.loading.hide()
    },
    buscarLineasTiqueteEpelsa () {
      this.$q.loading.show()
      var app = this
      var tiqueteLeido = false
      app.num_tiquete = parseInt(app.num_tiquete.substr(0, 11))
      var tiquetesLeidos = []
      if (app.dataResumen.length !== 0) {
        tiquetesLeidos = app.dataResumen.filter(v => parseInt(v.num_tiquete) === parseInt(app.num_tiquete))
        if (tiquetesLeidos.length > 0) {
          tiqueteLeido = true
        }
      }
      if (!tiqueteLeido) {
        axios.get(app.$store.state.jhsoft.url + 'api/facturacion/readtiqueteepelsa/' + app.num_tiquete).then(
          function (response) {
            if (response.data.length > 0) {
              var vendedor = null
              response.data.forEach(function (element, j) {
                const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element[0]))
                if (productoImpuesto !== undefined) {
                  if (parseInt(element[1]) > 50) {
                    element[1] = element[1] / 1000
                  }
                  var newProduct = {
                    id: app.itemsCounter,
                    producto: productoImpuesto.nombre,
                    producto_id: productoImpuesto.id,
                    producto_codigo: productoImpuesto.codigo,
                    cantidad: element[1],
                    precio: parseInt(element[2] / element[1]) / (1 + (parseInt(productoImpuesto.impuesto) / 100)),
                    iva: productoImpuesto.impuesto,
                    gen_iva_id: productoImpuesto.gen_iva_id,
                    desc: 0.00,
                    descporcentaje: 0.00,
                    despacho: false,
                    num_tiquete: element[3],
                    num_linea_tiquete: element[4]
                  }
                  app.dataResumen.push(newProduct)
                  vendedor = element[5]
                  app.itemsCounter = app.itemsCounter + 1
                  app.numLineas = app.numLineas + 1
                } else {
                  app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element[0]) + ' no esta creado.' })
                }
              })
              app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
              if (app.storeItems.gen_vendedor_id === undefined) {
                app.$q.notify({ color: 'negative', message: 'Error vendedor con codigo ' + vendedor + ' no existe, se cargara el vendedor por defecto.' })
                app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
              }
            } else {
              app.$q.notify({ color: 'negative', message: 'Error al leer el tiquete.' })
            }
            app.num_tiquete = null
            app.$q.loading.hide()
          }
        )
      } else {
        app.$q.notify({ color: 'negative', message: 'El tiquete ya esta cargado.' })
        app.num_tiquete = null
        app.$q.loading.hide()
      }
    },
    buscarLineasTiqueteMarques () {
      this.$q.loading.show()
      var app = this
      var tiqueteLeido = false
      var tiquetesLeidos = []
      if (app.dataResumen.length !== 0) {
        tiquetesLeidos = app.dataResumen.filter(v => parseInt(v.num_tiquete) === parseInt(app.num_tiquete.substr(6, 6)))
        if (tiquetesLeidos.length > 0) {
          tiqueteLeido = true
        }
      }
      if (!tiqueteLeido) {
        axios.get(app.$store.state.jhsoft.url + 'api/facturacion/movimientos/verificartiquetemarques/' + parseInt(app.num_tiquete.substr(6, 6)) + '/' + parseInt(app.num_tiquete.substr(4, 2)) + '/' + app.diaHoy).then(
          function (response) {
            if (response.data.length > 0) {
              app.$q.notify({ color: 'negative', message: 'El tiquete ya fue facturado.' })
              app.num_tiquete = null
              app.$q.loading.hide()
            } else {
              var ipMarques = null
              let ipMarquesArray = app.empresa.ruta_ip_marques.split('&')
              ipMarquesArray.forEach(function (element, j) {
                let itemMarques = element.split('-')
                if (parseInt(itemMarques[1]) === parseInt(app.num_tiquete.substr(4, 2))) {
                  ipMarques = itemMarques[0]
                }
              })
              if (ipMarques !== null) {
                axios.get('http://' + ipMarques + '/year/documentos?seek={"tipo_doc":1,"posto":' + parseInt(app.num_tiquete.substr(4, 2)) + ',"numero":' + parseInt(app.num_tiquete.substr(6, 6)) + '}&limit=1').then(
                  function (response) {
                    let cantLineas = response.data[0]['nr_parcelas']
                    axios.get('http://' + ipMarques + '/year/documentos_lnh?seek={"tipo_doc":1,"posto":' + parseInt(app.num_tiquete.substr(4, 2)) + ',"numero":' + parseInt(app.num_tiquete.substr(6, 6)) + ',"linha_f":0}&limit=' + cantLineas).then(
                      function (response) {
                        if (response.data.length > 0) {
                          var vendedor = null
                          response.data.forEach(function (element, j) {
                            // console.log(element.numero)
                            if (parseInt(element.numero) === parseInt(app.num_tiquete.substr(6, 6))) {
                              const productoImpuesto = app.productosImpuestos.find(v => parseInt(v.codigo) === parseInt(element.codigo))
                              if (productoImpuesto !== undefined) {
                                var newProduct = {
                                  id: app.itemsCounter,
                                  producto: productoImpuesto.nombre,
                                  producto_id: productoImpuesto.id,
                                  producto_codigo: productoImpuesto.codigo,
                                  cantidad: element.quantidade,
                                  precio: parseInt(element.preco_unit / (1 + (parseInt(productoImpuesto.impuesto) / 100))),
                                  iva: productoImpuesto.impuesto,
                                  gen_iva_id: productoImpuesto.gen_iva_id,
                                  desc: 0.00,
                                  descporcentaje: 0.00,
                                  despacho: false,
                                  num_tiquete: element.numero,
                                  puesto_tiquete: element.posto,
                                  num_linea_tiquete: element.linha_f
                                }
                                app.dataResumen.push(newProduct)
                                vendedor = 1
                                app.itemsCounter = app.itemsCounter + 1
                                app.numLineas = app.numLineas + 1
                              } else {
                                app.$q.notify({ color: 'negative', message: 'El codigo ' + parseInt(element.codigo) + ' no esta creado.' })
                              }
                            }
                          })
                          app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(vendedor))
                          if (app.storeItems.gen_vendedor_id === undefined) {
                            app.$q.notify({ color: 'negative', message: 'Error vendedor con codigo ' + vendedor + ' no existe, se cargara el vendedor por defecto.' })
                            app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(0))
                          }
                        } else {
                          app.$q.notify({ color: 'negative', message: 'Error al leer el tiquete ó verifique la conexion a las basculas.' })
                        }
                        app.num_tiquete = null
                        app.$q.loading.hide()
                      }
                    )
                  }
                )
              } else {
                app.$q.notify({ color: 'negative', message: 'Bascula ' + parseInt(app.num_tiquete.substr(4, 2)) + ' no configurada.' })
              }
            }
          }
        )
      }
    },
    verificarTipoDoc () {
      this.$q.loading.show()
      this.sucursal = null
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/facturacion/tipos/' + this.$route.params.id).then(
        function (response) {
          app.tipoDoc = response.data
          app.dataResumen = []
          app.pagos = []
          if (app.$route.params.consecmov === 'nuevo') {
            app.storeItems = {}
            app.fechasHoy()
            app.updateMode = false
            app.movActualId = null
            app.numLineas = 0
            if (response.data.naturaleza === '0') {
              axios.get(app.$store.state.jhsoft.url + 'api/facturacion/movrelacionado/' + response.data.id + '/items').then(
                function (response1) {
                  app.movimientos = response1.data
                  app.viewTerceros = false
                }
              )
            } else if (response.data.naturaleza === '4') {
              app.getTerceroPOS()
              app.prepareFormaspago()
              app.viewTerceros = true
            } else {
              app.viewTerceros = true
            }
          } else {
            app.numLineas = 0
            app.updateMode = true
            app.dataResumen = []
            app.movActualId = app.$route.params.consecmov
          }
          app.$q.loading.hide()
        }
      )
    },
    deletePago (id) {
      var index
      this.pagos.forEach(function (element, i) {
        if (id === element.id) {
          index = i
        }
      })
      this.pagos[index].valor = 0
    },
    getTerceroPOS () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/generales/empresa/filter/terceropos').then(
        function (response2) {
          app.sucursal = response2.data
        }
      )
    },
    prepareFormaspago () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/formaspago').then(
        function (responsePago) {
          responsePago.data.forEach(function (element, i) {
            element.valor = 0
            app.pagos.push(element)
          })
        }
      )
    },
    printPOS (id, copia) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/imprimirpos/' + id + '/' + copia).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      ).catch(function (error) {
        app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
        console.log(error)
      })
    },
    insertLineFromNav (item) {
      var app = this
      app.dataResumen.push(item)
      app.itemsCounter = app.itemsCounter + 1
      app.numLineas = app.numLineas + 1
    },
    setFactDataFromNav (item) {
      var app = this
      app.movActualId = item.mov.id
      app.sucursal = item.sucursal
      app.storeItems.fecha_facturacion = item.mov.fecha_facturacion
      app.storeItems.fecha_vencimiento = item.mov.fecha_vencimiento
      if (item.mov.nota !== null) {
        app.storeItems.nota = item.mov.nota
      } else {
        delete app.storeItems.nota
      }
      if (item.mov.prod_grupo_id !== null) {
        app.storeItems.prod_grupo_id = app.grupos.find(v => parseInt(v.id) === parseInt(item.mov.prod_grupo_id))
      } else {
        delete app.storeItems.prod_grupo_id
      }
      app.pagos = item.pagos
      app.storeItems.gen_vendedor_id = app.vendedores.find(v => parseInt(v.codigo_unico) === parseInt(item.vendedor))
    },
    fechasHoy () {
      var today = new Date()
      var dd = String(today.getDate()).padStart(2, '0')
      var mm = String(today.getMonth() + 1).padStart(2, '0')
      var yyyy = today.getFullYear()
      this.storeItems.fecha_facturacion = yyyy + '/' + mm + '/' + dd
      var today2 = new Date()
      today2.setDate(today2.getDate() + 15)
      dd = String(today2.getDate()).padStart(2, '0')
      mm = String(today2.getMonth() + 1).padStart(2, '0')
      yyyy = today2.getFullYear()
      this.storeItems.fecha_vencimiento = yyyy + '/' + mm + '/' + dd
    }
  },
  created: function () {
    this.globalGetForSelect('api/facturacion/formaspago', 'formasPago')
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
    this.globalGetForSelect('api/generales/vendedores', 'vendedores')
    this.globalGetForSelect('api/generales/empresa', 'empresa')
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productosImpuestos')
    this.fechasHoy()
  },
  computed: {
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
    totalAbono: function () {
      var response = 0
      this.pagos.forEach(function (element, i) {
        response = parseInt(element.valor) + response
      })
      return parseInt(response)
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
          if (app.tipoDoc.naturaleza === '2' || app.tipoDoc.naturaleza === '3') {
            axios.get(this.$store.state.jhsoft.url + 'api/facturacion/movimientos/filtro/facturaspendientesparanotas/' + this.sucursal + '/' + app.tipoDoc.id).then(
              function (response) {
                app.movimientos = response.data
              }
            )
          }
        }
      }
    },
    $route: {
      deep: true,
      handler () {
        this.verificarTipoDoc()
      }
    }
  },
  filters: {
    toMoney: function (value) {
      return parseInt(value).toLocaleString('de-DE')
    }
  },
  mounted () {
    this.verificarTipoDoc()
    var app = this
    if (app.$route.params.consecmov !== 'nuevo') {
      app.verTabla = false
      app.dataResumen = []
      app.numLineas = 0
      app.updateMode = true
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/editarfactura/' + app.$route.params.id + '/' + app.$route.params.consecmov).then(
        function (response2) {
          if (response2.data.lineas) {
            app.movActualId = response2.data.mov.id
            // cargar productos
            response2.data.lineas.forEach(function (element, j) {
              var newProduct = {
                id: app.itemsCounter,
                producto: element.producto_nombre,
                producto_id: element.producto_id,
                producto_codigo: element.producto_codigo,
                cantidad: element.cantidad,
                precio: element.precio,
                iva: element.impuesto,
                gen_iva_id: element.gen_iva_id,
                desc: ((element.precio) * (element.descporcentaje / 100)).toFixed(2),
                descporcentaje: element.descporcentaje,
                num_tiquete: element.num_tiquete,
                num_linea_tiquete: element.num_linea_tiquete,
                puesto_tiquete: element.puesto_tiquete
              }
              app.insertLineFromNav(newProduct)
            })
            app.movActualId = response2.data.mov.id
            // cargar tercero
            app.setFactDataFromNav(response2.data)
          } else {
            app.$router.push({ name: 'movimientos', params: { id: app.$route.params.id, consecmov: 'nuevo' } })
            app.$q.notify({ color: 'negative', message: response2.data })
          }
          app.verTabla = true
        }
      )
    }
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
    .input-consec .q-field__control{
      height: 40px!important;
    }
</style>
