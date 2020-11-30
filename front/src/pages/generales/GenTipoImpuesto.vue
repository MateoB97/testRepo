<template>
  <div>
    <CreateSingle urlAPI="api/generales/tipoimpuestos" titulo="Tipos de Impuestos"/>
  </div>
</template>

<script>

import CreateSingle from 'components/generales/CreateSingle.vue'

export default {
  name: 'PageGenTipoImpuesto',
  components: {
    CreateSingle
  }
}
</script>
