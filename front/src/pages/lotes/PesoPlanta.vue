<template>
  <div>
    <q-page padding>

        <h3>Peso en Planta</h3>
        <div class="overflow-hidden">
            <div v-if="showSelect" class="row q-col-gutter-sm">
                <div class="col-3">
                    <q-select
                      label="Seleccione Lote"
                      v-model="datos.lote"
                      :options="lotes"
                      @input="selectedLote"
                    >
                        <template v-slot:option="scope">
                            <q-item
                                v-bind="scope.itemProps"
                                v-on="scope.itemEvents"
                            >
                                <q-item-section>
                                <q-item-label v-html="scope.opt.consecutivo" />
                                <q-item-label caption>Marca: {{ scope.opt.marca }} // Num animales: {{ scope.opt.num_animales }} </q-item-label>
                                </q-item-section>
                            </q-item>
                        </template>
                    </q-select>
                </div>
                <div class="col-4">
                  <q-checkbox v-model="datos.cerdox2" label="Cerdos x2" />
                </div>
            </div>
            <div v-if="showDatosLote" class="row q-col-gutter-sm">
                <div class="col-3">
                    <p>Lote: {{ this.datos.consecutivo }}</p>
                </div>
                <div class="col-3">
                    <p>Marca: {{ this.datos.marca }}</p>
                </div>
                <div class="col-3">
                    <p>N° de Animales: {{ this.datos.num_animales }}</p>
                </div>
                <div class="col-3">
                    <p>Grupo: {{ this.datos.grupo }}</p>
                </div>
            </div>
            <div v-if="datos.producto_empacado === '0' || this.datos.producto_empacado === '2'">
              <div v-if="showGrupoCerdo" class="row q-col-gutter-sm">
                  <div class="col-6">
                      <p>Canales Faltantes: {{ this.datos.cerdoFaltante }}</p>
                  </div>
              </div>
              <div v-if="showGrupoRes" class="row q-col-gutter-sm">
                  <div class="col-6">
                      <p>Cuartos Del Faltantes: {{ this.datos.resDelFaltante }}</p>
                  </div>
                  <div class="col-6">
                      <p>Cuartos Tras Faltantes: {{ this.datos.resTrasFaltante }}</p>
                  </div>
              </div>
            </div>

            <q-separator class="q-mt-md q-mb-md" color="orange"/>

            <div v-if="showDatosLote">
              <div class="row q-col-gutter-sm q-mb-md">
                <div class="col-2">
                  <q-input color="primary" type="number" v-model="temp.temperatura" label="Temperatura"></q-input>
                </div>
                <div class="col-2">
                  <q-input color="primary" type="number" v-model="temp.ph" label="PH"></q-input>
                </div>
                <div class="col-2">
                  <q-checkbox v-model="temp.color" label="Color" />
                </div>
                <div class="col-2">
                  <q-checkbox v-model="temp.olor" label="Olor" />
                </div>
                <div class="col-2">
                  <q-checkbox v-model="temp.sin_sustancias" label="Sin sustancias extrañas" />
                </div>
                <div class="col-2">
                  <q-checkbox v-model="temp.cumple" label="Cumple" @input="limpiarObservaciones"/>
                </div>
              </div>
              <div v-if="!temp.cumple" class="row">
                <div style="width:100%">
                  <q-input
                  ref="observaciones"
                  v-model="temp.observaciones"
                  type="textarea"
                />
                </div>
              </div>
            </div>

            <q-separator class="q-mt-md q-mb-md" color="orange"/>

            <div v-if="showDatosLote">
              <div v-if="datos.producto_empacado === '1'"  class="row q-col-gutter-sm q-mb-md">
                <div class="col-3">
                    <q-input ref="peso" v-model="temp.peso" @focus="getPeso" @blur="stopGetPeso" label="Peso" />
                </div>
                <div class="col-2">
                    <q-select
                      label="Seleccione Subgrupo"
                      v-model="datos.prodSubgrupo_id"
                      @input="selectedSubgrupo()"
                      :options="listas.subgrupos"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                      emit-value
                      map-options
                    />
                </div>
                <div class="col-2">
                    <q-select
                      label="Seleccione Producto"
                      v-model="datos.producto"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                      map-options
                      :options="listas.productos"
                    />
                </div>
                <div class="col-2">
                  <q-btn icon="add" v-on:click="addFilaPeso()" color="positive"></q-btn>
                </div>
                <div class="col-3">
                  <q-btn icon="save" v-on:click="validarFaltantes()" label="Guardar" color="primary"></q-btn>
                </div>
              </div>
              <div v-if="datos.producto_empacado === '0' || this.datos.producto_empacado === '2'" class="row q-col-gutter-sm q-mb-md">
                <div class="col-12">
                    <Bascula
                      ref="basculaComponent"
                      v-model="temp.peso"
                      :withBasculaSelect="true"
                      :inicioAutomatico="true"
                    />
                </div>
                <div class="col-3" v-if="showGrupoRes">
                    <div class="q-gutter-sm">
                        <q-radio v-model="temp.pieza" val="delantero" label="1/4 Delantero" />
                        <q-radio v-model="temp.pieza" val="trasero" label="1/4 Trasero" />
                    </div>
                </div>
                <div class="col-3">
                  <q-btn icon="add" v-on:click="addFilaPeso()" color="positive"></q-btn>
                </div>
                <div class="col-3">
                  <q-btn icon="save" v-on:click="validarFaltantes()" label="Guardar" color="primary"></q-btn>
                </div>
              </div>
            </div>
        </div>
        <div v-if="datos.producto_empacado === '0' || this.datos.producto_empacado === '2'">
          <div v-if="showGrupoCerdo" class="row q-mt-xl q-col-gutter-sm">
              <q-table
                  title="Listado de canales"
                  :data="datos.canales"
                  :columns="columns"
                  :separator="separator"
                  row-key="id"
                  color="secondary"
                  table-style="width:100%"
              >
                  <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                      <q-btn icon="delete" v-on:click="eliminarFila('canal', props.value)" color="negative"></q-btn>
                  </q-td>
              </q-table>
          </div>
          <div v-if="showGrupoRes" class="row q-mt-xl q-col-gutter-lg">
            <div class="col-6">
              <q-table
                  title="Listado de 1/4 traseros"
                  :data="datos.traseros"
                  :columns="columns"
                  :separator="separator"
                  row-key="id"
                  color="secondary"
                  table-style="width:100%"
              >
                  <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                      <q-btn icon="delete" v-on:click="eliminarFila('traseros', props.value)" color="negative"></q-btn>
                  </q-td>
              </q-table>
            </div>
            <div class="col-6">
              <q-table
                  title="Listado de 1/4 delanteros"
                  :data="datos.delanteros"
                  :columns="columns"
                  :separator="separator"
                  row-key="id"
                  color="secondary"
                  table-style="width:100%"
              >
                  <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                      <q-btn icon="delete" v-on:click="eliminarFila('delanteros', props.value)" color="negative"></q-btn>
                  </q-td>
              </q-table>
            </div>
          </div>
        </div>
        <!-- <div v-if="datos.producto_empacado === '1' || datos.producto_empacado === '2'"> -->
          <div v-if="datos.producto_empacado === '1'">
          <q-table
              title="Listado de productos"
              :data="storeItems.productos"
              :columns="columnsProductos"
              :separator="separator"
              row-key="id"
              color="secondary"
              table-style="width:100%"
          >
              <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                  <q-btn icon="delete" v-on:click="eliminarFila('canal', props.value)" color="negative"></q-btn>
              </q-td>
          </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'
