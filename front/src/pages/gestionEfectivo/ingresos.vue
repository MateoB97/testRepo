<template>
  <div>
    <q-page padding>
        <!-- inicio popup impresion al guardar -->
        <q-dialog v-model="openedPrintFactura" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
          <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
            <q-header class="bg-primary">
              <q-toolbar>
                <q-btn flat v-close-popup round dense icon="close" />
              </q-toolbar>
            </q-header>

            <q-page-container>
              <q-page padding>
                <div class="overflow-hidden">
                  <div class="row q-col-gutter-sm">
                    <h3>Se ha guardado el Ingreso N° {{ callback[0] }}</h3>
                  </div>
                  <div class="row text-center">
                    <q-btn class="q-ml-xs" color="primary" @click="printPOS(callback[1])"> POS </q-btn>
                  </div>
                </div>
              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
        <!-- fin popup impresion al guardar -->
      <div class="div-86">
        <h3>Crear Ingreso</h3>
        <div class="overflow-hidden">
            <div class="row q-mt-md q-col-gutter-sm">
                <div class="col" style="position:relative">
                    <p class="v-money-label"> Valor: </p>
                    <money v-model="storeItems.valor" v-bind="money" class="v-money"></money>
                </div>
            </div>
            <div class="row q-mt-md">
              <div class="col">
                <q-input
                    ref="observaciones"
                    v-model="storeItems.descripcion"
                    type="textarea"
                    label="Descripcion"
                  />
              </div>
            </div>
            <div class="column q-mt-md text-right items-end">
              <div class="col-3">
                    <q-btn v-if="!showForUpdate" class="btn-azul" v-on:click="globalValidate('guardar')" label="Guardar" />
                    <q-btn v-if="showForUpdate" class="btn-azul" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
                </div>
            </div>
        </div>
      </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import { Money } from 'v-money'
const axios = require('axios')

export default {
  name: 'GestionIngresos',
  components: {
    Money
  },
  data: function () {
    return {
      money: {
        decimal: ',',
        thousands: '.',
        prefix: '          $ ',
        suffix: '',
        precision: 0,
        masked: false
      },
      showForUpdate: false,
      urlAPI: 'api/ingresos/items',
      visibleColumns: ['id', 'nombre', 'tipo', 'actions'],
      separator: 'horizontal',
      filter: '',
      storeItems: {
        valor: 0,
        descripcion: null
      },
      openedPrintFactura: false,
      callback: []
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave (callback = null) {
      this.storeItems.valor = 0
      this.storeItems.descripcion = null
      if (callback !== null) {
        this.callback = callback
        this.openedPrintFactura = true
      }
    },
    preSave () {
    },
    postEdit () {
    },
    printPOS (id) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/ingresos/imprimir/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Impresion realizada.' })
          }
        }
      )
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/ingresos/tipos', 'tipos')
  },
  computed: {
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
    .v-money{
      padding: 17px;
      border: none;
      border-bottom: 1px solid rgba(0,0,0,0.24);
      width: 100%;
    }
    .v-money:focus{
      outline: none;
      border-bottom: 1px solid #027be3;
    }
    .v-money-label{
      color: rgba(0,0,0,0.6);
      font-size: 16px;
      line-height: 20px;
      font-weight: 400;
      letter-spacing: 0.00937em;
      position: absolute;
      top: 24px;
    }
</style>
