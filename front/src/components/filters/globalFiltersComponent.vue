<template>
  <div>
    <q-dialog v-model="isVisible" full-width >
      <q-layout view="Lhh lpR fff" container style="height: 57vh; max-width: 50vw" class="bg-white">
        <q-header class="bg-primary">
          <q-toolbar>
            <q-btn flat v-close-popup round dense icon="close" />
          </q-toolbar>
        </q-header>
        <q-page-container>
          <q-page padding>
            <div class="overflow-hidden">
              <div class="row q-col-gutter-sm">
                <DateFilterComponent
                  v-if="datesFilter"
                  :dateUnique="dateUnique"
                  @setDates="setDates"
                />
                <SelectTerceroSucursal
                  v-if="tercerosFilter"
                  @tercero_id="setTercero"
                  @input="setSucursal"
                  columnas='col-12'
                  labelTercero='Tercero'
                />
                <MultiSelectGrupoProducto
                  v-if="gruposFilter"
                  v-model="datos.grupos_id"
                />
                <GrupoSubgrupoProductoFilter
                  v-if="productosFilter"
                  @grupo_id="setGrupo"
                  @subgrupo_id="setSubgrupo"
                  @producto_id="setProducto"
                />
                <SelectTipoDoc
                  v-if="tipoDocFilter"
                  v-model="datos.tiposdoc_id"
                />
                <LoteFilter
                  v-if="loteFilter"
                  v-model="datos.lote_id"
                />
                <AloneProductsFilter
                  v-if="productsAloneFilter"
                  :value="tipo_transform == 'entrada' ? tipo_transform='entrada' : tipo_transform='salida' "
                  @input="tipo_transform = $event.target.value"
                  @producto_in_id="setProductoIn"
                  @producto_out_id="setProductoOut"
                />
              </div>
              <div class="row q-col-gutter-sm">
              </div>
              <div class="row q-mt-md text-center">
                <a target="_blank" :href="$store.state.jhsoft.url + url + params">
                  <q-btn class="q-ml-xs" icon="assignment" color="primary">Generar</q-btn>
                </a>
              </div>
            </div>
          </q-page>
        </q-page-container>
      </q-layout>
    </q-dialog>
    <q-btn class="btn-azul w-100" v-on:click="isVisible = true" :label="titleBtn" />
  </div>
</template>
<script>

import { globalFunctions } from 'boot/mixins.js'
import DateFilterComponent from 'components/filters/dataFilterComponent.vue'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'
import MultiSelectGrupoProducto from 'components/filters/MultiSelectGrupoProducto.vue'
import GrupoSubgrupoProductoFilter from 'components/filters/GrupoSubgrupoProductoFilter.vue'
import LoteFilter from 'components/filters/LoteFilter.vue'
import SelectTipoDoc from 'components/filters/SelectTipoDoc.vue'
import AloneProductsFilter from 'components/filters/aloneProducts.vue'

export default {
  name: 'globalFilterComponent',
  components: {
    SelectTerceroSucursal,
    DateFilterComponent,
    MultiSelectGrupoProducto,
    SelectTipoDoc,
    GrupoSubgrupoProductoFilter,
    LoteFilter,
    AloneProductsFilter
  },
  props: [
    'datesFilter',
    'tercerosFilter',
    'gruposFilter',
    'loteFilter',
    'productosFilter',
    'productsAloneFilter',
    'tipoDocFilter',
    'dateUnique',
    'titleBtn',
    'url'
  ],
  data () {
    return {
      tipoDoc: null,
      tipo_transform: null,
      datos: {
        fecha_inicial: null,
        fecha_final: null,
        tercero_id: null,
        sucursal_id: null,
        tiposdoc_id: null,
        tiporec_id: null,
        grupos_id: null,
        grupo_id: null,
        subgrupo_id: null,
        producto_id: null,
        entrada: null,
        salida: null,
        lote_id: null
      },
      isVisible: false,
      params: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    setDates (items) {
      this.datos.fecha_inicial = items.fecha_inicial
      this.datos.fecha_final = items.fecha_final
    },
    setTercero (value) {
      this.datos.tercero_id = value
    },
    setSucursal (value) {
      this.datos.sucursal_id = value
    },
    setGrupo (value) {
      this.datos.grupo_id = value
    },
    setSubgrupo (value) {
      this.datos.subgrupo_id = value
    },
    setProducto (value) {
      this.datos.producto_id = value
      console.log(this.datos.producto_id)
    },
    setProductoIn (value) {
      this.datos.entrada = value.id
      this.tipo_transform = value.tip
      console.log(value.tip)
    },
    setProductoOut (value) {
      this.datos.salida = value.id
      this.tipo_transform = value.tip
      console.log(value.tip)
    }
  },
  computed: {

  },
  created: function () {

  },
  watch: {
    datos: {
      deep: true,
      handler () {
        var app = this
        var params = '?'
        for (var key in app.datos) {
          if (app.datos[key] !== null) {
            params += key + '=' + app.datos[key] + '&'
          }
        }
        params = params.slice(0, -1)
        console.log(params)
        app.params = params
      }
    },
    isVisible: {
      deep: true,
      handler () {
        this.datos = {
          fecha_inicial: null,
          fecha_final: null,
          tercero_id: null,
          sucursal_id: null,
          tiposdoc_id: null,
          tiporec_id: null,
          grupos_id: null,
          grupo_id: null,
          subgrupo_id: null,
          producto_id: null,
          lote_id: null
        }
      }
    }
  }
}
</script>

<style scoped>

</style>>
