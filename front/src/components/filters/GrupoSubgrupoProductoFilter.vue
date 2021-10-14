<template>
  <div class="col-12 row q-col-gutter-md">
    <div class="col-4">
        <q-select
          v-model="data.grupo"
          use-input
          autofocus
          hide-selected
          fill-input
          option-value="id"
          option-label="nombre"
          label="Grupo"
          option-disable="inactive"
          input-debounce="0"
          :options="options.grupos"
          @filter="filterGrupos"
          @input="selectedGrupo()"
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
    <div v-if="showSubgrupos" class="col-4">
        <q-select
          v-model="data.subgrupo"
          use-input
          autofocus
          hide-selected
          fill-input
          option-value="id"
          option-label="nombre"
          label="Subgrupo"
          option-disable="inactive"
          input-debounce="0"
          :options="options.subgrupos"
          @filter="filterSubgrupos"
          @input="selectedSubgrupo()"
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
    <div v-if="showProductos" class="col-4">
        <q-select
          v-model="data.producto"
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
          @input="selectedProducto()"
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

const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'ProductosFilterComponent',
  data () {
    return {
      model: null,
      showProductos: false,
      showSubgrupos: false,
      data: {
        grupo: null,
        subgrupo: null,
        producto: null
      },
      grupos: [],
      subgrupos: [],
      productos: [],
      options: {
        grupos: this.grupos,
        subgrupos: this.subgrupos,
        productos: this.productos
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    filterGrupos (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.grupos = this.grupos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterSubgrupos (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.subgrupos = this.subgrupos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterProductoManual (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productos = this.productos.filter(v => v.codigo.toLowerCase().indexOf(needle) > -1)
        if (this.options.productos.length < 1) {
          this.options.productos = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        }
      })
    },
    selectedGrupo () {
      var app = this
      app.data.producto = null
      app.data.subgrupo = null
      app.showProductos = false
      this.$emit('grupo_id', this.data.grupo.id)
      this.$emit('subgrupo_id', null)
      this.$emit('producto_id', null)
      axios.get(app.$store.state.jhsoft.url + 'api/productos/subgrupos/grupofilter/' + this.data.grupo.id).then(
        function (response) {
          app.subgrupos = response.data
          app.showSubgrupos = true
        }
      ).catch(error => {
        console.log(error.response)
        app.$q.notify('Error al filtrar los subgrupos')
      })
    },
    selectedSubgrupo () {
      var app = this
      app.data.producto = null
      this.$emit('subgrupo_id', this.data.subgrupo.id)
      this.$emit('producto_id', null)
      axios.get(app.$store.state.jhsoft.url + 'api/productos/items/subgrupofilter/' + this.data.subgrupo.id).then(
        function (response) {
          app.productos = response.data
          app.showProductos = true
        }
      ).catch(error => {
        console.log(error.response)
        app.$q.notify('Error al filtrar los productos')
      })
    },
    selectedProducto () {
      this.$emit('producto_id', this.data.producto.id)
    }
  },
  created: function () {
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
  }
}
</script>

<style scoped>

</style>
