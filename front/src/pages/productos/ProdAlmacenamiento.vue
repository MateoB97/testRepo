<template>
  <div>
    <CreateSingle urlAPI="api/productos/almacenamiento" titulo="Tipos de almacenamiento"/>
  </div>
</template>

<script>

import CreateSingle from 'components/generales/CreateSingle.vue'

export default {
  name: 'PageProdAlmacenamiento',
  components: {
    CreateSingle
  }
}
</script>
