<template>
    <div class="background-menu menu-lateral">
      <!-- inicio popup abrir caja -->
      <q-dialog v-model="openAbrirCaja" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <div class="overflow-hidden">
                <h3>Abrir Caja</h3>
                <!-- <q-input v-model="temp.monto_apertura" label="Monto apertura"></q-input> -->
                <div class="col-12" style="position:relative">
                  <p class="v-money-label-menu"> Valor: </p>
                  <money v-model="temp.monto_apertura" v-bind="money" class="v-money-menu"></money>
                </div>
                <q-btn v-close-popup class="q-mt-sm" @click="abrirCaja" color="primary">Abrir Caja</q-btn>
              </div>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>
      <!-- fin popup abrir caja -->
      <!-- inicio popup cerrar caja -->
      <q-dialog v-model="openCerrarCaja" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
        <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
          <q-header class="bg-primary">
            <q-toolbar>
              <q-btn flat v-close-popup round dense icon="close" />
            </q-toolbar>
          </q-header>

          <q-page-container>
            <q-page padding>
              <div class="overflow-hidden">
                <h3>Cerrar Caja</h3>
                <div class="col-12" style="position:relative">
                  <p class="v-money-label-menu"> Valor: </p>
                  <money v-model="temp.monto_cierre" v-bind="money" class="v-money-menu"></money>
                </div>
                <q-btn v-close-popup class="q-mt-sm" @click="cerrarCaja" color="primary">Cerrar Caja</q-btn>
              </div>
            </q-page>
          </q-page-container>
        </q-layout>
      </q-dialog>
      <!-- fin popup cerrar caja -->

      <q-list>
        <q-expansion-item
          v-if="globalValidarPermiso(71) && cuadreAbierto === 1"
          class="expansion-block"
          expand-separator
          icon="storefront"
          label="Facturación"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link v-for="doc in docFacturacion" :key="doc.id" :to="{ name: 'movimientos', params: { id: doc.id, consecmov: 'nuevo' } }" class="menuItem">{{ doc.nombre }}</router-link>
              <router-link to="/gestion-efectivo/ingresos" class="menuItem q-mt-sm">ingreso de efectivo</router-link>
              <router-link to="/gestion-efectivo/egresos" class="menuItem q-mt-sm">Egreso de efectivo</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <q-expansion-item
          v-if="globalValidarPermiso(54) && this.$store.state.jhsoft.lotes"
          class="expansion-block"
          expand-separator
          icon="business_center"
          label="Lotes"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link to="/lotes" class="menuItem">Crear Lote</router-link>
              <router-link to="/lotes/peso-planta" class="menuItem">Peso Planta</router-link>
              <router-link to="/lotes/empaque" class="menuItem">Empaque</router-link>
              <router-link to="/lotes/empaque-terminado" class="menuItem">Empaque Prod Terminado</router-link>
              <router-link to="/lotes/etiqueta-interna" class="menuItem">Etiqueta Interna</router-link>
              <router-link to="/lotes/peso-programacion" class="menuItem">Peso por programación</router-link>
              <router-link to="/lotes/peso-marinacion" class="menuItem">Peso Marinacion</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <q-expansion-item
          v-if="this.$store.state.jhsoft.despachos"
          class="expansion-block"
          expand-separator
          icon="local_shipping"
          label="Despachos"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link to="/despachos" class="menuItem">Salida de Mercancia</router-link>
              <router-link to="/despachos/listado" class="menuItem">Ver despachos</router-link>
              <router-link to="/despachos/crearporlote" class="menuItem">Crear despacho por lote completo</router-link>
              <router-link to="/despachos/pesodespacho" class="menuItem">Peso en despacho</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <q-expansion-item
          v-if="globalValidarPermiso(71) && cuadreAbierto === 1 && this.$store.state.jhsoft.recibos"
          class="expansion-block"
          expand-separator
          icon="attach_money"
          label="Recibos de caja"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link v-for="docRec in docRecibos" :key="'rec'+docRec.id" :to="{ name: 'recibos', params: { id: docRec.id } }" class="menuItem">{{ docRec.nombre }}</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <q-expansion-item
          v-if="this.$store.state.jhsoft.compras"
          class="expansion-block"
          expand-separator
          icon="shopping_cart"
          label="Compras"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link v-for="doc in docCompras" :key="'compra' + doc.id" :to="{ name: 'compras', params: { id: doc.id } }" class="menuItem">{{ doc.nombre }}</router-link>
              <br>
              <router-link v-for="doc in docComproEgresos" :key="'comproegreso' + doc.id" :to="{ name: 'compro-egresos', params: { id: doc.id } }" class="menuItem">{{ doc.nombre }}</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <q-expansion-item
          v-if="this.$store.state.jhsoft.ordenes"
          class="expansion-block"
          expand-separator
          icon="assignment_turned_in"
          label="Ordenes"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link v-for="doc in docOrdenes" :key="'compra' + doc.id" :to="{ name: 'ordenes', params: { id: doc.id } }" class="menuItem">{{ doc.nombre }}</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <q-expansion-item
          expand-separator
          icon="grain"
          label="General"
          class="expansion-block"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="Productos">
            <q-card>
              <q-card-section>
                <router-link to="/productos" class="menuItem">Crear Productos</router-link>
                <router-link to="/productos/grupos" class="menuItem">Crear Grupos</router-link>
                <router-link to="/productos/subgrupos" class="menuItem">Crear Subgrupos</router-link>
                <router-link to="/generales/unidades" class="menuItem">Crear Unidad</router-link>
                <router-link to="/productos/listadodeprecios" class="menuItem">Lista de precios</router-link>
                <router-link to="/productos/cambioprecios" class="menuItem">Cambio rapido de precios</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>
          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="Vendedores">
            <q-card>
              <q-card-section>
                <router-link to="/generales/vendedores" class="menuItem">Crear Vendedores</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>
          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="Terceros">
            <q-card>
              <q-card-section>
                <router-link to="/terceros" class="menuItem">Crear Terceros</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>
          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="Impuestos">
            <q-card>
              <q-card-section>
                <router-link to="/generales/impuestos" class="menuItem">Crear Impuesto</router-link>
                <router-link to="/generales/iva" class="menuItem">Crear IVA</router-link>
                <router-link to="/generales/puc" class="menuItem">Crear PUC</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>
        </q-expansion-item>
        <q-expansion-item
          expand-separator
          icon="settings"
          label="Configuración"
          class="expansion-block"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="Documentos">
            <q-card>
              <q-card-section>
                <router-link to="/generales/tipoimpuestos" class="menuItem">Tipos de Impuesto</router-link>
                <router-link to="/facturacion/tipo-documentos" class="menuItem">Tipos de Documentos</router-link>
                <router-link v-if="this.$store.state.jhsoft.recibos" to="/facturacion/tipo-recibos-caja" class="menuItem">Tipos de Recibos de Caja</router-link>
                <router-link v-if="this.$store.state.jhsoft.compras" to="/compras/tipo-compras" class="menuItem">Tipos de Compras</router-link>
                <router-link v-if="this.$store.state.jhsoft.compras" to="/compras/tipo-compro-egresos" class="menuItem">Tipos de Comprobantes de Egreso</router-link>
                <router-link v-if="this.$store.state.jhsoft.ordenes" to="/ordenes/tipo-ordenes" class="menuItem">Tipos de Ordenes</router-link>
                <router-link to="/egresos/tipos" class="menuItem">Tipos de Egresos</router-link>
                <router-link to="/facturacion/formaspago" class="menuItem">Formas de pago</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>

          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="General">
            <q-card>
              <q-card-section>
                <router-link to="/generales/impresoras" class="menuItem">Impresoras</router-link>
                <router-link to="/generales/basculas" class="menuItem">Basculas</router-link>
                <router-link to="/generales/empresa" class="menuItem">Empresa</router-link>
                <router-link v-if="this.$store.state.jhsoft.lotes" to="/generales/generalidades" class="menuItem">Generalidades</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>

          <q-expansion-item class="sub-expansion-block" expand-icon-class="text-white" :header-inset-level="0.5" :content-inset-level="1" label="Usuarios">
            <q-card>
              <q-card-section>
                <router-link to="/usuarios/categorias-permisos" class="menuItem">Categorias Permisos</router-link>
                <router-link to="/usuarios/permisos" class="menuItem">Permisos</router-link>
                <router-link to="/usuarios/roles" class="menuItem">Roles</router-link>
                <router-link to="/usuarios/asociar-permisos-rol" class="menuItem">Asociar Permisos a Rol</router-link>
                <router-link to="/register" class="menuItem">Usuarios</router-link>
                <router-link to="/changepass" class="menuItem">Cambiar Contraseña</router-link>
              </q-card-section>
            </q-card>
          </q-expansion-item>
        </q-expansion-item>
        <q-expansion-item
          class="expansion-block"
          expand-separator
          icon="assessment"
          label="Reportes"
          expand-icon-class="text-white"
          :content-inset-level="0.5"
        >
          <q-card>
            <q-card-section>
              <router-link v-if="this.$store.state.jhsoft.inventario" to="/reportes/inventario" class="menuItem">Inventario</router-link>
              <router-link v-if="this.$store.state.jhsoft.lotes" to="/reportes/inventario-produccion" class="menuItem">Inventario Produccion</router-link>
              <router-link to="/facturacion/movimientos" class="menuItem">Movimientos</router-link>
              <router-link v-if="this.$store.state.jhsoft.recibos" to="/facturacion/recibos" class="menuItem">Recibos</router-link>
              <router-link v-if="this.$store.state.jhsoft.compras" to="/compras/items" class="menuItem">Compras</router-link>
              <router-link v-if="this.$store.state.jhsoft.compras" to="/compras/compro-egresos" class="menuItem">Comprobantes de Egreso</router-link>
              <router-link to="/gestion-efectivo/resumen" class="menuItem">Egresos</router-link>
              <router-link v-if="this.$store.state.jhsoft.ordenes" to="/ordenes/resumen" class="menuItem">Ordenes</router-link>
              <router-link to="/generales/cuadrecaja" class="menuItem">Cuadre Z</router-link>
              <router-link to="/generales/tiquetesnofacturados" class="menuItem">Tiquete no facturado</router-link>
              <router-link to="/reportes/reportes-pdf" class="menuItem">Reportes Generados</router-link>
              <router-link v-if="this.$store.state.jhsoft.lotes" to="/reportes/peso-planta" class="menuItem">Informe Peso planta</router-link>
              <router-link v-if="this.$store.state.jhsoft.lotes" to="/reportes/productos-lote" class="menuItem">Informe Productos por Lote</router-link>
            </q-card-section>
          </q-card>
        </q-expansion-item>
        <div class="q-mt-md row box-buttons-menu">
          <div v-if="$auth.check()" class="text-center col-12">
              Usuario: {{ $auth.user().name }}
          </div>
        </div>
        <div class="row q-col-gutter-md" style="padding:10px">
          <div class="text-center col-6" v-if="globalValidarPermiso(71)">
            <q-btn v-if="cuadreAbierto === 0"  class="btn-coral" @click="openAbrirCaja = true">Abrir Caja</q-btn>
            <q-btn v-if="cuadreAbierto === 1"  class="btn-coral" @click="openCerrarCaja = true">Cerrar Caja</q-btn>
          </div>
          <div v-if="$auth.check()" class="text-center col-6">
            <a href="./" @click.prevent="$auth.logout(), $router.push({ name: 'login' })"><q-btn class="btn-naranja">Salir</q-btn></a>
          </div>
        </div>
        <!-- <div class="row q-mt-lg">
          <img id="logo" alt="Quasar logo" style="width:300px" src="~assets/logo-supercarnes.png">
        </div> -->
      </q-list>
    </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import { Money } from 'v-money'
