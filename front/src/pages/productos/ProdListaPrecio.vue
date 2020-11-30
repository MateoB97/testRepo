<template>
  <div>
    <CreateSingle urlAPI="api/productos/listadeprecios" titulo="Listas de precios"/>
  </div>
</template>

<script>

import CreateSingle from 'components/generales/CreateSingle.vue'

export default {
  name: 'PageProdListaPrecio',
  components: {
    CreateSingle
  }
}
</script>
