<template>
  <div class="q-pa-md">
    <div class="q-gutter-md row items-start">
      <q-select
        filled
        v-model="multiple"
        multiple
        :options="optionsData"
        option-value="id"
        option-label="nombre"
        label="Tipo Documento"
        style="width: 250px"
      />
    </div>
  </div>
</template>
<script>
const axios = require('axios')

export default {
  name: 'SelectTipo',
  data () {
    return {
      multiple: null,
      data: [],
      optionsData: []
    }
  },
  methods: {

  },
  mounted () {
    var app = this
    axios.get('/reportesgenerados/reportes/tipos').then(
      function (response) {
        app.data = response.data
        app.optionsData = response.data
      }
    )
  }
}
</script>

<style scoped>

</style>