const axios = require('axios')

export default {
  name: 'MenuPrincipal',
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
      docCompras: {},
      docOrdenes: {},
      docComproEgresos: {},
      docFacturacion: {},
      docRecibos: {},
      cuadreAbierto: null,
      openAbrirCaja: false,
      openCerrarCaja: false,
      temp: {
        monto_apertura: null,
        monto_cierre: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    abrirCaja () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/generales/cuadrecaja/abrircaja/' + app.temp.monto_apertura).then(
        function (response) {
          if (response.data === 'done') {
            app.temp.monto_apertura = null
            app.$q.notify({ color: 'positive', message: 'Caja Abierta' })
            app.verificarEstadoCaja()
          } else {
            app.$q.notify({ color: 'negative', message: response.data })
          }
        }
      )
    },
    cerrarCaja () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/generales/cuadrecaja/cerrarcaja/' + app.temp.monto_cierre).then(
        function (response) {
          if (response.data.respuesta === 'done') {
            app.monto_cierre = null
            app.$q.notify({ color: 'positive', message: 'Caja Cerrada' })
            app.verificarEstadoCaja()
            axios.get(app.$store.state.jhsoft.url + 'api/generales/cuadrecaja/imprimir/' + response.data.cuadre_id).then(
              function (response) {
                app.$q.notify({ color: 'positive', message: 'Cuadre impreso.' })
              }
            ).catch(function (error) {
              console.log(error)
              app.$q.notify({ color: 'negative', message: 'Error al imprimir, verifique la impresora.' })
            })
          }
        }
      )
    },
    verificarEstadoCaja () {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/generales/cuadrecaja/abiertoporusuario/' + app.$auth.user().id).then(
        function (response) {
          app.cuadreAbierto = response.data
        }
      )
    }
  },
  created: function () {
    this.globalGetForSelect('api/facturacion/tipos/estado/1', 'docFacturacion')
    this.globalGetForSelect('api/compras/tipos', 'docCompras')
    this.globalGetForSelect('api/compras/tiposcomproegreso', 'docComproEgresos')
    this.globalGetForSelect('api/facturacion/tiposrecibocaja', 'docRecibos')
    this.globalGetForSelect('api/ordenes/tipos', 'docOrdenes')
    this.verificarEstadoCaja()
  }
}
</script>

