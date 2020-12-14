<template>
  <div>
    <q-page padding>
        <!-- inicio popup CXP X CLIENTE -->
          <q-dialog v-model="modales.openedCXPxtercero" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 30vh; max-width: 50vw" class="bg-white">
              <q-header class="bg-primary">
                <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12">
                        <q-select
                          v-model="datos.tercero_id"
                          use-input
                          hide-selected
                          fill-input
                          option-label="nombre"
                          label="Tercero"
                          option-disable="inactive"
                          option-value="id"
                          input-debounce="0"
                          emit-value
                          map-options
                          :options="options.terceros"
                          @filter="filterTerceros"
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
                    <div class="row q-mt-md text-center">
                      <a target="_blank" :href="$store.state.jhsoft.url+'api/compras/informes/cxpxcliente/'+ datos.tercero_id +'?token='+ $store.state.jhsoft.token">
                        <q-btn class="q-ml-xs" icon="assignment" color="primary">Generar</q-btn>
                      </a>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup CXP X CLIENTE  -->

        <!-- inicio popup productos con lista de precio -->
          <q-dialog v-model="modales.productosxListaPrecio" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 30vh; max-width: 50vw" class="bg-white">
              <q-header class="bg-primary">
                <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12">
                        <q-select
                          v-model="datos.listaprecio_id"
                          use-input
                          hide-selected
                          fill-input
                          option-label="nombre"
                          label="Lista de precios"
                          option-disable="inactive"
                          option-value="id"
                          input-debounce="0"
                          emit-value
                          map-options
                          :options="options.listaprecios"
                          @filter="filterListaprecios"
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
                    <div class="row q-mt-md text-center">
                      <a target="_blank" :href="$store.state.jhsoft.url+'api/productos/informes/productosxlistaprecio/'+ datos.listaprecio_id +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" icon="assignment" color="primary">Generar</q-btn> </a>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup Saldos en cartera x cliente  -->

        <!-- inicio popup filtro fechas -->
          <q-dialog v-model="modales.filtrofecha" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 30vh; max-width: 50vw" class="bg-white">
              <q-header class="bg-primary">
                <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col-6">
                          <q-input label="Fecha de Inicial" v-model="datos.fecha_inicial" mask="date" :rules="['date']">
                            <template v-slot:append>
                              <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                  <q-date v-model="datos.fecha_inicial" @input="() => $refs.qDateProxy.hide()" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                      </div>
                      <div class="col-6">
                          <q-input label="Fecha de Final" v-model="datos.fecha_final" mask="date" :rules="['date']">
                            <template v-slot:append>
                              <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                                  <q-date v-model="datos.fecha_final" @input="() => $refs.qDateProxy1.hide()" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                      </div>
                    </div>
                    <div class="row q-mt-md text-center">
                      <a target="_blank" :href="$store.state.jhsoft.url+ ruta_fecha_activa + fecha_inicial + '/' + fecha_final +'?token='+ $store.state.jhsoft.token">
                        <q-btn class="q-ml-xs" icon="assignment" color="primary">Generar</q-btn>
                      </a>
                      <!-- <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/movimientos/filtro/impresionmovsporfecha/' + fecha_inicial + '/' + fecha_final">
                        <q-btn class="q-ml-xs" icon="local_printshop" color="primary">Generar POS</q-btn>
                      </a> -->
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup filtro por fecha  -->

        <!-- inicio popup filtro fechas y tercero -->
          <q-dialog v-model="modales.filtrofechatercero" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 40vh; max-width: 50vw" class="bg-white">
              <q-header class="bg-primary">
                <q-toolbar>
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col-6">
                          <q-input label="Fecha de Inicial" v-model="datos.fecha_inicial" mask="date" :rules="['date']">
                            <template v-slot:append>
                              <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                                  <q-date v-model="datos.fecha_inicial" @input="() => $refs.qDateProxy.hide()" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                      </div>
                      <div class="col-6">
                          <q-input label="Fecha de Final" v-model="datos.fecha_final" mask="date" :rules="['date']">
                            <template v-slot:append>
                              <q-icon name="event" class="cursor-pointer">
                                <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                                  <q-date v-model="datos.fecha_final" @input="() => $refs.qDateProxy1.hide()" />
                                </q-popup-proxy>
                              </q-icon>
                            </template>
                          </q-input>
                      </div>
                    </div>
                    <div class="row q-col-gutter-sm">
                      <div class="col-12">
                        <SelectTerceroSucursal v-model="sucursal" :editor="sucursal" columnas='col-12' labelTercero='Tercero'/>
                      </div>
                    </div>
                    <div class="row q-mt-md text-center">
                      <a target="_blank" :href="$store.state.jhsoft.url+ ruta_fecha_activa + fecha_inicial + '/' + fecha_final + '/'+ sucursal +'?token='+ $store.state.jhsoft.token">
                        <q-btn class="q-ml-xs" icon="assignment" color="primary">Generar</q-btn>
                      </a>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup filtro por fecha  -->

        <div class="overflow-hidden">
            <div v-if="this.$store.state.jhsoft.tipo_licencia !== 1" class="row q-col-gutter-md">
                <div class="col-12 q-mt-md"><h4 style="margin:0px">Cartera</h4></div>
                  <div class="col-4">
                    <GlobalFiltersComponent
                      titleBtn="CXC"
                      url="api/reportesgenerados/reportes/saldosencartera"
                      :tercerosFilter="true"
                      :tipoDocFilter="true"
                      :datesFilter="true"
                      :gruposFilter="true"
                      :dateUnique="0"
                    />
                  </div>
                <div class="col-4">
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/compras/informes/cuentasporpagar'+'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs w-100" color="primary">CXP</q-btn> </a>
                </div>
                <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="modales.openedCXPxtercero = true, datos.tercero_id = null" label="CXP por tercero" />
                </div>
            </div>
            <div class="row q-col-gutter-md">
              <div class="col-12 q-mt-md"><h4 style="margin:0px">Productos</h4></div>
                <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="modales.productosxListaPrecio = true, datos.listaprecio_id = null" label="Productos x lista precios" />
                </div>
                <div class="col-4">
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/productos/informes/listadoproductos'+'?token='+ $store.state.jhsoft.token"><q-btn  class="q-ml-xs w-100" color="primary">Listado Productos</q-btn> </a>
                </div>
            </div>
            <div class="row q-col-gutter-md">
              <div class="col-12 q-mt-md"><h4 style="margin:0px">Ventas</h4></div>
                <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="activarRutaFecha(0), datos.fecha_inicial = null, datos.fecha_final = null" label="Ventas Netas x Fecha" />
                </div>
                <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="activarRutaFecha(1), datos.fecha_inicial = null, datos.fecha_final = null" label="Recaudo Por Fecha" />
                </div>
            </div>
            <div class="row q-col-gutter-md" v-if="this.$store.state.jhsoft.tipo_licencia !== 1">
              <div class="col-12 q-mt-md"><h4 style="margin:0px">Compras</h4></div>
                <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="activarRutaFecha(2), datos.fecha_inicial = null, datos.fecha_final = null" label="Compras Netas x Fecha" />
                </div>
            </div>
            <div class="row q-col-gutter-md">
              <div class="col-12 q-mt-md"><h4 style="margin:0px">Movimientos</h4></div>
                <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="activarRutaFecha(3), datos.fecha_inicial = null, datos.fecha_final = null" label="Movimiento Forma Pago x Fecha" />
                </div>
                <div class="col-4">
                  <GlobalFiltersComponent
                      titleBtn="Movimientos x Fecha"
                      url="api/reportesgenerados/reportes/movimientosporfecha"
                      :tercerosFilter="true"
                      :tipoDocFilter="false"
                      :datesFilter="true"
                      :gruposFilter="false"
                      :dateUnique="0"
                    />
                    <!-- <q-btn class="w-100" color="primary"  v-on:click="activarRutaMovsCustom(7), datos.fecha_inicial = null, datos.fecha_final = null" label="Movimientos x Fecha" /> -->
                </div>
                <div class="col-4">
                  <GlobalFiltersComponent
                      titleBtn="Movimientos x Fecha Detalles"
                      url="api/reportesgenerados/reportes/movimientosporfechadetalles"
                      :tercerosFilter="true"
                      :tipoDocFilter="true"
                      :datesFilter="true"
                      :gruposFilter="true"
                      :dateUnique="0"
                    />
                    <!-- <q-btn class="w-100" color="primary"  v-on:click="activarRutaMovsCustom(7), datos.fecha_inicial = null, datos.fecha_final = null" label="Movimientos x Fecha" /> -->
                </div>
                <div class="col-4">
                  <GlobalFiltersComponent
                    titleBtn="Relacion Tiquete Factura"
                    url="api/reportesgenerados/reportes/relaciontiquetefactura"
                    :datesFilter="true"
                    :dateUnique="1"
                  />
                </div>
                <!-- <div class="col-4">
                    <q-btn class="w-100" color="primary"  v-on:click="activarRutaMovsDetailsCustom(8), datos.fecha_inicial = null, datos.fecha_final = null" label="Movimientos Detalles" />
                </div> -->
            </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'
