<template>
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
                <div class="col" style="position:relative">
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
</template>
<script>

import { Money } from 'v-money'

export default {
  components: {
    Money
  },
  props: ['openedAddProducto'],
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
      precio_producto: null,
      productos: [],
      options: {
        productos: this.productos
      },
      dataNotas: []
    }
  },
  methods: {
    filterProductoManual (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productos = this.productos.filter(v => v.codigo.toLowerCase().indexOf(needle) > -1)
        if (this.options.productos.length < 1) {
          this.options.productos = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        }
      })
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
  watch: {
    openedAddProducto: function (val) {
      console.log(val)
      if (val) {
        this.isVisible = true
      }
    }
  }
}
</script>

<style scoped>

</style>>