import Bascula from 'components/generales/BasculasComponent.vue'

export default {
  name: 'PagePesoPlanta',
  components: {
    Bascula
  },
  data: function () {
    return {
      urlAPI: 'api/lotes/pesoplanta',
      titulo: 'Peso Planta',
      showSelect: true,
      showDatosLote: false,
      showGrupoCerdo: false,
      showGrupoRes: false,
      tableData: [],
      grupos: [],
      bascula: {
        stop: false
      },
      groupSelected: [],
      prodGrupo_nombre: null,
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, sort: this.sortFunction, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Peso', align: 'left', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions',
          required: true,
          label: 'Acciones',
          align: 'left',
          field: 'id',
          sortable: true,
          classes: 'my-class',
          style: 'width: 200px'
        }
      ],
      columnsProductos: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'nombre', align: 'left', field: 'nombre_producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Peso', align: 'left', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'grupo', 'actions'],
      separator: 'horizontal',
      filter: '',
      storeItems: {
        lote_id: '',
        productos: []
      },
      listas: {
        lotes: [],
        subgrupos: [],
        productos: []
      },
      lotes: [],
      productos_piezas: [],
      datos: {
        marca: null,
        num_animales: null,
        grupo: null,
        cerdoFaltante: null,
        resDelFaltante: null,
        resTrasFaltante: null,
        delanteros: [],
        traseros: [],
        canales: [],
        producto: null,
        cerdox2: false,
        lote: null
      },
      temp: {
        peso: null,
        pieza: null,
        temperatura: null,
        color: true,
        olor: true,
        sin_sustancias: true,
        cumple: true,
        observaciones: 'Sin Observaciones',
        ph: null
      }
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
      this.showSelect = true
      this.showDatosLote = false
      this.showGrupoRes = false
      this.showGrupoCerdo = false
    },
    async preSave () {
    },
    sortFunction (a, b) {
      a = a.toString()
      b = b.toString()
      a = parseInt(a.replace('nuevo', '')) + 9999999
      b = parseInt(b.replace('nuevo', '')) + 9999999
      return a - b
    },
    validarFaltantes () {
      if (this.datos.producto_empacado === '0' || this.datos.producto_empacado === '2') {
        if (this.datos.grupo === 'Res') {
          if ((this.datos.resTrasFaltante === 0) && (this.datos.resDelFaltante === 0)) {
            this.datos.delanteros.forEach((item) => {
              this.storeItems.productos.push(item)
            })
            this.datos.traseros.forEach((item) => {
              this.storeItems.productos.push(item)
            })
            this.globalValidate('guardar')
          } else {
            this.$q.notify('Faltantes pendientes')
            this.datos.delanteros.forEach((item) => {
              this.storeItems.productos.push(item)
            })
            this.datos.traseros.forEach((item) => {
              this.storeItems.productos.push(item)
            })
            this.globalValidate('guardar')
          }
        } else if (this.datos.grupo === 'Cerdo') {
          if (this.datos.cerdoFaltante === 0) {
            this.datos.canales.forEach((element) => {
              this.storeItems.productos.push(element)
            })
            this.globalValidate('guardar')
          } else {
            this.datos.canales.forEach((element) => {
              this.storeItems.productos.push(element)
            })
            this.$q.notify('Faltantes pendientes')
            this.globalValidate('guardar')
          }
        }
      } else {

      }
    },
    eliminarFila (pieza, id) {
      var index = null
      this.$q.dialog({
        message: '¿ Quieres eliminar esta fila ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        if (this.datos.grupo === 'Res') {
          if (pieza === 'delanteros') {
            this.datos.delanteros.forEach(function (element, i) {
              if (id === element.id) {
                index = i
              }
            })
            this.datos.delanteros.splice(index, 1)
            this.datos.resDelFaltante++
          } else {
            this.datos.traseros.forEach(function (element, i) {
              if (id === element.id) {
                index = i
              }
            })
            this.datos.traseros.splice(index, 1)
            this.datos.resTrasFaltante++
          }
        } else {
          this.datos.canales.forEach(function (element, i) {
            if (id === element.id) {
              index = i
            }
          })
          this.datos.canales.splice(index, 1)
          this.datos.cerdoFaltante++
        }
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    addFilaPeso () {
      console.log(this.temp.peso)
      if ((this.temp.peso > 0) && (this.temp.peso !== null)) {
        if (this.datos.producto_empacado === '0' || this.datos.producto_empacado === '2') {
          if (this.datos.grupo === 'Res') {
            if (this.temp.pieza === 'delantero') {
              console.log('hola')
              if (this.datos.resDelFaltante > 0) {
                this.datos.delanteros.push({
                  id: 'nuevo' + parseInt(this.datos.delanteros.length),
                  peso: this.temp.peso,
                  producto_id: this.productos_piezas.prod_cuarto_dela,
                  temperatura: this.temp.temperatura,
                  ph: this.temp.ph,
                  color: this.temp.color,
                  olor: this.temp.olor,
                  sin_sustancias: this.temp.sin_sustancias,
                  cumple: this.temp.cumple,
                  observaciones: this.temp.observaciones
                })
                this.datos.resDelFaltante--
              } else {
                this.$q.notify('1/4 delanteros completos')
              }
            } else {
              if (this.datos.resTrasFaltante > 0) {
                this.datos.traseros.push({
                  id: 'nuevo' + parseInt(this.datos.traseros.length),
                  peso: this.temp.peso,
                  producto_id: this.productos_piezas.prod_cuarto_tras,
                  temperatura: this.temp.temperatura,
                  ph: this.temp.ph,
                  color: this.temp.color,
                  olor: this.temp.olor,
                  sin_sustancias: this.temp.sin_sustancias,
                  cumple: this.temp.cumple,
                  observaciones: this.temp.observaciones
                })
                this.datos.resTrasFaltante--
              } else {
                this.$q.notify('1/4 traseros completos')
              }
            }
          } else if (this.datos.grupo === 'Cerdo') {
            // this.datos.canales.push({
            //   id: 'nuevo' + parseInt(this.datos.canales.length),
            //   peso: this.temp.peso,
            //   producto_id: this.datos.productos_piezas[0].prod_canal,
            //   temperatura: this.temp.temperatura,
            //   ph: this.temp.ph,
            //   color: this.temp.color,
            //   olor: this.temp.olor,
            //   sin_sustancias: this.temp.sin_sustancias,
            //   cumple: this.temp.cumple,
            //   observaciones: this.temp.observaciones
            // })
            if (this.datos.cerdoFaltante > 0) {
              this.datos.canales.push({
                id: 'nuevo' + parseInt(this.datos.canales.length),
                peso: this.temp.peso,
                producto_id: this.productos_piezas.prod_canal,
                temperatura: this.temp.temperatura,
                ph: this.temp.ph,
                color: this.temp.color,
                olor: this.temp.olor,
                sin_sustancias: this.temp.sin_sustancias,
                cumple: this.temp.cumple,
                observaciones: this.temp.observaciones
              })
              this.datos.cerdoFaltante--
            } else {
              this.$q.notify('canales completas')
            }
          }
        } else {
          this.storeItems.productos.push({
            id: 'nuevo' + parseInt(this.storeItems.productos.length),
            peso: this.temp.peso,
            producto_id: this.datos.producto.id,
            nombre_producto: this.datos.producto.nombre,
            temperatura: this.temp.temperatura,
            ph: this.temp.ph,
            color: this.temp.color,
            olor: this.temp.olor,
            sin_sustancias: this.temp.sin_sustancias,
            cumple: this.temp.cumple,
            observaciones: this.temp.observaciones
          })
        }
        this.temp.peso = null
        this.temp.temperatura = null
        this.temp.ph = null
      } else {
        this.$q.notify('El peso debe ser mayor a 0')
      }
    },
    async selectedLote () {
      this.datos.traseros = []
      this.datos.delanteros = []
      this.datos.canales = []
      this.storeItems.productos = []
      this.showGrupoRes = false
      this.showGrupoCerdo = false
      this.datos.marca = this.datos.lote.marca
      this.datos.num_animales = this.datos.lote.num_animales
      this.datos.grupo = this.datos.lote.grupo
      this.datos.grupo_id = this.datos.lote.grupo_id
      this.datos.producto_empacado = this.datos.lote.producto_empacado
      this.datos.consecutivo = this.datos.lote.consecutivo
      this.storeItems.lote_id = this.datos.lote.id
      this.datos.lote = this.datos.lote.consecutivo
      console.log('StoreItems =' + this.storeItems)
      this.showDatosLote = true
      // mostrar faltantes
      if (this.datos.grupo === 'Cerdo') {
        this.showGrupoCerdo = true
        if (this.datos.cerdox2 === true) {
          this.datos.cerdoFaltante = this.datos.num_animales * 2
        } else {
          this.datos.cerdoFaltante = this.datos.num_animales
        }
      } else if (this.datos.grupo === 'Res') {
        this.showGrupoRes = true
        this.datos.resDelFaltante = this.datos.num_animales * 2
        this.datos.resTrasFaltante = this.datos.num_animales * 2
      }
      // mostrar subgrupos si son productos terminados
      if (this.datos.producto_empacado) {
        this.getSubgrupos()
      }
      // se busca si hay pesos guardados para el lote seleccionado
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/lotes/pesoplanta/' + this.storeItems.lote_id)
        var tempData = data.data
        if (tempData.length > 0) {
          tempData.forEach((element) => {
            if (element.producto_id === this.productos_piezas.prod_cuarto_tras) {
              this.datos.traseros.push(element)
              this.datos.resTrasFaltante--
            } else if (element.producto_id === this.productos_piezas.prod_cuarto_dela) {
              this.datos.delanteros.push(element)
              this.datos.resDelFaltante--
            } else if (element.producto_id === this.productos_piezas.prod_canal) {
              this.datos.canales.push(element)
              this.datos.cerdoFaltante--
            }
          })
        }
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al obtener informacion del lote!' })
      } finally {
      }
    },
    async selectedSubgrupo () {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/items/subgrupofilter/' + this.datos.prodSubgrupo_id)
        var tempData = data.data
        this.listas.productos = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los productos!' })
      } finally {
      }
    },
    async getSubgrupos () {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/subgrupos/grupofilter/' + this.datos.grupo_id)
        var tempData = data.data
        this.listas.subgrupos = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los subgrupos!' })
      } finally {
      }
    },
    limpiarObservaciones () {
      this.storeItems.observaciones = 'Sin Observaciones'
    }
  },
  created: function () {
    this.globalGetForSelect('api/lotes/items', 'lotes')
    this.globalGetForSelect('api/generales/generalidades', 'productos_piezas')
  },
  computed: {
  },
  mounted: function () {
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
</style>
