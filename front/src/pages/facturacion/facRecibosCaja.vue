<template>
  <div>
    <q-page padding>
        <div class="div-86">
          <q-dialog v-model="openedPrintRecibo" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
              <q-header class="bg-primary">
                <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <h3>Se ha guardado la {{ tipoRecCaja.nombre }} N° {{ callback[0] }}</h3>
                    </div>
                    <div class="row text-center">
                      <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/recibocaja/imprimir/'+ callback[1]+'?token='+ $store.state.jhsoft.token "><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn> </a>
                      <q-btn class="q-ml-xs" color="primary" @click="printPOS(callback[1])"> POS </q-btn>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>

          <q-dialog v-model="openedAddPago" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
              <q-header class="bg-primary">
                  <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                  </q-toolbar>
              </q-header>

              <q-page-container>
                  <q-page padding>
                  <h3>Agregar Pago - $ {{totalSeleccionado.toLocaleString('de-DE')}} </h3>
                  <div class="overflow-hidden">
                      <div class="row q-col-gutter-sm">
                          <div class="col-6">
                              <q-select
                                  class="w-100"
                                  v-model="temp.formaPago"
                                  use-input
                                  hide-selected
                                  fill-input
                                  option-value="id"
                                  option-label="nombre"
                                  label="Forma de pago"
                                  option-disable="inactive"
                                  map-options
                                  input-debounce="0"
                                  :options="options.formasPago"
                                  @filter="filterFormasPago"
                              >
                              </q-select>
                          </div>
                          <div class="col-6" style="position:relative">
                              <p class="v-money-label"> Valor: </p>
                              <money v-model="temp.valor" v-bind="money" class="v-money"></money>
                          </div>
                      </div>
                  </div>
                  <q-btn class="q-mt-sm"
                      color="primary"
                      v-close-popup
                      label="Guardar"
                      @click="addPago"
                  />
                  </q-page>
              </q-page-container>
              </q-layout>
          </q-dialog>

          <h4 style="margin: 15px 0px">{{ tipoRecCaja.nombre }}</h4>
          <div class="row q-col-gutter-md">
              <div class="col-8">
                  <SelectTerceroSucursal v-model="sucursal_id" :editor="sucursal_id" labelTercero='Cliente'/>
              </div>
              <div class="col-4">
                <q-input label="Fecha de Recibo" v-model="storeItems.fecha_recibo" class="date-field" mask="date" :rules="['date']" readonly="readonly">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                        <q-date v-model="storeItems.fecha_recibo" @input="() => $refs.qDateProxy.hide()" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
              </div>
          </div>
          <div class="row q-col-gutter-md q-mt-sm">
              <div class="col-6 box-resumen-abonos">
                <div class="col-12">
                  <q-input label="Total a abonar" stack-label type="text" readonly><span class="span-abono">{{ (parseInt(totalAbono) + parseInt(storeItems.ajuste)).toLocaleString('de-DE') }}</span></q-input>
                </div>
                <div class="col-12">
                  <q-input label="Total Seleccionado" stack-label type="text" readonly><span class="span-abono">{{ parseInt(totalSeleccionado).toLocaleString('de-DE') }}</span></q-input>
                </div>
                <div class="col-12">
                  <q-input label="Total" stack-label type="text" readonly><span class="span-abono">{{ ((parseInt(totalAbono) + parseInt(storeItems.ajuste)) - totalSeleccionado).toLocaleString('de-DE')  }}</span></q-input>
                </div>
              </div>
              <div class="col-6 row">
                <div class="col-6 box-resumen-pagos">
                  <p>Resumen de pagos</p>
                    <div v-for="pago in pagos" :key="pago.id" class="row">
                      <div v-if="pago.valor != 0" class="col-6">
                        <span> {{ pago.formapago_nombre }} :</span>
                      </div>
                      <div v-if="pago.valor != 0" class="col-6 text-right">
                        <span> ${{ parseInt(pago.valor).toLocaleString('de-DE') }} || <span>
                          <q-btn class="q-mt-sm boton-delete"
                            round
                            color="negative"
                            size="xs"
                            icon="clear"
                            @click="deletePago(pago.fac_forma_id)"
                          />
                          </span>
                        </span>
                      </div>
                    </div>
                </div>
                <div class="col-6 text-center">
                  <q-btn class="btn-limon" icon-right="attach_money" v-on:click="openedAddPago = true" label="PAGO" />
                </div>
              </div>
          </div>
          <div class="row q-col-gutter-md q-mt-md text-right">
            <div class="col-12">
              <div style="width:100%">
                <q-input
                  ref="observaciones"
                  v-model="storeItems.ajuste_observacion"
                  type="textarea"
                  label="Observaciones"
                />
              </div>
            </div>
            <div class="col-12">
              <q-btn class="btn-azul" v-on:click="validacion()" label="Guardar" />
            </div>
          </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
              :title="'Facturas Pendientes - Total: ' +  totalCliente.toLocaleString('de-DE') "
              :data="facturas"
              :columns="columns"
              row-key="id"
              selection="multiple"
              :selected.sync="selected"
              virtual-scroll
              :pagination.sync="pagination"
              :rows-per-page-options="[0]"
              table-header-class="bg-primary text-white"
            >
            <q-td slot="body-cell-total" slot-scope="props" :props="props">
                {{ props.value | toMoney }}
            </q-td>
            <q-td slot="body-cell-saldo" slot-scope="props" :props="props">
                {{  props.value | toMoney }}
            </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'
