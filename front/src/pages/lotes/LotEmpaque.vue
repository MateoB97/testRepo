<template>
  <div>
    <!-- type 0 for normal lote, type 1 for recieved finish products -->
    <Etiqueta type="0" />
  </div>
</template>

<script>

import Etiqueta from 'components/lotes/Etiqueta.vue'

export default {
  name: 'PageLotEmpaque',
  components: {
    Etiqueta
  }
}
</script>
