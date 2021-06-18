<template>
  <div>
    <q-page padding>
        <!-- inicio popup ver pesadas -->
          <q-dialog v-model="openedVerPesadas" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
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
                      <div class="row col-12">
                        <div class="col-3">
                          <p>PESO BASCULA</p>
                        </div>
                        <div class="col-3">
                          <p>TARA</p>
                        </div>
                        <div class="col-3">
                          <p>PESO NETO</p>
                        </div>
                        <div class="col-3">
                          <p>ELIMINAR</p>
                        </div>
                      </div>
                      <div v-for="pesada in temp.pesadas" v-bind:key='pesada.idPesada' class="row col-12">
                        <div class="col-3">
                          {{ pesada.pesoBascula }}
                        </div>
                        <div class="col-3">
                          {{ pesada.tara }}
                        </div>
                        <div class="col-3">
                          {{ pesada.pesoNeto }}
                        </div>
                        <div class="col-3">
                          <q-btn icon="remove_circle" @click="eliminarPesada(pesada.idPesada)"  color="negative"></q-btn>
                        </div>
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup ver pesadas -->
        <h3>Cierre de inventario</h3>
        <div class="row">
            <div class="col-6">
                <q-input label="Fecha de Movimiento" v-model="storeItems.fecha_cierre" class="date-field" mask="date" :rules="['date']">
                  <template v-slot:append>
                    <q-icon name="event" class="cursor-pointer">
                      <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                        <q-date v-model="storeItems.fecha_cierre" @input="() => $refs.qDateProxy1.hide()" />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
            </div>
            <div class="col-3">
            <ul class="mg-tp-0">
              <li class="ft-20"><strong>Ganancia/Perdida Kilos</strong></li>
              <li>{{ totalPerdidaGananciaKilos }}</li>
            </ul>
          </div>
          <div class="col-3">
            <ul class="mg-tp-0">
              <li class="ft-20"><strong>Ganancia/Perdida Dinero</strong></li>
              <li>{{ totalPerdidaGananciaDinero | toMoney }}</li>
            </ul>
          </div>
        </div>
        <div class="row q-mt-sm">
            <div class="col-2">
                <q-btn color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
            </div>
        </div>
        <div class="row q-mt-sm q-col-gutter-sm">
          <div class="col-2">
            <q-input  @keyup.enter="getProducto" v-model="temp.codigo" label="Cod."></q-input>
          </div>
          <div class="col-3">
            <ul class="mg-tp-0">
              <li class="ft-20"><strong>Producto</strong></li>
              <li>{{ temp.producto }}</li>
            </ul>
          </div>
          <div class="col-2">
            <ul class="mg-tp-0">
              <li class="ft-20"><strong>Cant. Stock</strong></li>
              <li>{{ parseFloat(temp.cantidad_stock).toFixed(3) }}</li>
            </ul>
          </div>
          <div class="col-2">
            <ul class="mg-tp-0">
              <li class="ft-20"><strong>Cant. Cierre</strong></li>
              <li>{{ temp.cantidad_cierre.toFixed(3) }}</li>
            </ul>
          </div>
          <div class="col-2">
            <q-btn class="w-100" color="positive" v-on:click="openedVerPesadas = true" label="Ver pesadas" />
          </div>
        </div>
        <div class="row q-mt-sm q-col-gutter-sm">
          <div class="col-6">
            <Bascula
              ref="basculaComponent"
              v-model="temp.peso"
              :withBasculaSelect="true"
              :inicioAutomatico="true"
            />
          </div>
          <div class="col-2">
            <q-input label="Tara" v-model="temp.tara"></q-input>
          </div>
          <div class="col-2">
            <q-btn class="w-100" color="positive" v-on:click="modifyPesoCierre('+')" label="+" />
          </div>
          <div class="col-2">
            <q-btn class="w-100" color="negative" v-on:click="modifyPesoCierre('-')" label="-" />
          </div>
        </div>
        <div class="row q-mt-xl">
            <q-table
                v-if="viewTable"
                :data="tableData"
                :columns="columns"
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
                <q-td slot="body-cell-cantSistema" slot-scope="props" :props="props">
                    {{ parseFloat(props.value).toFixed(3) }}
                </q-td>
                <q-td slot="body-cell-cantidadCierre" slot-scope="props" :props="props">
                    {{ parseFloat(props.value).toFixed(3) }}
                </q-td>
                <q-td slot="body-cell-perdidaGanancia" slot-scope="props" :props="props">
                    {{ calcularPerdidaGanancia(props.value).toFixed(3) }}
                </q-td>
                <q-td slot="body-cell-perdidaGananciaDinero" slot-scope="props" :props="props">
                    {{ calcularPerdidaGananciaDinero(props.value) | toMoney }}
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import Bascula from 'components/generales/BasculasComponent.vue'

