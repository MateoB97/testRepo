<template>
  <div class="row q-col-gutter-md">
    <div v-if="withBasculaSelect" class="col-6">
      <q-select
        label="Seleccione bascula"
        v-model="bascula"
        :options="basculas"
        option-value="ruta"
        option-label="nombre"
        option-disable="inactive"
        emit-value
        map-options
        @input="getPeso()"
      />
    </div>
    <div class="col-6">
      <q-input v-if="bascula != 'Manual'" ref="peso" v-model="peso" label="Peso" />
      <q-input v-if="bascula == 'Manual'" ref="peso" v-model="peso" label="Peso" @input="$emit('input', peso)" />
    </div>
  </div>

</template>
<script>

/**
 *  IMPORTANTE !!
 *  A toda pagina principal que llame este componente se le debe ingresar la siguiente linea de codigo
 *  beforeRouteLeave: function (to, from, next) {
 *    this.$refs.basculaComponent.stopGetPeso()
 *    next()
 *  }
 *  a mismo nivel de methods o create, esto para poder cancelar las peticiones al cambiarse a otra pagina
 */

import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  props: ['withBasculaSelect', 'basculaIp'],
  name: 'basculaComponent',
  data () {
    return {
      peso: null,
      basculas: [],
      bascula: null
    }
  },
  mixins: [globalFunctions],
  methods: {
    getPeso () {
      var v = this
      if (v.bascula !== 'Manual') {
        this.stopGetPeso()
        this.interval = setInterval(function () {
          axios.get(v.bascula).then(
            function (response) {
              v.peso = JSON.parse(response.data).p
              v.$emit('input', v.peso)
            }
          )
        }, 1000)
      } else {
        console.log('holi')
        this.stopGetPeso()
      }
    },
    stopGetPeso () {
      clearInterval(this.interval)
    },
    starter () {
      if (!this.withBasculaSelect) {
        this.bascula = this.basculaIp
        this.getPeso()
      }
    }
  },
  computed: {
  },
  created: function () {
    this.globalGetForSelect('api/generales/basculas', 'basculas')
    this.starter()
  }
}
</script>

<style scoped>

</style>>