import GlobalFiltersComponent from 'components/filters/globalFiltersComponent.vue'
const axios = require('axios')

export default {
  name: 'RepCarteraTotal',
  components: {
    SelectTerceroSucursal,
    GlobalFiltersComponent
  },
  data: function () {
    return {
      terceros: [],
      sucursales: [],
      sucursal: null,
      listaprecios: [],
      datos: {
        tercero_id: null,
        listaprecio_id: null,
        fecha_inicial: null,
        fecha_final: null
      },
      options: {
        terceros: this.terceros,
        listaprecios: this.listaprecios
      },
      rutas_fechas: [
        'api/facturacion/informes/ventasnetasporfecha/',
        'api/facturacion/informes/recaudoporfecha/',
        'api/compras/informes/comprasnetasporfecha/',
        'api/facturacion/informes/formasdepagopormovimientoporfecha/',
        'api/facturacion/informes/movimientosporfecha/',
        'api/facturacion/informes/movimientosporfechaportercero/',
        'api/reportesgenerados/reportes/saldosencartera/',
        'api/reportesgenerados/reportes/movimientosporfecha/',
        'api/reportesgenerados/reportes/relaciontiquetefactura/'
      ],
      ruta_fecha_activa: null,
      modales: {
        openedCXCxtercero: false,
        openedCXPxtercero: false,
        productosxListaPrecio: false,
        filtrofecha: false,
        filtrofechatercero: false,
        filtrocxccustom: false,
        filtromovscustom: false,
        filtromovsdetailscustom: false,
        filtrotiquetefactura: false
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
    },
    postEdit () {
    },
    assingTerceroID (val) {
      this.datos.tercero_id = val
      console.log(this.datos.tercero_id)
    },
    filterTerceros (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.terceros = this.terceros.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterListaprecios (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.listaprecios = this.listaprecios.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    activarRutaFecha (ruta) {
      this.ruta_fecha_activa = this.rutas_fechas[ruta]
      this.modales.filtrofecha = true
    },
    activarRutaFechaTercero (ruta) {
      this.ruta_fecha_activa = this.rutas_fechas[ruta]
      this.modales.filtrofechatercero = true
    },
    activarRutaCxcCustom (ruta) {
      this.ruta_fecha_activa = this.rutas_fechas[ruta]
      this.modales.filtrocxccustom = true
    },
    activarRutaMovsCustom (ruta) {
      this.ruta_fecha_activa = this.rutas_fechas[ruta]
      this.modales.filtromovscustom = true
    },
    activarRutaMovsDetailsCustom (ruta) {
      this.ruta_fecha_activa = this.rutas_fechas[ruta]
      this.modales.filtromovsdetailscustom = true
    },
    activarRutaTiqueteFactura (ruta) {
      this.ruta_fecha_activa = this.rutas_fechas[ruta]
      this.modales.filtrotiquetefactura = true
    },
    setDates (val) {
      console.log(val)
    }
  },
  created: function () {
    this.globalGetForSelect('api/terceros/items', 'terceros')
    this.globalGetForSelect('api/productos/listadeprecios/estado/activos', 'listaprecios')
  },
  printPoscxc: function () {
    var app = this
    axios.get('http://localhost/fusionback/public/api/facturacion/movimientos/filtro/imprescioncxc').then(
      function (response) {
        if (response) {
          return app.$q.notify({ color: 'negative', message: 'Impresión con exito' })
        }
      }
    )
  },
  computed: {
    fecha_inicial: function () {
      if (this.datos.fecha_inicial) {
        return this.datos.fecha_inicial.toString().replace('/', '-').replace('/', '-')
      } else {
        return null
      }
    },
    fecha_final: function () {
      if (this.datos.fecha_final) {
        return this.datos.fecha_final.toString().replace('/', '-').replace('/', '-')
      } else {
        return null
      }
    }
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
    .w-100{
      width: 100%;
    }
</style>
