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
                <SelectGrupoProducto
                  v-if="gruposFilter"
                  v-model="datos.grupos_id"
                />
                <SelectTipoDoc
                  v-if="tipoDocFilter"
                  v-model="datos.tiposdoc_id"
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
import SelectGrupoProducto from 'components/filters/MultiSelectGrupoProducto.vue'
import SelectTipoDoc from 'components/filters/SelectTipoDoc.vue'

export default {
  name: 'dateFilterComponent',
  components: {
    SelectTerceroSucursal,
    DateFilterComponent,
    SelectGrupoProducto,
    SelectTipoDoc
  },
  props: [
    'datesFilter',
    'tercerosFilter',
    'gruposFilter',
    'tipoDocFilter',
    'dateUnique',
    'titleBtn',
    'url'
  ],
  data () {
    return {
      tipoDoc: null,
      datos: {
        fecha_inicial: null,
        fecha_final: null,
        tercero_id: null,
        sucursal_id: null,
        tiposdoc_id: null,
        tiporec_id: null,
        grupos_id: null
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
    }
  }
}
</script>

<style scoped>

</style>>
