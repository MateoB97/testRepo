<template>
  <div>
    <q-page padding>

      <q-dialog v-model="showReimprimir" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <h3>Seleccione Impresora</h3>
            <div class="overflow-hidden">
              <div class="row q-col-gutter-sm">
                    <div class="col">
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
              </div>
            </div>
            <q-btn class="q-mt-sm"
              color="primary"
              v-close-popup
              label="Reimprimir"
              @click="reimprimir"
            />
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>

      <q-dialog v-model="showDividir" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <h3>Seleccione Impresora</h3>
            <div class="overflow-hidden">
              <div class="row q-col-gutter-sm">
                    <div class="col">
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
                    <div class="col">
                      <q-input color="primary" type="number" v-model="temp.cantidad" label="Cantidad" ref="cantidad" v-on:keyup.enter="() => $refs.precio.focus()">
                        </q-input>
                    </div>
              </div>
            </div>
            <q-btn class="q-mt-sm"
              color="primary"
              v-close-popup
              label="Dividir Canasta"
              @click="dividirCanasta"
            />
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>

        <div v-if="showForUpdate">
            <div class="overflow-hidden">
                <div class="row q-col-gutter-sm">
                    <div class="col-4">
                        <q-select
                        label="Seleccione Grupo Padre"
                        v-model="storeItems.prodGrupo_id"
                        @input="selectedGrupo()"
                        :options="grupos"
                        option-value="id"
                        option-label="nombre"
                        />
                    </div>
                    <div class="col-4">
                        <q-select
                        label="Seleccione Subgrupo Padre"
                        v-model="storeItems.prodSubgrupo_id"
                        option-value="id"
                        option-label="nombre"
                        :options="subgrupos"
                        @input="selectedSubgrupo()"
                        />
                    </div>
                    <div class="col-4">
                        <q-select
                        label="Seleccione Subgrupo Padre"
                        v-model="storeItems.producto_id"
                        option-value="id"
                        option-label="nombre"
                        :options="productos"
                        @input="selectedProducto()"
                        />
                    </div>
                </div>
            </div>
        </div>
        <div v-if="showForUpdate" class="row q-mt-md q-col-gutter-sm">
            <div class="col-3">
                <q-btn v-if="showForUpdate" color="primary" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
            </div>
        </div>
        <div class="row q-mt-xl q-col-gutter-sm">
          <div class="col-2">
            <div class="row q-col-gutter-sm">
              <div class="col-12">
                <h3>Filtros</h3>
              </div>
              <div class="col-12">
                <q-btn color="negative" v-on:click="eliminarFiltro()" label="Limpiar filtros" />
              </div>
            </div>
            <div class="row">
              <q-input class="w-100" color="primary" type="number" v-model="filtros.lote" label="Lote"/>
            </div>
            <div class="row">
              <q-input class="w-100 no-pd" label="Fecha Inicial" v-model="filtros.fecha_empaque_inicial" mask="date" :rules="['date']">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy ref="qDateProxy" transition-show="scale" transition-hide="scale">
                      <q-date v-model="filtros.fecha_empaque_inicial" @input="() => $refs.qDateProxy.hide()" />
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
            <div class="row">
              <q-input class="w-100 no-pd" label="Fecha Final" v-model="filtros.fecha_empaque_final" mask="date" :rules="['date']">
                <template v-slot:append>
                  <q-icon name="event" class="cursor-pointer">
                    <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                      <q-date v-model="filtros.fecha_empaque_final" @input="() => $refs.qDateProxy1.hide()" />
                    </q-popup-proxy>
                  </q-icon>
                </template>
              </q-input>
            </div>
            <div class="row">
              <q-select
                class="w-100"
                v-model="filtros.producto"
                use-input
                hide-selected
                fill-input
                option-value="id"
                option-label="nombre"
                label="Producto"
                option-disable="inactive"
                emit-value
                map-options
                input-debounce="0"
                :options="optionsProductos"
                @filter="filterFn"
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
            <div class="row">
              <div class="q-gutter-sm">
                <q-radio v-model="filtros.estado" val="null" label="Todos" />
                <q-radio v-model="filtros.estado" val="1" label="En inventario" />
                <q-radio v-model="filtros.estado" val="0" label="Despachado" />
              </div>
            </div>
            <div class="row q-gutter-sm">
              <div class="col-12 col-md-6">
                <q-btn color="primary" v-on:click="filtrar()" label="Filtrar" />
              </div>
            </div>
          </div>
          <div class="col-10">
            <q-table
                title= 'INVENTARIO'
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

                <q-td slot="body-cell-peso" slot-scope="props" :props="props">
                    {{ globalFormatPeso(props.value) }}
                </q-td>

                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <q-btn class="q-ml-xs" icon="edit" v-on:click="globalValidate('editar', props.value)" color="warning"></q-btn>
                    <q-btn class="q-ml-xs" v-if="globalValidarPermiso('73')" icon="delete" v-on:click="globalValidate('eliminar', props.value)" color="negative"></q-btn>
                    <q-btn class="q-ml-xs" icon="remove_red_eye" v-on:click="validateReimprimir(props.value)" color="positive"></q-btn>
                    <q-btn class="q-ml-xs" v-on:click="showDividir = true, temp.cantidad = null, canastaId = props.value" color="primary">Dividir</q-btn>
                </q-td>
            </q-table>
          </div>
        </div>
    </q-page>
  </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'ListadoInventario',
  data: function () {
    return {
      urlAPI: 'api/inventario/produccion',
      storeItems: {
        producto_id: null,
        prodGrupo_id: null,
        prodSubgrupo_id: null,
        impresora: null,
        etiqueta: null
      },
      filtros: {
        lote: null,
        fecha_empaque_inicial: null,
        fecha_empaque_final: null,
        estado: null,
        producto: null
      },
      productosFiltro: [],
      canastaId: null,
      optionsProductos: [],
      impresoras: [],
      tempId: null,
      temp: {
        cantidad: null
      },
      showDividir: false,
      showReimprimir: false,
      grupos: [],
      subgrupos: [],
      productos: [],
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'peso', align: 'left', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'marca', required: true, label: 'marca', align: 'left', field: 'marca', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'lote', required: true, label: 'lote', align: 'left', field: 'lote', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'despacho', required: true, label: 'Despacho', align: 'left', field: 'despacho', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_sacrificio', required: true, label: 'fecha_sacrificio', align: 'left', field: 'fecha_sacrificio', sortable: true, classes: 'my-class', style: 'width: 200px' },
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
    },
    preSave () {
      this.storeItems.producto_id = this.storeItems.producto_id.id
    },
    postEdit () {
      this.selectedGrupo(this.storeItems.prodGrupo_id.id)
      this.selectedSubgrupo(this.storeItems.prodSubgrupo_id.id)
    },
    async selectedSubgrupo () {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/items/subgrupofilter/' + this.storeItems.prodSubgrupo_id.id)
        this.productos = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los productos!' })
      } finally {
      }
    },
    async selectedGrupo () {
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/subgrupos/grupofilter/' + this.storeItems.prodGrupo_id.id)
        this.subgrupos = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los subgrupos!' })
      } finally {
      }
    },
    async filtrar () {
      var date1 = null
      var date2 = null
      if (this.filtros.fecha_empaque_inicial !== null) {
        date1 = this.filtros.fecha_empaque_inicial.toString().replace('/', '-')
        date1 = date1.replace('/', '-')
      }
      if (this.filtros.fecha_empaque_final !== null) {
        date2 = this.filtros.fecha_empaque_final.toString().replace('/', '-')
        date2 = date2.replace('/', '-')
      }

      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/inventario/indexfilter/' + this.filtros.lote + '/' + date1 + '/' + date2 + '/' + this.filtros.producto + '/' + this.filtros.estado)
        this.tableData = data.data
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los productos!' })
      } finally {
      }
    },
    async eliminarFiltro () {
      this.globalGetItems()
      this.filtros = {
        lote: null,
        fecha_empaque_inicial: null,
        fecha_empaque_final: null,
        estado: null,
        producto: null
      }
    },
    validateReimprimir (id) {
      this.$q.dialog({
        message: '¿ Quieres reimprimir este item ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.showReimprimir = true
        this.storeItems.etiqueta = id
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    async reimprimir () {
      try {
        let data = await axios.post(this.$store.state.jhsoft.url + 'api/inventario/reimprimir', this.storeItems)
        if (data.data === 'done') {
          this.$q.notify({ type: 'positive', message: 'Etiqueta Reimpresa!' })
        } else {
          this.$q.notify({ type: 'negative', message: 'Hubo un error al imprimir!' })
        }
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al imprimir!' })
      } finally {
      }
    },
    filterFn (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.optionsProductos = this.productosFiltro.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    dividirCanasta () {
      var app = this
      var data = {
        impresora: app.storeItems.impresora,
        cantidad: app.temp.cantidad,
        canasta: app.canastaId
      }
      axios.post(this.$store.state.jhsoft.url + 'api/inventario/dividircanasta', data).then(
        function (response) {
          if (response.data[0] === 'done') {
            app.globalGetForSelect('api/inventario/produccion', 'tableData')
            app.storeItems.etiqueta = response.data[1]
            app.reimprimir()
            app.storeItems.etiqueta = response.data[2]
            app.reimprimir()
          } else {
            app.$q.notify({ type: 'negative', message: 'Hubo un error al dividir!' })
          }
        }
      )
    }
  },
  created: function () {
    this.globalGetForSelect('api/inventario/produccion', 'tableData')
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productosFiltro')
    this.globalGetForSelect('api/generales/impresoras', 'impresoras')
  },
  computed: {
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
    h3{
      margin: 0px;
    }
    .w-100{
      width: 100%;
    }
    .no-pd{
      padding: 0px;
    }
</style>
