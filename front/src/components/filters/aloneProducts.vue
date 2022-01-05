<template>
    <div class="col-12 row q-col-gutter-md">
      <div class="col-4">
        <q-select
          use-input
          autofocus
          hide-selected
          fill-input
          v-model="data.producto_out"
          option-value="id"
          option-label="nombre"
          label="Sale"
          option-disable="inactive"
          input-debounce="0"
          :options="options.productOut"
          @filter="filterProductOut"
          @input="selectedProductOut"
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
              <q-item-label v-html="scope.opt.nombre" />
              </q-item-section>
          </q-item>
          </template>
        </q-select>
      </div>
      <div class="col-4">
        <q-select
          use-input
          autofocus
          hide-selected
          fill-input
          v-model="data.producto_in"
          option-value="id"
          option-label="nombre"
          label="Entra"
          option-disable="inactive"
          input-debounce="0"
          :options="options.productIn"
          @filter="filterProductIn"
          @input="selectedProductIn"
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
              <q-item-label v-html="scope.opt.nombre" />
              </q-item-section>
          </q-item>
          </template>
        </q-select>
      </div>
    </div>
</template>
<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'aloneProductsFilter',
  data () {
    return {
      model: null,
      storeItems: {
      },
      options: {
        productOut: this.productos,
        productIn: this.productos
      },
      productos: [],
      showForUpdate: false,
      data: {
        producto_in: null,
        producto_out: null
      }
    }
  },
  mixins: [globalFunctions],
  created: function () {
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
  },
  methods: {
    filterProductOut (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productOut = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        console.log(this.options.productOut)
      })
    },
    filterProductIn (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productIn = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    selectedProductOut (value) {
      // console.log(value, this.data.producto_out.id)
      this.data.producto_out.id = value.id
      this.data.producto_out.tip = 'salida'
      this.$emit('producto_out_id', this.data.producto_out)
    },
    selectedProductIn (value) {
      // console.log(value, this.data.producto_in.id)
      this.data.producto_in.id = value.id
      this.data.producto_out.tip = 'entrada'
      this.$emit('producto_in_id', this.data.producto_in)
    }
  }
}
</script>
<style lang="scss" scoped>

</style>