<style>
    .menuItem{
        display: block;
        text-decoration: none;
        color: #ffffff;
    }
    .menuItem:hover{
        color:#FF846E;
    }
    .expansion-block{
      background: #2E4DD4;
      color: #ffffff;
      font-family: poppins;
      font-weight: 400;
    }
    .sub-expansion-block{
      background: #2E4DD4;
      color: #ffffff;
    }
    .card
    .expansion-items{
      background-color: #ffffff;
    }
    .title-facturacion {
      color: black;
      text-align:center;
      margin-bottom: 0px;
      font-weight: 700
    }
    .v-money-menu{
      padding: 17px;
      border: none;
      border-bottom: 1px solid rgba(0,0,0,0.24);
      width: 100%;
    }
    .v-money-menu:focus{
      outline: none;
      border-bottom: 1px solid #027be3;
    }
    .v-money-label-menu{
      color: rgba(0,0,0,0.6);
      font-size: 16px;
      line-height: 20px;
      font-weight: 400;
      letter-spacing: 0.00937em;
      position: absolute;
      top: 16px;
    }
    .q-drawer__content.fit.scroll{
      background: #2E4DD4;
      color: #FFFFFF;
    }
    .menu-lateral .q-card{
      background: #2E4DD4;
    }
    .box-buttons-menu{
      border-top: 2px dashed #ffffff;
      padding-top: 15px;
    }
    .expansion-block .q-expansion-item__content,
    .expansion-block .q-expansion-item__content .q-expansion-item,
    .expansion-block .q-expansion-item__content .q-card,
    .expansion-block .q-expansion-item__content .q-expansion-item .q-card
    {
      background: #5A5AEA;
    }
    .sub-expansion-block .q-item__label{
      border-left: 5px solid #FFBC6E;
      padding-left: 5px;
    }
    .expansion-block .q-item{
      font-size: 17px;
    }
</style>
