<template>
  <div>
   <div class="row q-col-gutter-sm">
      <div class="col-6">
          <q-input label="Fecha de Inicial" v-model="datos.fecha_inicial" mask="date" :rules="['date']">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy ref="fecha_ini" transition-show="scale" transition-hide="scale">
                  <q-date v-model="datos.fecha_inicial" @input="datesInput('fecha_ini')" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
      </div>
      <div v-if="dateUnique == 0" class="col-6">
          <q-input label="Fecha de Final" v-model="datos.fecha_final" mask="date" :rules="['date']">
            <template v-slot:append>
              <q-icon name="event" class="cursor-pointer">
                <q-popup-proxy ref="fecha_fin" transition-show="scale" transition-hide="scale">
                  <q-date v-model="datos.fecha_final" @input="datesInput('fecha_fin')" />
                </q-popup-proxy>
              </q-icon>
            </template>
          </q-input>
      </div>
    </div>
  </div>
</template>
<script>

import { globalFunctions } from 'boot/mixins.js'

export default {
  props: ['dateUnique'],
  name: 'dateFilterComponent',
  data () {
    return {
      datos: {
        fecha_inicial: null,
        fecha_final: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    datesInput (value) {
      this.$refs[value].hide()
      if (this.dateUnique === 1) {
        this.$emit('setDates', this.datos.fecha_inicial)
      } else {
        this.$emit('setDates', this.datos)
      }
    }
  }
}
</script>

<style scoped>

</style>>
