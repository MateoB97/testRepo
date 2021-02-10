<template>
  <div class="col-6">
    <q-select
        v-model="model"
        :options="grupos"
        label="Seleccione los grupos"
        option-value="id"
        option-label="nombre"
        multiple
        emit-value
        map-options
        @input="$emit('input', model)"
    >
      <template v-slot:option="{ itemProps, itemEvents, opt, selected, toggleOption }">
        <q-item
          v-bind="itemProps"
          v-on="itemEvents"
        >
          <q-item-section>
            <q-item-label v-html="opt.nombre" ></q-item-label>
          </q-item-section>
          <q-item-section side>
            <q-toggle :value="selected" @input="toggleOption(opt)" />
          </q-item-section>
        </q-item>
      </template>
    </q-select>
  </div>
</template>
<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'gruposFilterComponent',
  data () {
    return {
      model: null,
      grupos: []
    }
  },
  mixins: [globalFunctions],
  methods: {
  },
  created: function () {
    this.globalGetForSelect('api/productos/grupos/estado/activos', 'grupos')
  }
}
</script>

<style scoped>

</style>
