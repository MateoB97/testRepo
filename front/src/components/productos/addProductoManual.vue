<template>
  <div>
    <q-dialog
        v-model="isVisible"
        persistent
        transition-show="slide-up"
        transition-hide="slide-down"
        full-width
        >
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
        <q-page-container>
        <q-page padding>
            <h3>Agregar Producto</h3>
            <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div  class="col-1">
                <q-input v-model="codigo_producto" label="Cod." readonly>
                </q-input>
                </div>
                <div class="col">
                <q-select
                    ref="selectProductoManual"
                    v-model="producto_selected"
                    use-input
                    autofocus
                    hide-selected
                    fill-input
                    option-value="id"
                    option-label="nombre"
                    label="Producto"
                    option-disable="inactive"
                    input-debounce="0"
                    :options="options.productos"
                    @filter="filterProductoManual"
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
                <div v-if="producto_selected" class="col">
                <q-input v-if="parseInt(producto_selected.unidades) === 1" color="primary" type="number" v-model="temp.cantidad" label="Cantidad" ref="cantidad" v-on:keyup.enter="() => $refs.precio.focus()"/>
                <q-input v-if="parseInt(producto_selected.unidades) !== 1" color="primary" type="number" v-model="temp.cantidad_unid" label="Cantidad Und" ref="cantidad" v-on:keyup.enter="() => $refs.precio.focus()"/>
                </div>
                <div v-if="withPrice == 1" class="col" style="position:relative">
                  <p class="v-money-label" style="top:25px"> Valor: </p>
                  <money v-model="precio_producto" v-bind="money" class="v-money"></money>
                </div>
            </div>
            </div>
        <q-btn class="q-mt-md"
            color="primary"
            label="Guardar"
            @click="addProducto"
        />
        <q-btn class="q-mt-md q-ml-sm"
            color="negative"
            @click="closeAddProducto"
            label="Cancelar"
        />
        </q-page>
        </q-page-container>
    </q-layout>
    </q-dialog>
    <q-btn class="btn-naranja w-100" icon-right="add" v-on:click="isVisible = true" label="Agregar Producto" />
  </div>
</template>
<script>

import { Money } from 'v-money'
import { globalFunctions } from 'boot/mixins.js'

export default {
  components: {
    Money
  },
  props: ['openedAddProducto', 'withPrice', 'listadoPrecios'],
  name: 'AddProductoManual',
  data () {
    return {
      money: {
        decimal: ',',
        thousands: '.',
        prefix: '          $ ',
        suffix: '',
        precision: 0,
        masked: false
      },
      alert: false,
      dialog: false,
      isVisible: false,
      maximizedToggle: true,
      producto_selected: null,
      precio_producto: 0,
      productos: [],
      options: {
        productos: this.productos
      },
      temp: {
        cantidad: 0
      },
      dataNotas: []
    }
  },
  mixins: [globalFunctions],
  methods: {
    filterProductoManual (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productos = this.productos.filter(v => v.codigo.toLowerCase().indexOf(needle) > -1)
        if (this.options.productos.length < 1) {
          this.options.productos = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        }
      })
    },
    closeAddProducto () {
      this.producto_selected = null
      this.temp.cantidad = null
      this.precio_producto = parseInt(0)
      this.stopGetPeso()
      this.isVisible = false
    },
    stopGetPeso () {
      clearInterval(this.interval)
    },
    addProducto () {
      var app = this
      var cantValidate = null
      if (parseInt(this.producto_selected.unidades) === 1) {
        cantValidate = app.temp.cantidad
      } else {
        cantValidate = app.temp.cantidad_unid
      }
      if (cantValidate === null || parseFloat(cantValidate) === 0) {
        app.$q.notify({ color: 'negative', message: 'La cantidad debe ser diferente de 0.' })
        app.$refs.cantidad.focus()
      } else if (parseInt(app.precio_producto) === 0 && app.withPrice === 1) {
        app.$q.notify({ color: 'negative', message: 'El precio debe ser diferente de 0.' })
      } else {
        if (app.listadoPrecios.length === 0 && app.withPrice === 1) {
          app.$q.notify({ color: 'negative', message: 'Se debe seleccionar un cliente o un despacho para cargar el listado de precios.' })
        } else {
          var newProduct = {}
          newProduct = {
            id: app.itemsCounter,
            producto: app.producto_selected.nombre,
            producto_id: app.producto_selected.id,
            producto_codigo: app.producto_selected.codigo,
            precio: parseInt(app.precio_producto) / (1 + (parseInt(app.producto_selected.impuesto) / 100)),
            iva: app.producto_selected.impuesto,
            gen_iva_id: app.producto_selected.gen_iva_id,
            desc: 0.00,
            descporcentaje: 0.00,
            despacho: false
          }
          if (parseInt(app.producto_selected.unidades) === 1) {
            newProduct.cantidad = app.temp.cantidad
          } else {
            newProduct.cantidad = app.temp.cantidad_unid
          }
          app.isVisible = false
          app.$emit('addProducto', newProduct)
          app.itemsCounter = app.itemsCounter + 1
          app.numLineas = app.numLineas + 1
          app.producto_selected = null
          app.precio_producto = parseInt(0)
          app.temp.cantidad = null
          app.temp.cantidad_unid = null
        }
      }
    }
  },
  computed: {
    codigo_producto: function () {
      if (this.producto_selected) {
        return this.producto_selected.codigo
      } else {
        return null
      }
    }
  },
  created: function () {
    if (this.productos.length < 1) {
      this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
    }
  },
  watch: {
  }
}
</script>

<style scoped>

</style>>
