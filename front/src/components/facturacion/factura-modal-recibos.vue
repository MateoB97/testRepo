<template>
<q-dialog
      v-model="isVisible"
      persistent
      transition-show="slide-up"
      transition-hide="slide-down"
      full-width
    >
      <q-card class="bg-primary text-white">
        <q-bar>
          <q-space />
          <q-btn dense flat icon="close" @click=" cerrarRecibosRelacionadas()" >
            <q-tooltip content-class="bg-white text-primary">Close</q-tooltip>
          </q-btn>
        </q-bar>
        <q-card-section>
          <div class="text-h6">Documentos Relacionados</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <div class="q-pa-md q-gutter-sm">
            <q-table
            title="Facturas - Recibos"
            :data="dataRecibos"
            :columns="columnsRecibos"
            row-key="name"
            >
              <q-td slot="body-cell-id" slot-scope="props" :props="props">
                <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/recibocaja/imprimir/'+ props.value +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn> </a>
              </q-td>
            </q-table>
          </div>
        </q-card-section>
      </q-card>
    </q-dialog>
</template>
<script>
const axios = require('axios')

export default {
  props: ['editorRecibos'],
  name: 'FacturaModalRecibos',
  data () {
    return {
      alert: false,
      dialog: false,
      isVisible: false,
      maximizedToggle: true,
      dataRecibos: [],
      columnsRecibos: [
        { name: 'consecutivo', label: 'Consecutivo', field: 'consecutivo', sortable: false, align: 'left' },
        { name: 'id', label: 'Imprimir', field: 'id', sortable: false, align: 'left' },
        { name: 'tipo', label: 'Tipo Mov', field: 'tipomov', sortable: false, align: 'left' },
        { name: 'tercero', label: 'Sucursal', field: 'sucursal', sortable: false, align: 'left' },
        { name: 'sucursal', label: 'Tercero', field: 'tercero', sortable: false, align: 'left' },
        { name: 'valor', label: 'Valor', field: 'valor', sortable: false, align: 'left' },
        { name: 'fecha', label: 'Fecha', field: 'fecha', sortable: false, align: 'left' }
      ]
    }
  },
  methods: {
    recibosRelacionadas (id) {
      var app = this
      axios.get(`facturacion/movimientos/filtro/reciboporid/${id}`).then(
        function (responseRecibo) {
          app.dataRecibos = responseRecibo.data
          if (app.dataRecibos.length > 0) {
            app.isVisible = true
          } else {
            app.$q.notify({ color: 'negative', message: 'Esta factura no tiene documentos relacionados.' })
            app.isVisible = false
          }
        }
      )
    },
    cerrarRecibosRelacionadas () {
      var app = this
      app.isVisible = false
      this.$emit('ReciboclearId', 0)
    }
  },
  watch: {
    editorRecibos: function (val) {
      if (val !== 0) {
        this.recibosRelacionadas(val)
        console.log(val)
      }
    }
  }
}
</script>

<style scoped>

</style>>
