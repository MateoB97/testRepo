<template>
  <div>
    <q-page padding>
      <NotasRelacionadas :editorNotas="idNota" @NotaclearID="idNota = 0" />
      <RecibosRelacionadas :editorRecibos="idRecibo" @ReciboclearId="idRecibo = 0" />
      <!-- inicio popup Errores Fact Elect -->
          <q-dialog v-model="openedErrores" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 250px; max-width: 800px; background-color: #7E7EF4; color: #FFFFFF">
              <q-header >
                <q-toolbar style="background-color: #7E7EF4!important;">
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div v-for="error in erroresFE" :key="error" class="col-12 text-center">
                        <p>{{ error }}</p>
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup Errores Fact Elect -->

        <h3>Movimientos</h3>
        <div class="row q-mt-xl">
            <q-table
                class="my-sticky-header-column-table"
                title= 'Movimientos'
                :data="tableData"
                :columns="columns"
                :filter="filter"
                :visible-columns="visibleColumns"
                :separator="separator"
                row-key="id"
                color="secondary"
                table-style="width:100%"
            >
                <template slot="top-right" slot-scope="props">
                    <q-input
                        hide-underline
                        color="secondary"
                        v-model="filter"
                        class="col-6"
                        debounce="500"
                    >
                      <template v-slot:append>
                        <q-icon name="search" />
                      </template>
                    </q-input>
                    <q-btn
                        flat round dense
                        :icon="props.inFullscreen ? 'fullscreen_exit' : 'fullscreen'"
                        @click="props.toggleFullscreen"
                    />
                </template>
                <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/facturacion/imprimir/'+ props.value +'?token='+ $store.state.jhsoft.token"><q-btn class="q-ml-xs" icon="assignment" color="primary"></q-btn></a>
                    <q-btn class="q-ml-xs" color="primary" @click="printPOS(props.value, 1)"> POS </q-btn>
                    <q-btn class="q-ml-xs" color="primary" title="Notas Credito y Debito" icon="code" @click="() => { idNota = props.value, modalNotasView = true }"></q-btn>
                    <q-btn class="q-ml-xs" color="primary" title="Recibos" icon="aspect_ratio" @click="() => { idRecibo = props.value, modalRecibosView = true }"></q-btn>
                </q-td>
                <q-td slot="body-cell-estado" slot-scope="props" :props="props">
                    <div v-if="props.value === '0'">Saldada</div>
                    <div v-if="props.value === '1'">Pendiente</div>
                    <div v-if="props.value === '2'">A favor</div>
                    <div v-if="props.value === '3'">Devolución</div>
                </q-td>
                <q-td slot="body-cell-total" slot-scope="props" :props="props">
                    {{ props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-saldo" slot-scope="props" :props="props">
                    {{  props.value | toMoney }}
                </q-td>
                <q-td slot="body-cell-enviar" slot-scope="props" :props="props">
                  <div v-if="validarLegal(props.value)">
                    <q-btn v-if="validarFactElect(props.value)" class="q-ml-xs" color="primary" @click="globalEnviarFacturaElectronica(props.value)"> Factura Electronica </q-btn>
                    <q-btn v-if="!validarFactElect(props.value)" class="q-ml-xs" color="positive" @click="consultarFacturaElectronica(props.value)"> Consultar Estado FE </q-btn>
                  </div>
                </q-td>
                <q-td slot="body-cell-estado_fe" slot-scope="props" :props="props">
                    <div v-if="props.value === '0'">Rechazada</div>
                    <div v-if="props.value === null">Pendiente</div>
                    <div v-if="props.value === '1'">Aceptado</div>
                    <div v-if="props.value === '2'">Enviada se debe Consultar</div>
                    <div v-if="props.value === '3'">No Enviado, Errores</div>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import NotasRelacionadas from 'components/facturacion/factura-modal-notas'
import RecibosRelacionadas from 'components/facturacion/factura-modal-recibos'

const axios = require('axios')

export default {
  name: 'PagMovResumen',
  components: { NotasRelacionadas, RecibosRelacionadas },
  data: function () {
    return {
      alert: false,
      storeItems: {
        nombre: null
      },
      selected: [],
      idNota: null,
      modalNotasView: false,
      modalRecibosView: false,
      idRecibo: null,
      urlAPI: 'api/facturacion/movimientos',
      showForUpdate: false,
      tableData: [],
      erroresFE: [],
      openedErrores: false,
      empresa: [],
      visibleColumns: ['id', 'nombre', 'actions'],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
    },
    preSave () {
    },
    postEdit () {
    },
    printPOS (id, copia) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/facturacion/imprimirpos/' + id + '/' + copia).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      ).catch(function (error) {
        app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
        console.log(error)
      })
    },
    validarFactElect (id) {
      var item = this.tableData.find(element => element.id === id)
      if (item.cufe !== null) {
        return false
      } else {
        return true
      }
    },
    validarLegal (id) {
      var item = this.tableData.find(element => element.id === id)
      if (parseInt(item.soenac_tipo_doc) === 0) {
        return false
      } else {
        return true
      }
    },
    consultarFacturaElectronica (id) {
      var app = this
      app.$q.loading.show()
      var item = this.tableData.find(element => element.id === id)
      const url = 'https://supercarnes-jh.apifacturacionelectronica.xyz/api/ubl2.1/status/document/'
      var token = '?api_token=' + app.empresa.token_fac_elect
      axios.post(app.$store.state.jhsoft.url + 'api/facturacion/enviarfacturasoenac', { url: url + item.cufe + token }).then(
        function (response1) {
          var dataFac = {
            estado_fe: null,
            zip_key: response1.data.zip_key,
            zip_name: response1.data.zip_name,
            url_acceptance: response1.data.url_acceptance,
            url_rejection: response1.data.url_rejection,
            pdf_base64_bytes: response1.data.pdf_base64_bytes,
            dian_response_base64_bytes: response1.data.dian_response_base64_bytes,
            application_response_base64_bytes: response1.data.application_response_base64_bytes
          }
          if (response1.data.is_valid === true) {
            app.$q.notify({ color: 'positive', message: 'El documento ha sido Aceptado.' })
            dataFac.estado_fe = 1
            var dataCorreo = {
              'to': [
                {
                  'email': item.email
                }
              ],
              'bcc': [
                {
                  'email': app.empresa.email_backup_fact_elect
                }
              ]
            }
            axios.post(app.$store.state.jhsoft.url + 'api/facturacion/enviarfacturasoenac', { url: 'https://supercarnes-jh.apifacturacionelectronica.xyz/api/ubl2.1/mail/send/' + item.cufe + token, body: dataCorreo }).then(
              function (response3) {
                if (parseInt(response3.data.is_valid) === 1) {
                  app.$q.notify({ color: 'positive', message: 'Notificacion enviada al correo ' + item.email })
                } else {
                  app.$q.notify({ color: 'negative', message: 'Error al enviar el email.' })
                }
              }
            )
          } else {
            app.$q.notify({ color: 'negative', message: 'El documento no ha sido Aceptado.' })
            dataFac.estado_fe = 0
            app.erroresFE = response1.data.errors_messages
            app.openedErrores = true
          }
          axios.post(app.$store.state.jhsoft.url + 'api/facturacion/agregarcufe/' + id, dataFac).then(
            function (response2) {
              if (response2.data === 'done') {
                app.$q.notify({ color: 'positive', message: 'Documento Actualizado.' })
                app.globalGetItems()
                app.$q.loading.hide()
              }
            }
          )
        }
      )
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/generales/empresa', 'empresa')
  },
  computed: {
    columns: function () {
      if (this.$store.state.jhsoft.tipo_licencia === 3 || this.$store.state.jhsoft.tipo_licencia === 4) {
        return [
          { name: 'consecutivo', required: true, label: 'Consecutivo', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'actions', required: true, label: 'Impirmir', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'tipo', required: true, label: 'Tipo Mov', align: 'left', field: 'tipo', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'estado', required: true, label: 'Estado', align: 'left', field: 'estado', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'fecha_facturacion', required: true, label: 'Fecha facturacion', align: 'left', field: 'fecha_facturacion', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'fecha_vencimiento', required: true, label: 'Fecha vencimiento', align: 'left', field: 'fecha_vencimiento', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'total', required: true, label: 'Total', align: 'right', field: 'total', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'saldo', required: true, label: 'Saldo', align: 'right', field: 'saldo', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'enviar', required: true, label: 'Enviar', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'estado_fe', required: true, label: 'Estado FE', align: 'right', field: 'estado_fe', sortable: true, classes: 'my-class', style: 'width: 200px' }
        ]
      } else {
        return [
          { name: 'consecutivo', required: true, label: 'Consecutivo', align: 'left', field: 'consecutivo', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'actions', required: true, label: 'Impirmir', align: 'right', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'tipo', required: true, label: 'Tipo Mov', align: 'left', field: 'tipo', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'tercero', required: true, label: 'Tercero', align: 'left', field: 'tercero', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'sucursal', required: true, label: 'Sucursal', align: 'left', field: 'sucursal', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'estado', required: true, label: 'Estado', align: 'left', field: 'estado', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'fecha_facturacion', required: true, label: 'Fecha facturacion', align: 'left', field: 'fecha_facturacion', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'fecha_vencimiento', required: true, label: 'Fecha vencimiento', align: 'left', field: 'fecha_vencimiento', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'total', required: true, label: 'Total', align: 'right', field: 'total', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'saldo', required: true, label: 'Saldo', align: 'right', field: 'saldo', sortable: true, classes: 'my-class', style: 'width: 200px' },
          { name: 'usuario', required: true, label: 'Usuario', align: 'right', field: 'usuario', sortable: true, classes: 'my-class', style: 'width: 200px' }
        ]
      }
    }
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
</style>
