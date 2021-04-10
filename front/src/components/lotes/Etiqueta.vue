<template>
    <div>
        <q-page padding>
            <div class="row q-col-gutter-md">
                <div class="col-3">
                    <q-select
                      v-if="type == 0"
                      label="Seleccione tipo de almacenamiento"
                      v-model="storeItems.prodAlmacenamiento_id"
                      :options="almacenamientos"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                      emit-value
                      map-options
                    />
                    <q-input v-if="type == 1" label="Fecha de Vencimiento" v-model="storeItems.fecha_vencimiento" mask="date" :rules="['date']">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                            <q-date v-model="storeItems.fecha_vencimiento" @input="() => $refs.qDateProxy2.hide()" />
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
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
              <div class="col-3">
                <q-input v-model="datos.lote_id" label="Buscar por Lote" />
              </div>
              <div class="col-4 self-center">
                <q-btn class="btn-100w" :class="classActive == 'Lote' ? 'bg-positive' : ''" color="primary" v-on:click="getPorLote(), classActive = 'Lote'" label="Buscar" />
              </div>
            </div>
            <div class="row q-mt-md q-col-gutter-md">
                <div class="col-5 col-lotes" >
                    <div class="q-pa-md q-gutter-md">
                        <q-card class="my-card-prog" v-for="programacion in programaciones" :key="programacion.programacion_id" @click="selectedProgramacion(programacion)">
                            <q-card-section>
                               <p class="no-margin">programación: {{ programacion.programacion_id }}</p>
                               <p v-if="type == 0" class="no-margin">Tercero: {{ programacion.tercero }}</p>
                               <p v-if="type == 0" class="no-margin">Sucursal: {{ programacion.sucursal }}</p>
                               <p v-if="type == 0" class="no-margin">Fecha desposte: {{ programacion.fecha_desposte }}</p>
                               <p class="no-margin">Marca: {{ programacion.marca }}</p>
                               <p class="no-margin">Lote: {{ programacion.lote_id }}   //   Numero animales: {{ programacion.num_animales_programacion }}</p>
                               <p v-if="type == 1" class="no-margin">Lote Tercero: {{ programacion.lote_tercero }}</p>
                            </q-card-section>
                        </q-card>
                    </div>
                </div>
                <div class="col-4 col-lotes">
                    <div v-if="show.subgrupos" class="q-pa-md row items-start q-gutter-md">
                        <q-card class="my-card" v-for="subgrupo in listas.subgrupos" :key="subgrupo.id" @click="selectedSubgrupo(subgrupo)">
                            <q-card-section>
                                <p>{{ subgrupo.nombre }}</p>
                            </q-card-section>
                        </q-card>
                    </div>
                    <div v-if="show.productos" class="q-pa-md row items-start q-gutter-md">
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
                    <div class="row">
                      <div class="col-12">
                          <Bascula
                            ref="basculaComponent"
                            v-model="storeItems.cantidad"
                            :withBasculaSelect="true"
                          />
                      </div>
                    </div>
                    <div class="row q-mt-md">
                      <q-btn color="primary" v-on:click="globalStoreItem(0)" label="Guardar" />
                    </div>
                </div>
            </div>
        </q-page>
    </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'
import Bascula from 'components/generales/BasculasComponent.vue'

export default {
  name: 'LotEmpaque',
  components: {
    Bascula
  },
  props: ['type'],
  data () {
    return {
      urlAPI: 'api/inventario/items',
      basculas: [
        { label: 'Local',
          value: 'http://127.0.0.1:5002/basculas'
        }
      ],
      impresoras: [],
      left: true,
      right: true,
      listas: [],
      almacenamientos: [],
      programaciones: [],
      storeItems: {
        prog_lotes_id: null,
        cantidad: null,
        producto_id: null,
        impresora: null,
        marinado: false
      },
      datos: {
        lote: 'Debe seleccionar una programación.',
        marca: 'Debe seleccionar una programación.',
        grupo: 'Debe seleccionar una programación.',
        subgrupo: 'Debe seleccionar un subgrupos.',
        producto: 'Debe seleccionar un producto.',
        faltantes: 'Debe seleccionar un producto.',
        bascula: null
      },
      classActive: 'Todos',
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
    beforeRouteLeave: function (to, from, next) {
      if (this.$refs.basculaComponent) {
        this.$refs.basculaComponent.stopGetPeso()
      }
      next()
    },
    preSave () {
    },
    postSave () {
      this.cantidad = null
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
      console.log(this.datos.num_animales)
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
      console.log(producto)
      this.storeItems.producto_id = producto.id
      this.datos.producto = producto.nombre
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/inventario/productonprogram/' + producto.id + '/' + this.datos.programacion_id)
        var tempData = data.data
        var existentes = tempData[0].existentes_counter
        this.datos.faltantes = (parseInt(producto.unid_por_animal) * parseInt(this.datos.num_animales)) - parseInt(existentes)
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los productos!' })
      } finally {
      }
    },
    async getPorGrupo (id) {
      this.$q.loading.show()
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/lotes/programaciones/abiertasporgrupo/' + id + '/' + this.type)
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
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/lotes/programaciones/abiertasporlote/' + this.datos.lote_id + '/' + this.type)
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
    console.log(this.type)
    this.globalGetForSelect('api/lotes/programaciones/abiertas/' + this.type, 'programaciones')
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

<style scoped lang="stylus">
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

  .col-lotes {
    max-height: 600px;
    overflow-y: scroll;
  }

  .btn-100w{
    width: 100px;
  }

  .no-margin{
    margin:0px;
  }

</style>
