<template>
    <div>
        <q-page padding>
            <q-dialog v-model="openedResumen" :content-css="{minWidth: '80vw', minHeight: '80vh'}">
              <q-layout view="Lhh lpR fff" container style="height: 80vh; max-width: 800px" class="bg-white">
                <q-header class="bg-primary">
                  <q-toolbar>
                    <q-btn flat v-close-popup round dense icon="close" />
                  </q-toolbar>
                </q-header>

                <q-page-container>
                  <q-page padding>
                    <h3>Despacho:</h3>
                    <p>Peso en vacio: {{ globalFormatPeso(datos.pesoVacio) }}</p>
                     <div class="q-pa-md" style="max-width: 100%">
                        <q-list bordered>
                          <q-expansion-item
                            header-class="bg-teal text-white"
                            expand-icon-class="text-white"
                            v-for="element in dataResumen"
                            :key="element.producto"
                          >
                            <template v-slot:header>
                              <q-item-section>
                               {{element.producto}}
                              </q-item-section>

                              <q-item-section side>
                                <span class="text-white">{{element.peso }} Kl</span>
                              </q-item-section>
                            </template>

                            <q-card v-for="elementItem in element.items" :key="elementItem.inventario_id">
                              <q-card-section>
                                <div class="row">
                                  <div class="col-3">
                                    id: {{elementItem.inventario_id}}
                                  </div>
                                  <div class="col-3">
                                    Lote: {{elementItem.lote}}
                                  </div>
                                  <div class="col-3">
                                    Empaque: {{elementItem.fecha_empaque}}
                                  </div>
                                  <div class="col-3">
                                    Peso: {{elementItem.peso }} Kl
                                  </div>
                                </div>
                              </q-card-section>
                            </q-card>
                          </q-expansion-item>
                        </q-list>
                     </div>
                  </q-page>
                </q-page-container>
              </q-layout>
            </q-dialog>

            <div class="row q-col-gutter-md">
                <!-- <div class="col-3">
                    <div class="row q-col-gutter-sm">
                      <div class="col-12">
                        <h5>Buscar:</h5>
                      </div>
                    </div>
                    <div class="col-12">
                      <q-input label="Fecha Inicial" v-model="storeItems.fecha_inicial" mask="date" :rules="['date']">
                        <template v-slot:append>
                          <q-icon name="event" class="cursor-pointer">
                            <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                              <q-date v-model="storeItems.fecha_inicial" @input="() => $refs.qDateProxy2.hide()" />
                            </q-popup-proxy>
                          </q-icon>
                        </template>
                      </q-input>
                    </div>
                    <div class="col-12">
                      <q-input label="Fecha Final" v-model="storeItems.fecha_final" mask="date" :rules="['date']">
                        <template v-slot:append>
                          <q-icon name="event" class="cursor-pointer">
                            <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                              <q-date v-model="storeItems.fecha_final" @input="() => $refs.qDateProxy1.hide()" />
                            </q-popup-proxy>
                          </q-icon>
                        </template>
                      </q-input>
                    </div>
                    <div class="col-12">
                        <q-select
                          v-model="datos.tercero_id"
                          use-input
                          hide-selected
                          fill-input
                          option-label="label"
                          label="cliente"
                          option-disable="inactive"
                          map-options
                          input-debounce="0"
                          :options="options.terceros"
                          @filter="filterFn"
                          @input="globalSelectedTercero()"
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
                      <div class="col-12">
                        <q-select
                          v-model="storeItems.terceroSucursal_id"
                          use-input
                          hide-selected
                          fill-input
                          option-label="label"
                          label="Sucursal"
                          option-disable="inactive"
                          option-value="id"
                          map-options
                          emit-value
                          input-debounce="0"
                          :options="options.sucursales"
                          @filter="filterSucursales"
                          @input="globalSelectedTercero()"
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
                    <div class="col-12 q-mt-sm">
                      <q-btn color="primary" v-on:click="filtrarDespachos()" label="Filtrar" />
                    </div>
                </div> -->
                <div class="col-12">
                  <div class="row">
                    <div class="col-12">
                      <q-table
                          title="Despachos"
                          :data="tableData"
                          :columns="Columns"
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

                          <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                              <q-btn icon="remove_red_eye" v-on:click="getDataResumen(props.value)" color="positive"></q-btn>
                              <a target="_blank" :href="$store.state.jhsoft.url+'api/despachos/getdatacertificado/'+props.value "><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn> </a>
                              <q-btn class="q-ml-xs" icon="delete" v-on:click="globalValidate('eliminar', props.value)" color="negative"></q-btn>
                          </q-td>
                      </q-table>
                    </div>
                  </div>
                </div>
            </div>
        </q-page>
    </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'PageSalMercanciaListado',
  data () {
    return {
      urlAPI: 'api/despachos/items',
      left: true,
      right: true,
      openedResumen: false,
      listas: [],
      almacenamientos: [],
      tableData: [],
      dataResumen: [],
      storeItems: {
        fecha_inicial: null,
        fecha_final: null,
        terceroSucursal_id: null
      },
      datos: {
        tercero_id: null,
        pesoVacio: 0
      },
      options: {
        terceros: this.terceros,
        sucursales: this.sucursales
      },
      show: {
        subgrupos: false,
        productos: false,
        noProductos: false
      },
      Columns: [
        { name: 'consecutivo', required: true, label: 'Consec', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'fecha', required: true, label: 'Fecha', align: 'left', field: 'fecha', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 100px' }
      ],
      interval: null,
      filter: '',
      separator: 'horizontal'
    }
  },
  mixins: [globalFunctions],
  methods: {
    preSave () {
    },
    postSave () {
    },
    filterFn (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.terceros = this.terceros.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterSucursales (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.sucursales = this.sucursales.filter(v => v.label.toLowerCase().indexOf(needle) > -1)
      })
    },
    async filtrarDespachos () {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/despachos/datefilter/' + this.storeItems.fecha_inicial + '/' + this.storeItems.fecha_final + '/' + this.storeItems.terceroSucursal_id)
        this.tableData = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los Despachos!' })
      } finally {
        this.showSubgrupos = true
      }
    },
    async getDataResumen (id) {
      var ArrayResumen = []
      var pesoVacio = 0
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/despachos/getdataresumen/' + id)
        var response = data.data
        response.forEach(item => {
          var index = null
          if (item.almacenamiento.indexOf('vacio') !== -1) {
            pesoVacio = pesoVacio + parseFloat(item.peso)
          }
          if (ArrayResumen.length > 0) {
            ArrayResumen.forEach(function (element, i) {
              if (item.producto === element.producto) {
                index = i
              }
            })
          }
          if (index === null) {
            var newProduct = {
              producto: item.producto,
              peso: item.peso,
              items: [ item ]
            }
            ArrayResumen.push(newProduct)
          } else {
            ArrayResumen[index].peso = parseFloat(ArrayResumen[index].peso) + parseFloat(item.peso)
            ArrayResumen[index].peso = ArrayResumen[index].peso.toFixed(3)
            ArrayResumen[index].items.push(item)
          }
        })
        this.dataResumen = ArrayResumen
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los Despachos!' })
      } finally {
        this.openedResumen = true
        this.datos.pesoVacio = pesoVacio
      }
    }
  },
  created: function () {
    this.globalGetForSelect('api/despachos/items', 'tableData')
    this.globalGetForSelect('api/terceros/items', 'terceros')
  },
  computed: {
  },
  mounted () {
    // this.getPeso()
  }
}
</script>

<style scoped>
  h5{
    width: 100%;
    margin: 5px;
  }
</style>
