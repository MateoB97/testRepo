<template>
  <div>
    <q-page padding>
      <q-dialog v-model="showItemModal" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" style="height: 400px; max-width: 800px" container class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <div class="layout-padding">
              <h3>{{ showItem.nombre }}</h3>
              <div class="overflow-hidden">
                <div class="row q-col-gutter-sm">
                  <div class="col-12">
                    <p><strong>Grupo:</strong> {{ showItem.prodGrupo_id.nombre }}</p>
                    <p><strong>Subgrupo:</strong> {{ showItem.prod_subgrupo_id.nombre }}</p>
                    <p><strong>Codigo:</strong> {{ showItem.codigo }}</p>
                    <p><strong>Impuesto:</strong> {{ showItem.gen_iva_id.nombre }}</p>
                    <p><strong>Precios:</strong></p>
                    <ul v-for="precios in showItem.precios" :key="precios.prodListaPrecio_nombre">
                      <li><p>{{ precios.prodListaPrecio_nombre }} = ${{ precios.precio }} </p></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>

      <q-dialog v-model="openedPrecios" persistent :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" style="height: 400px; max-width: 800px" container class="bg-white">

          <q-page-container>
            <q-page padding>
              <div class="layout-padding">
              <h3>Agregar Precio</h3>
              <div class="overflow-hidden">
                <div class="row q-col-gutter-sm">
                  <div class="col-6">
                    <q-select
                        label="Seleccione la lista de precios"
                        v-model="tempPrecio.prod_lista_precio"
                        :options="listaprecios"
                        option-value="id"
                        option-label="nombre"
                      />
                  </div>
                  <div class="col" style="position:relative;">
                    <p class="v-money-label"> Valor: </p>
                    <money v-model="tempPrecio.precio" v-bind="money" class="v-money"></money>
                  </div>
                </div>
              </div>
              <q-btn v-if="!showForUpdatePrecio" class="q-mt-sm"
                color="primary"
                v-close-popup
                label="Guardar"
                @click="addPrecio"
              />
              <q-btn v-if="showForUpdatePrecio" class="q-mt-sm"
                  color="primary"
                  v-close-popup
                  label="Guardar Edición"
                  @click="saveEditPrecio()"
                />
                <q-btn class="q-mt-md q-ml-sm"
                  color="negative"
                  @click="closePrecios"
                  label="Cancelar"
                />
              </div>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>

      <q-dialog v-model="openedVencimiento" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <h3>Agregar Vencimiento</h3>
            <div class="overflow-hidden">
              <div class="row q-col-gutter-sm">
                <div class="col-6">
                  <q-select
                      label="Seleccione tipo de almacenamiento"
                      v-model="tempVencimiento.prod_almacenamiento"
                      :options="almacenamientos"
                      option-value="id"
                      option-label="nombre"
                    />
                </div>
                <div class="col">
                  <q-input color="primary" type="number" v-model="tempVencimiento.dias_vencimiento" label="Ingrese los días de vencimiento">
                  </q-input>
                </div>
              </div>
            </div>
            <q-btn v-if="!showForUpdateVencimiento" class="q-mt-sm"
              color="primary"
              v-close-popup
              label="Guardar"
              @click="addVencimiento"
            />
            <q-btn v-if="showForUpdateVencimiento" class="q-mt-sm"
                  color="primary"
                  v-close-popup
                  label="Guardar Edición"
                  @click="saveEditVencimiento(tempVencimiento.id)"
                />
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>

      <div class="div-86">
        <h3>Crear Producto</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col-3">
                    <q-select
                      label="Seleccione Grupo Padre"
                      v-model="storeItems.prodGrupo_id"
                      @input="selectedGrupo()"
                      :options="grupos"
                      option-value="id"
                      option-label="nombre"
                    />
                </div>
                <div v-if="showSubgrupos" class="col-3">
                    <q-select
                      label="Seleccione Subgrupo Padre"
                      v-model="storeItems.prod_subgrupo_id"
                      option-value="id"
                      option-label="nombre"
                      :options="subgrupos"
                      @input="selectedSubgrupo()"
                    />
                </div>
                <div v-if="showProducto" class="col-2">
                    <q-input v-model="storeItems.codigo" label="Codigo"/>
                </div>
                <div v-if="showProducto" class="col-2">
                    <q-input v-model="storeItems.cod_prod_padre" label="Codigo Padre"/>
                </div>
                <div v-if="showProducto" class="col-2">
                    <q-select
                      label="Seleccione Unidades"
                      v-model="storeItems.gen_unidades_id"
                      :options="unidades"
                      option-value="id"
                      option-label="nombre"
                    />
                </div>
            </div>
            <div v-if="showProducto" class="row q-col-gutter-sm q-mt-sm">
                <div v-if="showProducto" class="col-4">
                    <q-input v-model="storeItems.nombre" label="Nombre del producto"/>
                </div>
                <div class="col-2">
                    <q-select
                      label="Seleccione impuestos"
                      v-model="storeItems.gen_iva_id"
                      :options="impuestos"
                      option-value="id"
                      option-label="nombre"
                    />
                </div>
                <div class="col-2" >
                    <q-input v-model="storeItems.cod_ean_13" label="Codigo EAN13"/>
                </div>
                <div class="col-2" v-if="showProducto && (this.$store.state.jhsoft.tipo_licencia === 4 || this.$store.state.jhsoft.tipo_licencia === 5)" >
                    <q-input v-model="storeItems.unid_por_animal" label="Unid por animal"/>
                </div>
                <div class="col-2">
                  <q-select
                    v-model="storeItems.cuenta_contable_venta_id"
                    use-input
                    autofocus
                    hide-selected
                    fill-input
                    option-value="id"
                    option-label="nombre"
                    label="Cuenta contable"
                    option-disable="inactive"
                    input-debounce="0"
                    :options="options.genpuc"
                    @filter="filterGenpuc"
                  >
                      <template v-slot:no-option>
                      <q-item>
                          <q-item-section class="text-grey">
                          No results
                          </q-item-section>
                      </q-item>
                      </template>
                      <template v-slot:option="scope">
                      <q-item
                          v-bind="scope.itemProps"
                          v-on="scope.itemEvents"
                      >
                          <q-item-section>
                          <q-item-label v-html="scope.opt.codigo + ' - ' + scope.opt.nombre" />
                          </q-item-section>
                      </q-item>
                      </template>
                    </q-select>
                </div>
            </div>
            <div v-if="showProducto" class="row q-col-gutter-sm q-mt-sm">
                <div class="col-6">
                  <div class="col-12 text-center">
                    <q-btn class="btn-limon" icon-right="add" v-on:click="openedPrecios = true" label="Lista de precios" />
                  </div>
                  <div class="col-12">
                    <div class="col-5 q-ma-sm">
                      <template>
                        <q-table
                          :data="storeItems.precios"
                          :columns="ColumnsPrecios"
                          row-key="prodListaPrecio_id"
                        >
                        <q-td slot="body-cell-valor" slot-scope="props" :props="props">
                          {{ props.value | toMoney }}
                        </q-td>
                        <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                          <q-btn class="q-ml-xs btn-coral" icon-right="delete"  v-on:click="eliminarFilaPrecios(props.value)">Eliminar</q-btn>
                          <q-btn class="q-ml-xs btn-naranja" icon-right="edit" v-on:click="editPrecio(props.value)">Editar</q-btn>
                        </q-td>
                        </q-table>
                      </template>
                    </div>
                  </div>
                </div>
                <div v-if="this.$store.state.jhsoft.lotes === true" class="col-6">
                  <div class="col-12 text-center">
                    <q-btn class="btn-limon" icon-right="add" v-on:click="openedVencimiento = true" label="Vencimiento" />
                  </div>
                  <div class="col-12">
                    <div class="col-5 q-ma-sm">
                      <template>
                        <q-table
                          :data="storeItems.vencimientos"
                          :columns="ColumnsVencimientos"
                          row-key="prodAlmacenamiento_id"
                        >
                        <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                          <q-btn class="q-ml-xs btn-coral" icon-right="delete" v-on:click="eliminarFilaVencimientos(props.value)">Eliminar</q-btn>
                          <q-btn class="q-ml-xs btn-naranja" icon-right="edit" v-on:click="editVencimiento(props.value)">Editar</q-btn>
                        </q-td>
                        </q-table>
                      </template>
                    </div>
                  </div>
                </div>
            </div>
            <div v-if="showProducto" class="row q-col-gutter-sm text-right">
                <div class="col-12">
                  <q-btn v-if="!showForUpdate" class="btn-azul" v-on:click="globalValidate('guardar')" label="Guardar" />
                  <q-btn v-if="showForUpdate" class="btn-azul" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
                </div>
            </div>
        </div>
      </div>
        <div class="row q-mt-xl">
          <div class="col-12">
            <q-table
                title="Listado de productos"
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
                    <q-btn v-if="goblalValidarEstado(props.value) == 0" class="q-ml-xs btn-azul" icon-right="add" v-on:click="globalValidate('activar', props.value)">Activar</q-btn>
                    <q-btn v-if="goblalValidarEstado(props.value) == 1" class="q-ml-xs btn-coral" icon-right="remove" v-on:click="globalValidate('inactivar', props.value)">Desactivar</q-btn>
                    <q-btn class="q-ml-xs btn-naranja" icon-right="edit" v-on:click="preEditar(props.value)">Editar</q-btn>
                    <q-btn class="q-ml-xs btn-limon" icon-right="remove_red_eye" v-on:click="getShowItem(props.value)">Ver</q-btn>
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
import { Money } from 'v-money'

