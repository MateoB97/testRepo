<template>
    <div>
        <q-page padding>
            <h3>Crear Despacho por lote</h3>
            <div class="row q-col-gutter-md">
              <div class="col">
                <q-input color="primary" type="number" v-model="storeItems.lote_id" label="Ingrese lote"></q-input>
              </div>
              <div class="col">
                <q-input color="primary" type="number" v-model="storeItems.temperatura" label="Temperatura"></q-input>
              </div>
              <div class="col">
                <q-input color="primary" type="text" v-model="storeItems.vehiculo" label="Placa Vehiculo"></q-input>
              </div>
            </div>
            <SelectTerceroSucursal v-model="storeItems.terceroSucursal_id" columnas='col-8' labelTercero='Cliente'/>
            <div class="row q-mt-md">
                <div class="col-12">
                    <q-btn color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
                </div>
            </div>
        </q-page>
    </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'

export default {
  name: 'PageSalMercancia',
  components: {
    SelectTerceroSucursal
  },
  data () {
    return {
      urlAPI: 'api/despachos/crearporlote',
      storeItems: {
        lote_id: null,
        terceroSucursal_id: null,
        temperatura: null,
        vehiculo: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    preSave () {
    },
    postSave () {
    }
  }
}
</script>

<style scoped>
  .my-card{
    width: 100%;
    max-width: 250px;
    cursor:pointer}
  .my-card:hover{
    background-color:greenyellow
  }
  .my-card-prog{
    cursor: pointer;
  }
  .my-card-prog:hover{
    background-color: aqua;
  }
  h5{
    width: 100%;
    margin: 5px;
  }

  .col-lotes {
    max-height: 600px;
    overflow-y: scroll;
  }
</style>
