<template>
  <div>
    <q-page padding>
      <q-dialog v-model="show.modal" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
        <q-layout view="Lhh lpR fff" style="height: 400px; max-width: 800px" container class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-on:click="cleanTemp()" v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
                <div class="row q-col-gutter-sm q-mb-sm">
                    <div class="col-4">
                        <q-select
                        label="Seleccione Grupo Padre"
                        v-model="temp.grupo"
                        option-value="id"
                        option-label="nombre"
                        option-disable="inactive"
                        @input="selectedGrupo()"
                        :options="listas.grupos"
                        />
                    </div>
                    <div v-if="show.subgrupos" class="col-4">
                        <q-select
                        label="Seleccione Subgrupo Padre"
                        v-model="temp.subgrupo"
                        option-value="id"
                        option-label="label"
                        option-disable="inactive"
                        :options="listas.subgrupos"
                        @input="selectedSubgrupo('del')"
                        />
                    </div>
                    <div v-if="show.productos" class="col-4">
                        <q-select
                        label="Seleccione Producto"
                        v-model="temp.producto"
                        option-value="id"
                        option-label="label"
                        option-disable="inactive"
                        :options="listas.productos"
                        />
                    </div>
                </div>
                <q-btn v-on:click="saveTemp()" v-close-popup label="Guardar" color="warning"></q-btn>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>
        <h3>Generalidades</h3>
        <div class="overflow-hidden">
            <p><strong>Producto para 1/4 Delantero:</strong></p>
            <div class="row q-col-gutter-sm q-mb-sm">
                <q-input class="col-4" filled readonly color="primary" type="text" v-model="datos.delantero.grupo_nombre" label="Grupo">
                </q-input>
                <q-input class="col-4" filled readonly color="primary" type="text" v-model="datos.delantero.subgrupo_nombre" label="Subgrupo">
                </q-input>
                <q-input  class="col-4" filled readonly color="primary" type="text" v-model="datos.delantero.producto_nombre" label="Producto">
                </q-input>
            </div>
            <q-btn class="q-mb-md" v-on:click="openModal('del')" label="Cambiar" color="warning"></q-btn>
            <p><strong>Producto para 1/4 Trasero:</strong></p>
            <div class="row q-col-gutter-sm q-mb-sm">
                <q-input class="col-4" filled readonly color="primary" type="text" v-model="datos.trasero.grupo_nombre" label="Grupo">
                </q-input>
                <q-input class="col-4" filled readonly color="primary" type="text" v-model="datos.trasero.subgrupo_nombre" label="Subgrupo">
                </q-input>
                <q-input  class="col-4" filled readonly color="primary" type="text" v-model="datos.trasero.producto_nombre" label="Producto">
                </q-input>
            </div>
            <q-btn class="q-mb-md" v-on:click="openModal('tras')" label="Cambiar" color="warning"></q-btn>
            <p><strong>Producto para Canal:</strong></p>
            <div class="row q-col-gutter-sm q-mb-sm">
                <q-input class="col-4" filled readonly color="primary" type="text" v-model="datos.canal.grupo_nombre" label="Grupo">
                </q-input>
                <q-input class="col-4" filled readonly color="primary" type="text" v-model="datos.canal.subgrupo_nombre" label="Subgrupo">
                </q-input>
                <q-input  class="col-4" filled readonly color="primary" type="text" v-model="datos.canal.producto_nombre" label="Producto">
                </q-input>
            </div>
            <q-btn class="q-mb-md" v-on:click="openModal('canal')" label="Cambiar" color="warning"></q-btn>
            <div class="row q-col-gutter-sm q-mt-md">
              <div class="col-3">
                <q-btn icon="save" v-on:click="globalValidate('guardar')" label="Guardar" color="primary"></q-btn>
              </div>
            </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'CreateTipoAlmacen',
  data: function () {
    return {
      urlAPI: 'api/generales/generalidades',
      storeItems: {
        prod_cuarto_dela: null,
        prod_cuarto_tras: null,
        prod_canal: null
      },
      filter: '',
      listas: {
        grupos: [],
        subgrupos: [],
        productos: [],
        data: null
      },
      datos: {
        producto: null,
        prodGrupo_id: null,
        prodSubgrupo_id: null,
        delantero: {
          grupo_nombre: null,
          subgrupo_nombre: null,
          producto_nombre: null
        },
        trasero: {
          grupo_nombre: null,
          subgrupo_nombre: null,
          producto_nombre: null
        },
        canal: {
          grupo_nombre: null,
          subgrupo_nombre: null,
          producto_nombre: null
        }
      },
      show: {
        subgrupos: false,
        modal: false,
        productos: false
      },
      temp: {
        pieza: null,
        producto: null,
        grupo: null,
        subgrupo: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
      this.fillFields()
    },
    preSave () {
    },
    openModal (pieza) {
      this.show.modal = true
      this.temp.pieza = pieza
    },
    async selectedGrupo () {
      this.listas.subgrupos = []
      this.temp.subgrupo = null
      this.show.subgrupos = false
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/subgrupos/grupofilter/' + this.temp.grupo.id)
        var tempData = data.data
        tempData.forEach(element => {
          element.value = element.id
          element.label = element.nombre
        })
        this.listas.subgrupos = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los subgrupos!' })
      } finally {
        this.show.subgrupos = true
      }
    },
    async selectedSubgrupo () {
      this.show.productos = false
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/productos/items/subgrupofilter/' + this.temp.subgrupo.id)
        var tempData = data.data
        tempData.forEach(element => {
          element.value = element.id
          element.label = element.nombre
        })
        this.listas.productos = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar los productos!' })
      } finally {
        this.show.productos = true
      }
    },
    saveTemp () {
      if (this.temp.pieza === 'del') {
        this.datos.delantero.grupo_nombre = this.temp.grupo.nombre
        this.datos.delantero.subgrupo_nombre = this.temp.subgrupo.label
        this.datos.delantero.producto_nombre = this.temp.producto.label
        this.storeItems.prod_cuarto_dela = this.temp.producto.id
      }
      if (this.temp.pieza === 'tras') {
        this.datos.trasero.grupo_nombre = this.temp.grupo.nombre
        this.datos.trasero.subgrupo_nombre = this.temp.subgrupo.label
        this.datos.trasero.producto_nombre = this.temp.producto.label
        this.storeItems.prod_cuarto_tras = this.temp.producto.id
      }
      if (this.temp.pieza === 'canal') {
        this.datos.canal.grupo_nombre = this.temp.grupo.nombre
        this.datos.canal.subgrupo_nombre = this.temp.subgrupo.label
        this.datos.canal.producto_nombre = this.temp.producto.label
        this.storeItems.prod_canal = this.temp.producto.id
      }
      this.temp.grupo = null
      this.temp.subgrupo = null
      this.temp.producto = null
      this.show.subgrupos = false
      this.show.productos = false
    },
    cleanTemp () {
      this.temp.grupo = null
      this.temp.subgrupo = null
      this.temp.producto = null
      this.show.subgrupos = false
      this.show.productos = false
    },
    fillFields () {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/productos/items').then(
        function (response) {
          app.listas.productos_completos = response.data
          axios.get(app.$store.state.jhsoft.url + app.urlAPI).then(
            function (response2) {
              if (response2.data) {
                const getProdCuartoDelantero = app.listas.productos_completos.find(producto => producto.id === response2.data.prod_cuarto_dela)
                app.datos.delantero.grupo_nombre = getProdCuartoDelantero.grupo
                app.datos.delantero.subgrupo_nombre = getProdCuartoDelantero.subgrupo
                app.datos.delantero.producto_nombre = getProdCuartoDelantero.nombre
                app.storeItems.prod_cuarto_dela = response2.data.prod_cuarto_dela
                // ASDASDA
                const getProdCuartoTras = app.listas.productos_completos.find(producto => producto.id === response2.data.prod_cuarto_tras)
                app.datos.trasero.grupo_nombre = getProdCuartoTras.grupo
                app.datos.trasero.subgrupo_nombre = getProdCuartoTras.subgrupo
                app.datos.trasero.producto_nombre = getProdCuartoTras.nombre
                app.storeItems.prod_cuarto_tras = response2.data.prod_cuarto_tras
                // asdfasdfdsa
                const getProdCanal = app.listas.productos_completos.find(producto => producto.id === response2.data.prod_canal)
                app.datos.canal.grupo_nombre = getProdCanal.grupo
                app.datos.canal.subgrupo_nombre = getProdCanal.subgrupo
                app.datos.canal.producto_nombre = getProdCanal.nombre
                app.storeItems.prod_canal = response2.data.prod_canal
              }
            }
          ).catch(function (error) {
            console.log(error)
            app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
          })
        }
      ).catch(function (error) {
        console.log(error)
        app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
      })
    }
  },
  created: function () {
    var app = this
    axios.get(this.$store.state.jhsoft.url + 'api/productos/grupos').then(
      function (response) {
        app.listas.grupos = response.data
      }
    ).catch(function (error) {
      console.log(error)
      app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
    })
    this.fillFields()
  },
  computed: {
  }
}
</script>

<style>
    .q-table__container{
        width: 100%;
    }
</style>
