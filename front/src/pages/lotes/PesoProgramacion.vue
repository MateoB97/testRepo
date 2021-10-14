<template>
  <div>
    <q-page padding>
        <q-dialog v-model="show.addAnimales" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 90%" class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <h3>Ingreso de animal:</h3>
            <div class="overflow-hidden">
              <div class="row q-col-gutter-sm">
                <div class="col">
                  <q-input type="number" v-model="storeItems.num_animal" label="N° de Animal"/>
                </div>
                <div class="col">
                  <q-input v-model="storeItems.ppe" label="Peso Pie"/>
                </div>
                <div class="col">
                  <q-input v-model="storeItems.pcc" label="Peso Canal Caliente"/>
                </div>
                <div class="col">
                  <q-input v-model="storeItems.pcr" label="Peso Canal Frio"/>
                </div>
              </div>
              <div class="row q-mt-md">
                <q-btn v-close-popup color="primary" v-on:click="localValidation()" label="Guardar" />
              </div>
            </div>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>

        <h3>Pesos por programación</h3>
        <div class="row q-col-gutter-md">
          <div class="col-3">
            <q-select
              class="w-100"
              v-model="temp.programacion"
              use-input
              hide-selected
              fill-input
              option-value="programacion_id"
              option-label="programacion_id"
              label="Seleccione Programacion"
              option-disable="inactive"
              input-debounce="0"
              :options="options.programaciones"
              @filter="filterProgramaciones"
              @input="selectedProgramacion"
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
          <div v-if="temp.programacion" class="col">
            N° Animales: {{ temp.programacion.num_animales_programacion }} - Lote: {{ temp.programacion.consecutivo }}  - Marca: {{ temp.programacion.marca }} - Tercero: {{ temp.programacion.tercero }} - Sucursal: {{ temp.programacion.sucursal }}
          </div>
        </div>
        <div class="row q-mt-md">
          <q-btn v-if="temp.programacion !== null" color="warning" v-on:click="openAddAnimals" label="Agregar animal" />
        </div>
        <div v-if="temp.programacion !== null" class="row q-mt-xl">
            <q-table
                title= "Pesos por licencia"
                :data="tableData"
                :columns="columns"
                :filter="filter"
                :visible-columns="visibleColumns"
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
                    <q-btn class="q-ml-xs" icon="close" v-on:click="deletePeso(props.value)" color="negative"></q-btn>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'PagePesosProgramacion',
  data: function () {
    return {
      urlAPI: 'api/lotes/programaciones/pesoprogramacion',
      storeItems: {
        num_animal: null,
        ppe: null,
        pcc: null,
        pcr: null,
        lotProgramacion_id: null
      },
      programaciones: [],
      options: {
        programaciones: this.programaciones
      },
      temp: {
        programacion: null
      },
      show: {
        addAnimales: false
      },
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'num_animal', required: true, label: 'N° Animal', align: 'left', field: 'num_animal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'ppe', required: true, label: 'Peso Pie', align: 'left', field: 'ppe', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'pcc', required: true, label: 'Peso Canal Caliente', align: 'left', field: 'pcc', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'pcr', required: true, label: 'Peso Canal Frio', align: 'left', field: 'pcr', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'actions'],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/lotes/programaciones/pesosporprogramacion/' + parseInt(this.temp.programacion.programacion_id)).then(
        function (response) {
          app.tableData = response.data
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
      this.storeItems.num_animal = null
      this.storeItems.ppe = null
      this.storeItems.pcc = null
      this.storeItems.pcr = null
    },
    preSave () {
      this.storeItems.lotProgramacion_id = this.temp.programacion.programacion_id
    },
    postEdit () {
    },
    localValidation () {
      if (this.temp.programacion.num_animales_programacion > this.tableData.length) {
        this.globalValidate('guardar')
      } else {
        this.$q.notify({ color: 'negative', message: 'El numero de animales no puede ser mayor a los ingresados en la Programación.' })
      }
    },
    openAddAnimals () {
      this.show.addAnimales = true
      this.storeItems.num_animal = null
      this.storeItems.ppe = null
      this.storeItems.pcc = null
      this.storeItems.pcr = null
    },
    deletePeso (id) {
      var app = this
      axios.delete(this.$store.state.jhsoft.url + 'api/lotes/programaciones/pesoprogramacion/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'El peso fue eliminado.' })
          } else {
            app.$q.notify({ color: 'negative', message: response.data })
          }
          axios.get(app.$store.state.jhsoft.url + 'api/lotes/programaciones/pesosporprogramacion/' + parseInt(app.temp.programacion.programacion_id)).then(
            function (response) {
              app.tableData = response.data
            }
          ).catch(function (error) {
            console.log(error)
          }).finally(function () {
            app.$q.loading.hide()
          })
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    filterProgramaciones (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.programaciones = this.programaciones.filter(v => v.programacion_id.toLowerCase().indexOf(needle) > -1)
      })
    },
    selectedProgramacion () {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/lotes/programaciones/pesosporprogramacion/' + parseInt(this.temp.programacion.programacion_id)).then(
        function (response) {
          app.tableData = response.data
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    }
  },
  created: function () {
    this.globalGetForSelect('api/lotes/programaciones/abiertas/' + 0, 'programaciones')
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