export default {
  name: 'Productos',
  components: {
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
      showForUpdate: false,
      urlAPI: 'api/productos/items',
      impuestos: [],
      unidades: [],
      showSubgrupos: false,
      showProducto: false,
      optionsVisibleMarinacion: [
        { label: 'Si',
          value: 1
        },
        {
          label: 'No',
          value: 0
        },
        {
          label: 'N/A',
          value: 2
        }
      ],
      storeItems: {
        nombre: null,
        prod_subgrupo_id: null,
        codigo: null,
        precios: [],
        gen_iva_id: null,
        gen_unidades_id: null,
        cod_prod_padre: '',
        vencimientos: []
      },
      showAnimales: true,
      showItemModal: false,
      showForUpdatePrecio: false,
      showItem: {
        prod_subgrupo_id: {},
        prodGrupo_id: {},
        gen_iva_id: {},
        gen_unidades_id: {}
      },
      showForUpdateVencimiento: false,
      openedPrecios: false,
      tempPrecio: {
        prodListaPrecio_id: null,
        precio: 0,
        prodListaPrecio_nombre: null
      },
      tempVencimiento: {
        prodAlmacenamiento_id: null,
        prodAlmacenamiento_nombre: null,
        dias_vencimiento: null
      },
      openedVencimiento: false,
      almacenamientos: [],
      precio_counter: 0,
      listaprecios: [],
      ColumnsVencimientos: [
        { name: 'almacenamiento', required: true, label: 'Almacenamiento', align: 'left', field: 'prodAlmacenamiento_nombre', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'dias', required: true, label: 'Días', align: 'left', field: 'dias_vencimiento', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      ColumnsPrecios: [
        { name: 'lista', required: true, label: 'Lista', align: 'left', field: 'prodListaPrecio_nombre', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'valor', required: true, label: 'Precio', align: 'left', field: 'precio', sortable: true, classes: 'my-class', style: 'width: 100px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      tableData: [],
      grupos: [],
      subgrupos: [],
      genpuc: [],
      options: {
        genpuc: this.genpuc
      },
      columns: [
        { name: 'codigo', required: true, label: 'Codigo', align: 'left', field: 'codigo', sortable: true, classes: 'my-class', style: 'width: 50px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 150px' },
        { name: 'subgrupo', required: true, label: 'Subgrupo', align: 'left', field: 'subgrupo', sortable: true, classes: 'my-class', style: 'width: 150px' },
        { name: 'grupo', required: true, label: 'Grupo', align: 'left', field: 'grupo', sortable: true, classes: 'my-class', style: 'width: 150px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'subgrupo', 'grupo', 'actions'],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    preEditar (id) {
      this.globalValidate('editar', id)
      this.showAnimales = true
      this.showSubgrupos = true
      this.showProducto = true
    },
    postEdit () {
      var app = this
      this.selectedGrupo()
      for (const prop in app.storeItems) {
        if (app.storeItems[prop] === null) {
          delete app.storeItems[prop]
        }
      }
      this.storeItems.cuenta_contable_venta_id = this.genpuc.find(v => parseInt(v.id) === parseInt(this.storeItems.cuenta_contable_venta_id))
    },
    postSave () {
      this.showSubgrupos = false
      this.showProducto = false
      this.storeItems = {
        nombre: null,
        prod_subgrupo_id: null,
        codigo: null,
        precios: [],
        gen_iva_id: null,
        gen_unidades_id: null,
        vencimientos: []
      }
    },
    preSave () {
      if (this.storeItems.cod_prod_padre === null) {
        delete this.storeItems.cod_prod_padre
      }
      this.storeItems.prod_subgrupo_id = this.storeItems.prod_subgrupo_id.id
      this.storeItems.gen_iva_id = this.storeItems.gen_iva_id.id
      this.storeItems.gen_unidades_id = this.storeItems.gen_unidades_id.id
      if (this.storeItems.cuenta_contable_venta_id == null) {
        delete this.storeItems.cuenta_contable_venta_id
      } else {
        this.storeItems.cuenta_contable_venta_id = this.storeItems.cuenta_contable_venta_id.id
      }
    },
    filterGenpuc (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.genpuc = this.genpuc.filter(v => v.codigo.toLowerCase().indexOf(needle) > -1)
        if (this.options.genpuc.length < 1) {
          this.options.genpuc = this.genpuc.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        }
      })
    },
    addPrecio () {
      const item = this.storeItems.precios.find(v => parseInt(v.prodListaPrecio_id) === parseInt(this.tempPrecio.prod_lista_precio.id))
      if (item) {
        return this.$q.notify({ color: 'negative', message: 'Ya existe una lista de precio Asociada a este producto' })
      }
      this.storeItems.precios.push({
        id: 'nuevo' + this.precio_counter,
        prodListaPrecio_id: this.tempPrecio.prod_lista_precio.id,
        precio: this.tempPrecio.precio,
        prodListaPrecio_nombre: this.tempPrecio.prod_lista_precio.nombre
      })
      this.precio_counter++
      this.tempPrecio.prod_lista_precio = null
      this.tempPrecio.precio = 0
    },
    editPrecio (id) {
      const item = this.storeItems.precios.find(item => item.id === id)
      this.tempPrecio.id = item.id
      this.tempPrecio.precio = item.precio
      this.tempPrecio.prod_lista_precio = item.prod_lista_precio
      this.showForUpdatePrecio = true
      this.openedPrecios = true
    },
    saveEditPrecio (id) {
      var index = null
      this.storeItems.precios.forEach(function (element, i) {
        if (id === element.id) {
          index = i
        }
      })
      this.storeItems.precios.splice(index, 1)
      this.storeItems.precios.push({
        id: this.tempPrecio.id,
        prodListaPrecio_id: this.tempPrecio.prod_lista_precio.id,
        precio: this.tempPrecio.precio,
        prodListaPrecio_nombre: this.tempPrecio.prod_lista_precio.nombre
      })
      this.tempPrecio.precio = 0
      this.showForUpdatePrecio = false
    },
    closePrecios () {
      this.tempPrecio.prod_lista_precio = null
      this.tempPrecio.precio = parseInt(0)
      this.openedPrecios = false
    },
    eliminarFilaPrecios (id) {
      var index = null
      this.$q.dialog({
        message: '¿ Quieres eliminar esta fila ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.storeItems.precios.forEach(function (element, i) {
          if (id === element.id) {
            index = i
          }
        })
        this.storeItems.precios.splice(index, 1)
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    selectedSubgrupo () {
      this.showProducto = true
    },
    selectedGrupo () {
      if (!this.showForUpdate) {
        this.storeItems.prod_subgrupo_id = null
      }
      if (this.storeItems.prodGrupo_id.animal === '0') {
        this.showAnimales = false
        this.storeItems.unid_por_animal = 99
      }
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/productos/subgrupos/grupofilter/' + this.storeItems.prodGrupo_id.id).then(
        function (response) {
          app.subgrupos = response.data
          app.showSubgrupos = true
        }
      ).catch(error => {
        console.log(error.response)
        app.$q.notify('Error al filtrar los subgrupos')
      })
    },
    getShowItem (id) {
      this.$q.loading.show()
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/productos/items/' + id).then(
        function (response) {
          app.showItem = response.data
          app.$q.loading.hide()
          app.showItemModal = true
        }
      ).catch(error => {
        console.log(error.response)
        app.$q.notify('Error al cargar los datos del producto')
      })
    },
    editVencimiento (id) {
      const item = this.storeItems.vencimientos.find(item => item.id === id)
      this.tempVencimiento = item
      this.showForUpdateVencimiento = true
      this.openedVencimiento = true
    },
    eliminarFilaVencimientos (id) {
      var index = null
      this.$q.dialog({
        message: '¿ Quieres eliminar esta fila ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.storeItems.vencimientos.forEach(function (element, i) {
          if (id === element.id) {
            index = i
          }
        })
        this.storeItems.vencimientos.splice(index, 1)
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    addVencimiento () {
      const item = this.storeItems.vencimientos.find(v => parseInt(v.prodAlmacenamiento_id) === parseInt(this.tempVencimiento.prod_almacenamiento.id))
      if (item) {
        return this.$q.notify({ color: 'negative', message: 'Ya existe una lista de almacenamiento Asociada a este producto' })
      }
      this.storeItems.vencimientos.push({
        id: 'nuevo' + this.vencimientos_counter,
        prodAlmacenamiento_id: this.tempVencimiento.prod_almacenamiento.id,
        dias_vencimiento: this.tempVencimiento.dias_vencimiento,
        prodAlmacenamiento_nombre: this.tempVencimiento.prod_almacenamiento.nombre
      })
      this.vencimientos_counter++
      this.tempVencimiento.prod_almacenamiento = null
      this.tempVencimiento.dias_vencimiento = null
    },
    saveEditVencimiento (id) {
      var index = null
      this.storeItems.vencimientos.forEach(function (element, i) {
        if (id === element.id) {
          index = i
        }
      })
      this.storeItems.vencimientos.splice(index, 1)
      this.storeItems.vencimientos.push({
        id: this.tempVencimiento.id,
        prodAlmacenamiento_id: this.tempVencimiento.prod_almacenamiento.id,
        dias_vencimiento: this.tempVencimiento.dias_vencimiento,
        prodAlmacenamiento_nombre: this.tempVencimiento.prod_almacenamiento.nombre
      })
      this.tempVencimiento.prod_almacenamiento = null
      this.tempVencimiento.dias_vencimiento = null
      this.showForUpdateVencimiento = false
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/generales/unidades/estado/activos', 'unidades')
    this.globalGetForSelect('api/generales/iva', 'impuestos')
    this.globalGetForSelect('api/productos/listadeprecios/estado/activos', 'listaprecios')
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
    this.globalGetForSelect('api/productos/almacenamiento/estado/activos', 'almacenamientos')
    this.globalGetForSelect('api/generales/genpuc', 'genpuc')
  },
  computed: {

  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
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
