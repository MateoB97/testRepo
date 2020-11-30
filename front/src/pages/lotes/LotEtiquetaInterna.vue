<template>
    <div>
        <q-page padding>
            <div class="row q-col-gutter-md">
              <div class="col-3">
                  <q-select
                      label="Seleccione tipo de almacenamiento"
                      v-model="storeItems.prodAlmacenamiento_id"
                      :options="almacenamientos"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                      emit-value
                      map-options
                    />
                </div>
                <div class="col-3">
                  <q-select
                      label="Seleccione impresora"
                      v-model="storeItems.impresora"
                      :options="impresoras"
                      option-value="ruta"
                      option-label="nombre"
                      option-disable="inactive"
                      emit-value
                      map-options
                    />
                </div>
                <div class="col-3">
                    <q-checkbox v-model="storeItems.marinado" left-label label="Marinado" />
                </div>
                <div class="col-3">
                    <q-checkbox v-model="storeItems.reimpresion" left-label label="Reimpresión" />
                </div>
            </div>
            <div class="row q-col-gutter-md">
              <div class="col-5 row">
                <div class="col-4 self-center text-center">
                  <q-btn class="btn-100w" :class="classActive == 'Todos' ? 'bg-positive' : ''" color="primary" v-on:click="getTodos(), classActive = 'Todos'" label="Todos" />
                </div>
                <div class="col-4 self-center text-center">
                  <q-btn class="btn-100w" :class="classActive == 'Res' ? 'bg-positive' : ''" color="primary" v-on:click="getPorGrupo(2), classActive = 'Res'" label="Res" />
                </div>
                <div class="col-4 self-center text-center">
                  <q-btn class="btn-100w" :class="classActive == 'Cerdo' ? 'bg-positive' : ''" color="primary" v-on:click="getPorGrupo(1), classActive = 'Cerdo'" label="Cerdo" />
                </div>
              </div>
              <div class="col-2">
                <q-input v-model="datos.lote_id" label="Buscar por Lote" />
              </div>
              <div class="col-2 self-center">
                <q-btn class="btn-100w" :class="classActive == 'Lote' ? 'bg-positive' : ''" color="primary" v-on:click="getPorLote(), classActive = 'Lote'" label="Buscar" />
              </div>
              <div class="col-2 self-center">
                <p>Piezas impresas: {{ datos.piezasImpresas }}</p>
              </div>
            </div>
            <div class="row q-mt-md q-col-gutter-md">
                <div class="col-5 col-lotes">
                    <div class="q-pa-md q-gutter-md">
                        <q-card class="my-card-prog" v-for="programacion in programaciones" :key="programacion.programacion_id" @click="selectedProgramacion(programacion)">
                            <q-card-section>
                               <p>programación: {{ programacion.programacion_id }}</p>
                               <p>Fecha desposte: {{ programacion.fecha_desposte }}</p>
                               <p>Marca: {{ programacion.marca }}</p>
                               <p>Lote: {{ programacion.lote_id }}   //   Numero animales: {{ programacion.num_animales_programacion }}</p>
                            </q-card-section>
                        </q-card>
                    </div>
                </div>
                <div class="col-4">
                    <div v-if="show.subgrupos" class="q-pa-md row items-start q-gutter-md col-lotes">
                        <q-card class="my-card" v-for="subgrupo in listas.subgrupos" :key="subgrupo.id" @click="selectedSubgrupo(subgrupo)">
                            <q-card-section>
                                <p>{{ subgrupo.nombre }}</p>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div v-if="show.productos" class="q-pa-md row items-start q-gutter-md col-lotes">
                        <q-card class="my-card" v-for="producto in listas.productos" :key="producto.id" @click="selectedProducto(producto)">
                            <q-card-section>
                                <p>{{ producto.nombre }}</p>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div v-if="show.noProductos" class="q-pa-md row items-start q-gutter-md">
                        <h5>No existen productos creados para este subgrupo.</h5>
                    </div>
                </div>
                <div class="col-3">
                    <div class="row">
                      <h5>Lote:</h5>
                      <p>{{ datos.lote }}</p>
                    </div>
                    <div class="row">
                      <h5>Marca:</h5>
                      <p>{{ datos.marca }}</p>
                    </div>
                    <div class="row">
                      <h5>Grupo:</h5>
                      <p>{{ datos.grupo }}</p>
                    </div>
                    <div class="row">
                      <h5>Subgrupo:</h5>
                      <p>{{ datos.subgrupo }}</p>
                    </div>
                    <div class="row">
                      <h5>Producto:</h5>
                      <p>{{ datos.producto }}</p>
                    </div>
                    <div class="row q-mt-md">
                      <q-btn class="print-btn" @click="printEtiquetas(1)" color="primary" label="Imprimir X1" />
                    </div>
                    <div class="row q-mt-md">
                      <q-btn class="print-btn" @click="printEtiquetas(2)" color="primary" label="Imprimir X2" />
                    </div>
                    <div class="row q-mt-md">
                      <q-btn class="print-btn" @click="printEtiquetas(4)" color="primary" label="Imprimir X4" />
                    </div>
                    <div class="row q-mt-md">
                      <q-btn class="print-btn" @click="printEtiquetas(6)" color="primary" label="Imprimir X6" />
                    </div>
                    <div class="row q-mt-md">
                      <q-btn class="print-btn" @click="printEtiquetas(8)" color="primary" label="Imprimir X8" />
                    </div>
                    <div class="row q-mt-md">
                      <q-btn class="print-btn" @click="printEtiquetas(10)" color="primary" label="Imprimir X10" />
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
  name: 'PageLotEtiquetaInterna',
  data () {
    return {
      urlAPI: 'api/inventario/etiqueta-interna',
      left: true,
      right: true,
      listas: [],
      classActive: 'Todos',
      almacenamientos: [],
      storeItems: {
        prog_lotes_id: null,
        producto_id: null,
        prodAlmacenamiento_id: null,
        numEtiquetas: null,
        impresora: null,
        marinado: false,
        reimpresion: false
      },
      programaciones: [],
      grupos: [],
      datos: {
        lote: 'Debe seleccionar una programación.',
        marca: 'Debe seleccionar una programación.',
        grupo: 'Debe seleccionar una programación.',
        subgrupo: 'Debe seleccionar un subgrupos.',
        producto: 'Debe seleccionar un producto.',
        piezasImpresas: 'Debe seleccionar un producto.'
      },
      impresoras: [],
      show: {
        subgrupos: false,
        productos: false,
        noProductos: false
      },
      interval: null
    }
  },
  mixins: [globalFunctions],
  methods: {
    preSave () {
    },
    postSave () {
    },
    async getPiezasImpresas () {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/inventario/piezasimpresas/' + this.storeItems.prog_lotes_id + '/' + this.storeItems.producto_id)
        var tempData = data.data
        this.datos.piezasImpresas = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al obtener las impresas!' })
      } finally {
      }
    },
    async selectedProgramacion (programacion) {
      this.show.subgrupos = false
      this.show.productos = false
      this.show.noProductos = false
      this.datos.lote = programacion.lote_id
      this.datos.programacion_id = programacion.programacion_id
      this.datos.marca = programacion.marca
      this.datos.grupo = programacion.grupo
      this.datos.num_animales = programacion.num_animales_programacion
      this.storeItems.prog_lotes_id = parseInt(programacion.programacion_id)
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/subgrupos/grupofilter/' + programacion.grupo_id)
        var tempData = data.data
        this.listas.subgrupos = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los subgrupos!' })
      } finally {
        this.show.subgrupos = true
        this.show.grupos = false
      }
    },
    async selectedSubgrupo (subgrupo) {
      this.datos.subgrupo = subgrupo.nombre
      this.show.subgrupos = false
      this.listas.productos = []
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/items/subgrupofilter/' + subgrupo.id)
        var tempData = data.data
        this.listas.productos = tempData
        if (tempData.length === 0) {
          this.show.noProductos = true
        } else {
          this.show.productos = true
        }
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los productos!' })
      } finally {
      }
    },
    async selectedProducto (producto) {
      this.storeItems.producto_id = producto.id
      this.datos.producto = producto.nombre
      this.getPiezasImpresas()
    },
    async printEtiquetas (numEtiquetas) {
      this.$q.notify({ color: 'warning', message: 'Enviando Etiquetas!' })
      this.storeItems.numEtiquetas = numEtiquetas
      try {
        let response = await axios.post(
          this.$store.state.jhsoft.url + this.urlAPI, this.storeItems
        )
        if (response.data === 'doneNoRestore') { // si se desea restaurar el formulario el api debe devolver "done"
          this.$q.notify({ color: 'positive', message: 'Imprimiendo Etiquetas!' })
        }
      } catch (error) {
        this.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo imprimir las Etiquetas!' })
      } finally {
        this.getPiezasImpresas()
      }
    },
    async getPorGrupo (id) {
      this.$q.loading.show()
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/lotes/programaciones/abiertasporgrupo/' + id)
        this.programaciones = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las programaciones!' })
      } finally {
        this.$q.loading.hide()
        this.$forceUpdate()
      }
    },
    async getPorLote (id) {
      this.$q.loading.show()
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/lotes/programaciones/abiertasporlote/' + this.datos.lote_id)
        this.programaciones = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las programaciones!' })
      } finally {
        this.$q.loading.hide()
        this.$forceUpdate()
        this.datos.lote_id = null
      }
    },
    async getTodos () {
      this.globalGetForSelect('api/lotes/programaciones/abiertas', 'programaciones')
    }
  },
  created: function () {
    this.globalGetForSelect('api/lotes/programaciones/abiertas', 'programaciones')
    this.globalGetForSelect('api/productos/grupos', 'grupos')
    this.globalGetForSelect('api/productos/almacenamiento', 'almacenamientos')
    this.globalGetForSelect('api/generales/impresoras', 'impresoras')
  },
  computed: {
  },
  mounted () {
    // this.getPeso()
  }
}
</script>

<style scoped>
  .my-card{
    width: 100%;
    max-width: 250px;
    cursor:pointer}
  .my-card:hover{
    background-color: #26a69a;
    color: white;
  }
  .my-card-prog{
    cursor: pointer;
  }
  .my-card-prog:hover{
    background-color: #26a69a;
    color: white;
  }
  h5{
    width: 100%;
    margin: 5px;
  }
  .print-btn{
    width: 200px;
  }

  .col-lotes {
    max-height: 600px;
    overflow-y: scroll;
  }

  .btn-100w{
    width: 100px;
  }
</style>
