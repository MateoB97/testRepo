<template>
   <div class="row col-12 q-col-gutter-sm">
      <div class="col-6">
        <q-input v-model='value' label="Lote" @input="$emit('input', value)"></q-input>
      </div>
    </div>
</template>
<script>

import { globalFunctions } from 'boot/mixins.js'

export default {
  props: ['label'],
  name: 'LoteFilter',
  data () {
    return {
      value: null
    }
  },
  mixins: [globalFunctions],
  methods: {
  }
}
</script>

<style scoped>

</style>>
