<template>
  <div>
    <q-page padding>

        <h3>Productos por lote</h3>
        <div class="row">
            <div class="col-6">
                <q-input v-model="idlote" label="Ingrese Lote"/>
            </div>
            <div class="col-2">
                <a target="_blank" :href="$store.state.jhsoft.url+'api/informes/productosporlote/' + this.idlote +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" color="primary">Enviar</q-btn> </a>
            </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'PageInformeProductosLote',
  data: function () {
    return {
      idlote: null
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
    },
    postEdit () {
    }
  },
  created: function () {
  },
  computed: {
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
</style>