export default {
  name: 'CierreInventario',
  components: {
    Bascula
  },
  data: function () {
    return {
      urlAPI: 'api/inventario/cierre-inventario',
      storeItems: {
        fecha_cierre: null
      },
      viewTable: true,
      temp: {
        peso: null,
        codigo_producto: null,
        producto: null,
        cantidad_stock: 0,
        cantidad_cierre: 0,
        tara: 0,
        pesadas: []
      },
      openedVerPesadas: false,
      tableData: [],
      columns: [
        { name: 'codigo', required: true, label: 'Codigo', align: 'left', field: 'codigo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Producto', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'grupo', required: true, label: 'Grupo', align: 'left', field: 'grupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cantSistema', required: true, label: 'Cant. sistema', align: 'right', field: 'cantidad', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cantidadCierre', required: true, label: 'Cant. Cierre', align: 'right', field: 'cantidad_cierre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'perdidaGanancia', required: true, label: 'Perdida-Ganancia Kilos', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'perdidaGananciaDinero', required: true, label: 'Perdida-Ganancia Dinero', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  beforeRouteLeave: function (to, from, next) {
    if (this.$refs.basculaComponent) {
      this.$refs.basculaComponent.stopGetPeso()
    }
    next()
  },
  methods: {
    postSave () {
      this.globalGetForSelect('api/inventario/items', 'tableData')
      this.temp = {
        peso: null,
        codigo_producto: null,
        producto: null,
        cantidad_stock: 0,
        cantidad_cierre: 0
      }
    },
    preSave () {
      this.storeItems.lineas = this.tableData
      this.storeItems.total_diferencia_kilos = this.totalPerdidaGananciaKilos
      this.storeItems.total_diferencia_dinero = this.totalPerdidaGananciaDinero
    },
    postEdit () {
    },
    getProducto () {
      var tempItem = this.tableData.find(v => v.codigo === this.temp.codigo)
      this.temp.producto = tempItem.nombre
      this.temp.cantidad_stock = tempItem.cantidad
      if (!tempItem.cantidad_cierre) {
        tempItem.cantidad_cierre = 0
      }
      this.temp.cantidad_cierre = tempItem.cantidad_cierre
      this.temp.pesadas = tempItem.pesadas
    },
    modifyPesoCierre (operator) {
      var item = {
        idPesada: '',
        pesoBascula: this.temp.peso,
        tara: this.temp.tara,
        pesoNeto: this.temp.peso - this.temp.tara
      }
      var tempItem = this.tableData.find(v => v.codigo === this.temp.codigo)
      this.temp.peso = this.temp.peso - this.temp.tara
      var status = 1
      if (operator === '+') {
        tempItem.cantidad_cierre = parseFloat(tempItem.cantidad_cierre) + parseFloat(this.temp.peso)
        this.temp.cantidad_cierre = parseFloat(this.temp.cantidad_cierre) + parseFloat(this.temp.peso)
      } else {
        if (parseFloat(this.temp.peso) <= parseFloat(this.temp.cantidad_cierre)) {
          tempItem.cantidad_cierre = parseFloat(tempItem.cantidad_cierre) - parseFloat(this.temp.peso)
          this.temp.cantidad_cierre = parseFloat(this.temp.cantidad_cierre) - parseFloat(this.temp.peso)
        } else {
          status = 0
          this.$q.notify({ color: 'negative', message: 'Cantidad de cierre no puede ser negativo.' })
        }
      }
      if (status === 1) {
        if (!tempItem.pesadas) {
          tempItem.pesadas = []
          tempItem.totalPesadas = 0
        }
        tempItem.totalPesadas += 1
        item.idPesada = tempItem.totalPesadas
        tempItem.pesadas.push(item)
      }
      this.temp.pesadas = tempItem.pesadas
      this.temp.tara = 0
    },
    eliminarPesada (id) {
      var tempItem = this.tableData.find(v => v.codigo === this.temp.codigo)
      var index = tempItem.pesadas.findIndex(v => v.idPesada === id)
      tempItem.cantidad_cierre = parseFloat(this.temp.cantidad_cierre) - parseFloat(tempItem.pesadas[index].pesoNeto)
      this.temp.cantidad_cierre = parseFloat(this.temp.cantidad_cierre) - parseFloat(tempItem.pesadas[index].pesoNeto)
      tempItem.pesadas.splice(index, 1)
      this.temp.pesadas = tempItem.pesadas
    },
    calcularPerdidaGanancia (id) {
      var tempItem = this.tableData.find(v => v.id === id)
      return parseFloat(tempItem.cantidad_cierre) - parseFloat(tempItem.cantidad)
    },
    calcularPerdidaGananciaDinero (id) {
      var tempItem = this.tableData.find(v => v.id === id)
      return (parseFloat(tempItem.cantidad_cierre) - parseFloat(tempItem.cantidad)) * parseInt(tempItem.precio)
    }
  },
  created: function () {
    this.globalGetForSelect('api/inventario/items', 'tableData')
  },
  computed: {
    totalPerdidaGananciaKilos: function () {
      var response = 0
      this.tableData.forEach(function (element, i) {
        response += parseFloat(element.cantidad_cierre) - parseFloat(element.cantidad)
      })
      return response.toFixed(3)
    },
    totalPerdidaGananciaDinero: function () {
      var response = 0
      this.tableData.forEach(function (element, i) {
        response += (parseFloat(element.cantidad_cierre) - parseFloat(element.cantidad)) * parseInt(element.precio)
      })
      return response.toFixed(0)
    }
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
    ul {
      list-style-type: none;
    }
    .ft-20 {
      font-size: 20px;
    }
    .mg-tp-0 {
      margin-top: 0px;
    }
</style>
