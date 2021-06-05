<template>
  <div>
    <CreateSingle urlAPI="api/users/permisos/roles" titulo="Roles"/>
  </div>
</template>

<script>

import CreateSingle from 'components/generales/CreateSingle.vue'

export default {
  name: 'PageUserRoles',
  components: {
    CreateSingle
  }
}
</script>
