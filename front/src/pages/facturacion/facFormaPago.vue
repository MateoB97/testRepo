<template>
  <div>
    <CreateSingle urlAPI="api/facturacion/formaspago" titulo="Formas de Pago"/>
  </div>
</template>

<script>

import CreateSingle from 'components/generales/CreateSingle.vue'

export default {
  name: 'PageFacFormaPago',
  components: {
    CreateSingle
  }
}
</script>