const axios = require('axios')
import { Money } from 'v-money'

export default {
  name: 'PageRecibosCaja',
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
      urlAPI: 'api/facturacion/reciboscaja',
      storeItems: {
        fecha_recibo: null,
        ajuste_observacion: 'Sin observaciones',
        ajuste: 0
      },
      formasPago: [],
      options: {
        formasPago: this.formasPago
      },
      datos: {
      },
      tipoRecCaja: {},
      facturas: [],
      temp: {
        valor: 0,
        formaPago: null
      },
      selected: [],
      pagos: [],
      callback: [],
      openedPrintRecibo: false,
      openedAddPago: false,
      showReferencia: false,
      showForUpdate: false,
      sucursal_id: null,
      tableData: [],
      columns: [
        { name: 'tipo', required: true, label: 'TIPO MOVIMIENTO', align: 'left', field: 'tipo', sortable: false, classes: 'my-class', style: 'width: 200px' },
        { name: 'consecutivo', required: true, label: 'CONSECUTIVO', align: 'left', field: 'consecutivo', sortable: false, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_facturacion', required: true, label: 'FECHA FACTURACIÓN', align: 'left', field: 'fecha_facturacion', sortable: false, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_vencimiento', required: true, label: 'FECHA VENCIMIENTO', align: 'left', field: 'fecha_vencimiento', sortable: false, classes: 'my-class', style: 'width: 200px' },
        { name: 'total', required: true, label: 'TOTALES', align: 'right', field: 'total', sortable: false, classes: 'my-class', style: 'width: 200px' },
        { name: 'saldo', required: true, label: 'SALDO', align: 'right', field: 'saldo', sortable: false, classes: 'my-class', style: 'width: 200px' }

      ],
      separator: 'horizontal',
      filter: '',
      pagination: {
        rowsPerPage: 0
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave (callback) {
      this.storeItems = {
        fecha_recibo: null,
        ajuste_observacion: 'Sin observaciones',
        ajuste: 0
      }
      this.facturas = []
      this.pagos = []
      this.selected = []
      this.callback = callback
      this.openedPrintRecibo = true
      this.sucursal_id = null
      var today = new Date()
      var dd = String(today.getDate()).padStart(2, '0')
      var mm = String(today.getMonth() + 1).padStart(2, '0')
      var yyyy = today.getFullYear()
      today = yyyy + '/' + mm + '/' + dd
      this.storeItems.fecha_recibo = today
    },
    preSave () {
      this.storeItems.pagos = this.pagos
      this.storeItems.facturas = this.selected
      this.storeItems.abono = this.totalAbono
      this.storeItems.total = parseInt(this.totalAbono) + parseInt(this.storeItems.ajuste)
      this.storeItems.fac_tipo_rec_caja_id = this.tipoRecCaja.id
      this.storeItems.tercero_sucursal_id = this.sucursal_id
    },
    postEdit () {
    },
    validacion () {
      if (this.totalAbono > 0 && this.totalSeleccionado > 0) {
        if (this.totalSeleccionado >= this.totalAbono) {
          this.globalValidate('guardar')
        } else {
          this.$q.notify({ color: 'negative', message: 'El valor abonado debe ser inferior o igual al seleccionado.' })
        }
      } else {
        this.$q.notify({ color: 'negative', message: 'El valor abonado y seleccionado deben ser diferentes de 0.' })
      }
    },
    addPago () {
      var verificacion = this.pagos.filter(v => v.formapago_id === this.temp.formaPago.id)
      if (verificacion.length > 0) {
        this.$q.notify({ color: 'negative', message: 'Tipo de pago duplicado, Elimine al anterior.' })
      } else {
        this.pagos.push({
          formapago_nombre: this.temp.formaPago.nombre,
          fac_formas_pago_id: this.temp.formaPago.id,
          valor: this.temp.valor
        })
      }
      this.temp.formaPago = null
      this.temp.valor = 0
    },
    deletePago (id) {
      var index
      this.pagos.forEach(function (element, i) {
        if (id === element.formapago_id) {
          index = i
        }
      })
      this.pagos.splice(index, 1)
    },
    filterFormasPago (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.formasPago = this.formasPago.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    printPOS (id) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/recibocaja/imprimirpos/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      )
    }
  },
  created: function () {
    this.globalGetForSelect('api/facturacion/formaspago', 'formasPago')
    var today = new Date()
    var dd = String(today.getDate()).padStart(2, '0')
    var mm = String(today.getMonth() + 1).padStart(2, '0')
    var yyyy = today.getFullYear()
    today = yyyy + '/' + mm + '/' + dd
    this.storeItems.fecha_recibo = today
  },
  computed: {
    totalAbono: function () {
      var response = 0
      this.pagos.forEach(function (element, i) {
        response = parseInt(element.valor) + response
      })
      return parseInt(response)
    },
    totalSeleccionado: function () {
      var response = 0
      this.selected.forEach(function (element, i) {
        response = parseInt(element.saldo) + response
      })
      return parseInt(response)
    },
    totalCliente: function () {
      var response = 0
      this.facturas.forEach(function (element, i) {
        response = parseInt(element.saldo) + response
      })
      return parseInt(response)
    }
  },
  watch: {
    sucursal_id: {
      deep: true,
      handler () {
        if (this.sucursal_id !== null) {
          var app = this
          app.selected = []
          axios.get(this.$store.state.jhsoft.url + 'api/facturacion/movimientos/filtro/facturasporsucursalytipodoc/' + this.sucursal_id + '/' + this.$route.params.id).then(
            function (response) {
              app.facturas = response.data
              if (response.data.length < 1) {
                app.$q.notify({ color: 'positive', message: 'El tercero no tiene facturas pendientes.' })
              }
              app.$q.loading.hide()
            }
          )
        }
      }
    },
    $route: {
      deep: true,
      handler () {
        var app = this
        this.sucursal_id = null
        axios.get(this.$store.state.jhsoft.url + 'api/facturacion/tiposrecibocaja/' + this.$route.params.id).then(
          function (response) {
            app.tipoRecCaja = response.data
            app.$q.loading.hide()
          }
        )
      }
    }
  },
  mounted () {
    var app = this
    axios.get(this.$store.state.jhsoft.url + 'api/facturacion/tiposrecibocaja/' + this.$route.params.id).then(
      function (response) {
        app.tipoRecCaja = response.data
      }
    )
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
    .q-table th{
      opacity: 1;
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
      top: 24px;
    }
</style>
