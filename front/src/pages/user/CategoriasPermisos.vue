<template>
  <div>
    <CreateSingle urlAPI="api/users/permisos/categorias-permisos" titulo="Categorias permisos"/>
  </div>
</template>

<script>

import CreateSingle from 'components/generales/CreateSingle.vue'

export default {
  name: 'PageCategoriasPermisos',
  components: {
    CreateSingle
  }
}
</script>
