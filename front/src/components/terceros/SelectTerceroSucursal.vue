<template>
    <div :class=columnas class="row q-col-gutter-sm">
        <div class="col-6">
          <q-select
            v-model="datos.tercero_id"
            use-input
            hide-selected
            fill-input
            option-label="nombre"
            :label="labelTercero"
            option-disable="inactive"
            option-value="id"
            input-debounce="0"
            :options="options.terceros"
            @filter="filterTerceros"
            @input="selectedTercero()"
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
        <div class="col-6">
          <q-select
            v-model="datos.sucursal"
            use-input
            hide-selected
            fill-input
            option-label="nombre"
            label="Sucursal"
            option-disable="inactive"
            option-value="id"
            map-options
            emit-value
            input-debounce="0"
            :options="options.sucursales"
            @filter="filterSucursales"
            @input="sendToParent()"
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
        <div v-if="sucursalActiva" class="col-12">
          <span>Dirección: {{ sucursalActiva.direccion }}</span>
        </div>
        <div v-if="sucursalActiva" class="col-12">
          <span>Teléfono: {{ sucursalActiva.telefono }}</span>
        </div>
    </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'

export default {
  props: ['columnas', 'labelTercero', 'editor'],
  name: 'SelectTerceroSucursal',
  data: function () {
    return {
      terceros: [],
      sucursales: [],
      datos: {
        tercero_id: null,
        sucursal: null
      },
      options: {
        terceros: this.terceros,
        sucursales: this.sucursales
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    selectedTercero () {
      var app = this
      this.$emit('tercero_id', this.datos.tercero_id.id)
      this.datos.sucursal = null
      axios.get(this.$store.state.jhsoft.url + 'api/terceros/sucursales/tercerofilter/' + this.datos.tercero_id.id).then(
        function (response) {
          app.sucursales = response.data
        }
      )
    },
    filterTerceros (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.terceros = this.terceros.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterSucursales (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.sucursales = this.sucursales.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    sendToParent () {
      this.$emit('input', this.datos.sucursal)
      this.$emit('setPlazo', this.datos.tercero_id.plazo_facturacion)
    }
  },
  created: function () {
    this.globalGetForSelect('api/terceros/items/estado/activos', 'terceros')
    this.globalGetForSelect('api/terceros/sucursales', 'sucursales')
  },
  computed: {
    sucursalActiva: function () {
      return this.sucursales.find(v => v.id === this.datos.sucursal)
    }
  },
  watch: {
    editor: function (val) {
      var app = this
      if (val !== this.datos.sucursal) {
        if (val !== null) {
          axios.get(this.$store.state.jhsoft.url + 'api/terceros/sucursales').then(
            function (response) {
              app.sucursales = response.data
              app.datos.sucursal = app.sucursales.find(element => parseInt(element.id) === parseInt(val))
              app.datos.tercero_id = app.terceros.find(element => parseInt(element.id) === parseInt(app.datos.sucursal.tercero_id))
            }
          )
        } else {
          this.datos.sucursal = null
          this.datos.tercero_id = null
        }
      }
    }
  }
}
</script>
