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
          <q-btn dense flat icon="close" @click=" cerrarNotasRelacionadas()" >
            <q-tooltip content-class="bg-white text-primary">Close</q-tooltip>
          </q-btn>
        </q-bar>
        <q-card-section>
          <div class="text-h6">Documentos Relacionados</div>
        </q-card-section>
        <q-card-section class="q-pt-none">
          <div class="q-pa-md q-gutter-sm">
            <q-table
            title="Facturas - Notas"
            :data="dataNotas"
            :columns="columnsNotas"
            row-key="name"
            >
              <q-td slot="body-cell-id" slot-scope="props" :props="props">
                <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/imprimir/'+ props.value +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn></a>
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
  props: ['editorNotas'],
  name: 'FacturaModalNotas',
  data () {
    return {
      alert: false,
      dialog: false,
      isVisible: false,
      maximizedToggle: true,
      dataNotas: [],
      columnsNotas: [
        { name: 'consecutivo', label: 'Consecutivo', field: 'consecutivo', sortable: false, align: 'left' },
        { name: 'id', label: 'Imprimir', field: 'id', sortable: false, align: 'left' },
        { name: 'tipo', label: 'Tipo Mov', field: 'tipomov', sortable: false, align: 'left' },
        { name: 'tercero', label: 'Sucursal', field: 'sucursal', sortable: false, align: 'left' },
        { name: 'sucursal', label: 'Tercero', field: 'tercero', sortable: false, align: 'left' },
        { name: 'estado', label: 'Estado Documento', field: 'estado', sortable: false, align: 'left' },
        { name: 'fecha_facturacion', label: 'Fecha Facturación', field: 'fecha_facturacion', sortable: false, align: 'left' },
        { name: 'fecha_vencimiento', label: 'Fecha Vencimiento', field: 'fecha_vencimiento', sortable: false, align: 'left' },
        { name: 'descuento', label: 'Descuento', field: 'descuento', sortable: false, align: 'left' },
        { name: 'ivatotal', label: 'Iva Total', field: 'ivatotal', sortable: false, align: 'left' },
        { name: 'subtotal', label: 'Sub Total', field: 'subtotal', sortable: false, align: 'left' },
        { name: 'total', label: 'Total', field: 'total', sortable: false, align: 'left' }
      ]
    }
  },
  methods: {
    notasRelacionadas (id) {
      var app = this
      axios.get(`facturacion/movimientos/filtro/notaporid/${id}`).then(
        function (responseNotas) {
          app.dataNotas = responseNotas.data
          if (app.dataNotas.length > 0) {
            app.isVisible = true
          } else {
            app.$q.notify({ color: 'negative', message: 'Esta factura no tiene documentos relacionados.' })
            app.isVisible = false
          }
        }
      )
    },
    cerrarNotasRelacionadas () {
      var app = this
      app.isVisible = false
      this.$emit('NotaclearID', 0)
    }
  },
  watch: {
    editorNotas: function (val) {
      if (val !== 0) {
        this.notasRelacionadas(val)
        console.log(val)
      }
    }
  }
}
</script>

<style scoped>

</style>>
