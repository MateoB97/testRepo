<template>
  <div>
    <q-page padding>
        <!-- inicio popup Errores Fact Elect -->
          <q-dialog v-model="openedEliminarDatosHabFE" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 250px; max-width: 800px; background-color: #ffffff; color: #FFFFFF">
              <q-header >
                <q-toolbar style="background-color: #7E7EF4!important;">
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col">
                        <q-input type="password" label="Ingrese la contraseña para eliminar" v-model="contraseñaEliminarHabFE"></q-input>
                      </div>
                    </div>
                    <div class="row q-col-gutter-sm q-mt-md">
                      <div class="col">
                        <q-btn v-close-popup color="negative" v-on:click="eliminarDatosHabFE()" label="Eliminar Datos Habil FE" />
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup Errores Fact Elect -->
        <!-- inicio popup Limpiar Basculas -->
          <q-dialog v-model="openedLimpiarBascula" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
            <q-layout view="Lhh lpR fff" container style="height: 250px; max-width: 800px; background-color: #ffffff; color: #FFFFFF">
              <q-header >
                <q-toolbar style="background-color: #7E7EF4!important;">
                  <q-btn flat v-close-popup round dense icon="close" />
                </q-toolbar>
              </q-header>

              <q-page-container>
                <q-page padding>
                  <div class="overflow-hidden">
                    <div class="row q-col-gutter-sm">
                      <div class="col">
                        <q-input type="password" label="Ingrese la contraseña para limpiar " v-model="contraseñaLimpiarBascula"></q-input>
                      </div>
                    </div>
                    <div class="row q-col-gutter-sm q-mt-md">
                      <div class="col">
                        <q-btn v-close-popup color="negative" v-on:click="limpiarTiquetesBasculas()" label="Limpiar Tiquetes no Facturados" />
                      </div>
                    </div>
                  </div>
                </q-page>
              </q-page-container>
            </q-layout>
          </q-dialog>
        <!-- fin popup Limpiar Basculas -->

        <h3>Datos de la Empresa</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col-4">
                    <q-input v-model="storeItems.nombre" label="Nombre"/>
                </div>
                <div class="col-4">
                    <q-input v-model="storeItems.razon_social" label="Razon Social"/>
                </div>
                <div class="col-4">
                    <q-input v-model="storeItems.direccion" label="Dirección"/>
                </div>
                <div class="col-3">
                    <q-input v-model="storeItems.telefono" label="Teléfono"/>
                </div>
                <div class="col-3">
                    <q-input v-model="storeItems.tipo_regimen" label="Tipo Regimen"/>
                </div>
                <div class="col-3">
                    <q-input v-model="storeItems.nit" label="NIT"/>
                </div>
                <div class="col-3">
                    <q-checkbox v-model="storeItems.fact_grupo" label="Factura por Grupos"></q-checkbox>
                    <q-checkbox v-model="storeItems.print_logo_pos" label="POS con logo"></q-checkbox>
                    <q-checkbox v-model="storeItems.bloquear_tercero" label="Bloquear Tercero?"></q-checkbox>
                    <q-checkbox v-model="storeItems.precio_bascula_marques" label="Activar Precio Bascula Marques?"></q-checkbox>
                    <!-- <q-checkbox v-model="storeItems.activar_precio_bascula" label="Activar Precio Bascula Marques?"></q-checkbox> -->
                </div>
                <div class="col-6">
                  <q-select
                    class="w-100"
                    v-model="datos.departamento_id"
                    use-input
                    hide-selected
                    fill-input
                    option-value="id"
                    option-label="nombre"
                    label="Departamento"
                    option-disable="inactive"
                    emit-value
                    map-options
                    input-debounce="0"
                    :options="options.departamentos"
                    @filter="filterDepartamentos"
                    @input="selectedDepartamento"
                  >
                    <template v-slot:no-option>
                      <q-item>
                        <q-item-section class="text-grey">
                          No results
                        </q-item-section>
                      </q-item>
                    </template>
                  </q-select>
                </div>
                <div class="col-6">
                  <q-select
                    class="w-100"
                    v-model="storeItems.gen_municipios_id"
                    use-input
                    hide-selected
                    fill-input
                    option-value="id"
                    option-label="nombre"
                    label="Municipio"
                    option-disable="inactive"
                    emit-value
                    map-options
                    input-debounce="0"
                    :options="options.municipios"
                    @filter="filterMunicipios"
                  >
                    <template v-slot:no-option>
                      <q-item>
                        <q-item-section class="text-grey">
                          No results
                        </q-item-section>
                      </q-item>
                    </template>
                  </q-select>
                </div>
                <div class="col-6">
                    <SelectTerceroSucursal v-model="sucursal" :editor="sucursal" columnas='col-3' labelTercero='Tercero POS por Defecto'/>
                </div>
                <div class="col-3">
                    <q-select
                        label="Lista de precios defecto"
                        v-model="storeItems.prod_lista_precios_id"
                        :options="listaprecios"
                        option-value="id"
                        option-label="nombre"
                        emit-value
                        map-options
                      />
                </div>
                <div class="col-3">
                    <q-select
                        label="Tipo Escaner"
                        v-model="storeItems.tipo_escaner"
                        :options="tipos_escaner"
                        option-value="value"
                        option-label="label"
                        emit-value
                        map-options
                      />
                </div>
                <div class="col-12">
                  <q-input v-model="storeItems.licencia" label="Licencia"/>
                </div>
                <!-- <div class="col-6">
                  <q-btn @click="exportarProductos()">Exportar Productos</q-btn>
                </div>
                <div class="col-6">
                  <q-btn @click="importProductos()">Importar Productos</q-btn>
                </div> -->
                <div class="col-12">
                  <h5 class="no-margin">Configuración impresora POS</h5>
                </div>
                <div class="col-12">
                    <q-input
                      ref="input"
                      v-model="storeItems.cantidad_caracteres"
                      label="Cantidad de caracteres"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <!-- Bascula DIBAL -->
                <div class="col-12">
                  <h5 class="no-margin">Configuración Bascula Dibal</h5>
                </div>
                <div class="col-10">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_archivo_tiquetes_dibal"
                      label="Ruta Archivo Bascula"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <div class="col-2">
                    <q-select outlined
                      v-model="storeItems.secciones_dibal"
                      :options="numero_secciones"
                      label="Secciones"
                    ></q-select>
                </div>
                <div class="col-9">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_archivo_tx_dival"
                      label="Ruta archivo TX subir datos a bascula dibal"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <div class="col-3">
                  <q-btn color="positive" v-on:click="subirDatosBasculaDibal(storeItems.secciones_dibal)" label="Generar Archivo TX" />
                </div>
                <!-- BASCULA EPELSA -->
                <div class="col-12">
                  <h5 class="no-margin">Configuración Bascula epelsa</h5>
                </div>
                <div class="col-10">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_archivo_tiquetes_epelsa"
                      label="Ruta Archivo Tiquetes Epelsa"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                  <div class="col-2">
                    <q-select outlined
                      v-model="storeItems.secciones_epelsa"
                      :options="numero_secciones"
                      label="Secciones"
                    ></q-select>
                </div>
                <div class="col-9">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_archivo_precios_epelsa"
                      label="Ruta archivo precios bascula Epelsa"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <div class="col-3">
                  <q-btn color="positive" v-on:click="subirDatosBasculaEpelsa(storeItems.secciones_epelsa)" label="Generar Archivo Precios" />
                </div>
                <!-- BASCULA ISHIDA -->
                 <div class="col-12">
                  <h5 class="no-margin">Configuración Bascula Ishida</h5>
                </div>
                <div class="col-10">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_archivo_tiquetes_ishida"
                      label="Ruta Archivo Bascula"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <div class="col-2">
                    <q-select outlined
                      v-model="storeItems.secciones_ishida"
                      :options="numero_secciones"
                      label="Secciones"
                    ></q-select>
                </div>
                <div class="col-9">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_archivo_txt_ishida"
                      label="Ruta archivo PRODUCTOS subir datos a bascula ishida"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <div class="col-3">
                  <q-btn color="positive" v-on:click="subirDatosBasculaIshida(storeItems.secciones_ishida)" label="Generar Archivo PRODUCTOS" />
                </div>
                <!-- BASCULA MARQUES -->
                <div class="col-12">
                  <h5 class="no-margin">Configuración Bascula Marques</h5>
                </div>
                <div class="col-6">
                    <q-input
                      ref="input"
                      v-model="storeItems.ruta_ip_marques"
                      label="Direccion IP de la bascula Marques maestra"
                      bottom-slots
                      hint="use / en vez de \"
                      error-message="Ha usado \ y no es valido"
                      :error="!isValid"
                    ></q-input>
                </div>
                <div class="col-2">
                    <q-select outlined
                      v-model="storeItems.secciones_marquez"
                      :options="numero_secciones"
                      label="Secciones"
                    ></q-select>
                </div>
                <div class="col-2">
                  <q-btn color="positive" v-on:click="eliminarFamiliasMarques()" label="Eliminar familias" />
                </div>
                <div class="col-2">
                  <q-btn color="positive" v-on:click="eliminarProductosMarques()" label="Eliminar Productos" />
                </div>
                <div class="col-2">
                  <q-btn color="positive" v-on:click="subirDatosBasculaMarques(storeItems.secciones_marquez)" label="Enviar datos a bascula" />
                </div>
                <div class="row q-col-gutter-md col-12" v-if="this.$store.state.jhsoft.tipo_licencia === 3 || this.$store.state.jhsoft.tipo_licencia === 4">
                  <!-- Fac electronica -->
                  <div class="col-12">
                    <h5 class="no-margin">Configuración Factura Electronica</h5>
                  </div>
                  <div class="col-6">
                      <q-input v-model="storeItems.token_fac_elect" label="Token Facturación Electrónica"/>
                  </div>
                  <div class="col-6">
                      <q-input v-model="storeItems.test_id_fe" label="Id Test Dian FE"/>
                  </div>
                  <div class="col-4">
                    <q-select
                      v-model="storeItems.producto_bolsa_id"
                      use-input
                      hide-selected
                      fill-input
                      option-value="id"
                      option-label="nombre"
                      label="Producto Impuesto Bolsa"
                      option-disable="inactive"
                      input-debounce="0"
                      emit-value
                      map-options
                      :options="options.productos"
                      @filter="filterProductoManual"
                    >
                      <template v-slot:no-option>
                        <q-item>
                          <q-item-section class="text-grey">
                            No results
                          </q-item-section>
                        </q-item>
                      </template>
                      <template v-slot:option="scope">
                        <q-item
                          v-bind="scope.itemProps"
                          v-on="scope.itemEvents"
                        >
                          <q-item-section>
                            <q-item-label v-html="scope.opt.codigo + ' - ' + scope.opt.nombre" />
                          </q-item-section>
                        </q-item>
                      </template>
                    </q-select>
                  </div>
                  <div class="col-4">
                      <q-select
                        label="Seleccione impuesto Excluido"
                        v-model="storeItems.gen_iva_excluido_id"
                        :options="impuestos"
                        option-value="id"
                        option-label="nombre"
                        emit-value
                        map-options
                      />
                  </div>
                  <div class="col-4">
                    <q-input type="email" v-model="storeItems.email_backup_fact_elect" label="Email Backup FE"/>
                  </div>
                  <div class="col">
                    <q-btn color="negative" v-on:click="openedEliminarDatosHabFE = true" label="Eliminar Datos Habil FE" />
                  </div>
                  <div class="col">
                    <q-btn color="negative" v-on:click="openedLimpiarBascula = true" label="Limpiar Tiquetes" />
                  </div>
                </div>
            </div>
            <div class="row q-mt-md">
                <div class="col-3">
                    <q-btn color="primary" v-on:click="globalValidate('guardar-edicion', storeItems.id)" label="Guardar Edición" />
                </div>
            </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'
const axios = require('axios')

export default {
  name: 'GenImpuesto',
  components: {
    SelectTerceroSucursal
  },
  data: function () {
    return {
      showForUpdate: false,
      urlAPI: 'api/generales/empresa',
      tableData: [],
      tipos: [],
      contraseñaEliminarHabFE: null,
      contraseñaLimpiarBascula: null,
      openedEliminarDatosHabFE: false,
      openedLimpiarBascula: false,
      groupSelected: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'valor_porcentaje', required: true, label: '%', align: 'left', field: 'valor_porcentaje', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'tipo', 'actions'],
      separator: 'horizontal',
      filter: '',
      listaprecios: [],
      productos: [],
      impuestos: [],
      options: {
        listaprecios: this.listaprecios,
        departamentos: this.departamentos,
        municipios: this.municipios,
        productos: this.productos
      },
      sucursal: null,
      storeItems: {
        nombre: null,
        nit: null,
        direccion: null,
        telefono: null,
        razon_social: null,
        tipo_regimen: null,
        ruta_archivo_tiquetes_dibal: '',
        tercero_sucursal_pos_id: null,
        prod_lista_precios_id: null,
        gen_municipios_id: null,
        ruta_archivo_tx_dival: null,
        ruta_archivo_precio_ishida: null,
        ruta_ip_marques: null,
        tipoEscaner: null,
        producto_bolsa_id: null,
        resolucion_soenac_id: null,
        test_id_fe: null,
        cantidad_caracteres: 0,
        precio_bascula_marques: null,
        secciones_dibal: 0,
        secciones_marquez: 0,
        secciones_epelsa: 0,
        secciones_ishida: 0
      },
      numero_secciones: [1, 2, 3, 4, 5, 6, 7, 8, 9],
      tipos_escaner: [
        { label: 'Bascula Dibal',
          value: '1'
        },
        {
          label: 'Bascula Marques',
          value: '2'
        },
        {
          label: 'Codigo de Barras',
          value: '3'
        },
        {
          label: 'Bascula Epelsa',
          value: '4'
        },
        {
          label: 'Despacho',
          value: '5'
        },
        {
          label: 'Etiqueta por producto',
          value: '6'
        }
      ],
      departamentos: [],
      municipios: [],
      datos: {
        departamento_id: null
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
      this.getData()
    },
    preSave () {
      // var activated = this.storeItems.activar_precio_bascula === true ? 1 : 0
      this.storeItems.tercero_sucursal_pos_id = this.sucursal
      this.storeItems.prod_lista_precios_id = this.storeItems.prod_lista_precios_id.id
      if (this.storeItems.gen_municipios_id.id) {
        this.storeItems.gen_municipios_id = this.storeItems.gen_municipios_id.id
      }
      // this.storeItems.precio_bascula_marques = activated
    },
    postEdit () {
    },
    filterProductoManual (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productos = this.productos.filter(v => v.codigo.toLowerCase().indexOf(needle) > -1)
        if (this.options.productos.length < 1) {
          this.options.productos = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
        }
      })
    },
    filterMunicipios (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.municipios = this.municipios.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    filterDepartamentos (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.departamentos = this.departamentos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    selectedDepartamento () {
      var app = this
      app.$q.loading.show()
      app.storeItems.gen_municipios_id = null
      axios.get(this.$store.state.jhsoft.url + 'api/generales/municipios/filtro/pordepartamento/' + this.datos.departamento_id).then(
        function (response) {
          app.municipios = response.data
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    subirDatosBasculaDibal (seccion) {
      var app = this
      app.$q.loading.show()
      // console.log(seccion, '#seccion')
      seccion = (seccion - seccion) + 1 * (seccion * 2)
      axios.get(this.$store.state.jhsoft.url + 'api/productos/configuracion/cargarabascula/' + seccion).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Archivo Generado.' })
          }
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    subirDatosBasculaEpelsa (seccion) {
      var app = this
      app.$q.loading.show()
      axios.get(this.$store.state.jhsoft.url + 'api/productos/configuracion/subirpreciosepelsa/' + seccion).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Archivo Generado.' })
          }
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    subirDatosBasculaIshida (seccion) {
      var app = this
      app.$q.loading.show()
      axios.get(this.$store.state.jhsoft.url + 'api/productos/configuracion/subirpreciosishida/' + seccion).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'Archivo Generado.' })
          }
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    eliminarDatosHabFE () {
      if (this.contraseñaEliminarHabFE === 'g6N|o?y@') {
        this.contraseñaEliminarHabFE = null
        var app = this
        app.$q.loading.show()
        axios.get(this.$store.state.jhsoft.url + 'api/facturacion/facturacionelectronica/eliminardatoshabilitacion').then(
          function (response) {
            if (response.data === 'done') {
              app.$q.notify({ color: 'positive', message: 'Datos Eliminados.' })
            }
          }
        ).catch(function (error) {
          console.log(error)
        }).finally(function () {
          app.$q.loading.hide()
        })
      } else {
        this.contraseñaEliminarHabFE = null
        this.$q.notify({ color: 'negative', message: 'Contraseña Incorrecta' })
      }
    },
    limpiarTiquetesBasculas () {
      if (this.contraseñaLimpiarBascula === 'adminsgc3.1416') {
        this.contraseñaLimpiarBascula = null
        var app = this
        app.$q.loading.show()
        axios.get(this.$store.state.jhsoft.url + 'api/facturacion/tiquetesnofacturados/limpiartiquetes').then(
          function (response) {
            if (response.data === 'done') {
              app.$q.notify({ color: 'positive', message: 'Datos Eliminados.' })
            }
          }
        ).catch(function (error) {
          console.log(error)
        }).finally(function () {
          app.$q.loading.hide()
        })
      } else {
        this.contraseñaLimpiarBascula = null
        this.$q.notify({ color: 'negative', message: 'Contraseña Incorrecta' })
      }
    },
    subirDatosBasculaMarques (seccion) {
      var app = this
      app.$q.loading.show()
      let ipMarquesMaster = app.storeItems.ruta_ip_marques.split('&')[0].split('-')
      axios.get(this.$store.state.jhsoft.url + 'api/productos/configuracion/subirdatosbasculamarques').then(
        function (response) {
          axios.put('http://' + ipMarquesMaster[0] + '/year/familias', response.data[0]).then(
            function (response1) {
              app.$q.notify({ color: 'positive', message: 'Familias enviadas.' })
              let j = 0
              while (j < response.data[1].length) {
                axios.put('http://' + ipMarquesMaster[0] + '/year/artigos', response.data[1][j]).then(
                  function (response2) {
                    app.$q.notify({ color: 'positive', message: 'Articulos enviados' })
                  }
                )
                j = j + 1
              }
            }
          )
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    eliminarFamiliasMarques () {
      var app = this
      let ipMarquesMaster = app.storeItems.ruta_ip_marques.split('&')[0].split('-')
      axios.delete('http://' + ipMarquesMaster[0] + '/year/familias?seek={"codigo":"0"}&limit=100').then(
        function (response) {
          app.$q.notify({ color: 'positive', message: 'familias eliminadas' })
        }
      )
      // let i = 0
      // while (i < dataSplit.length) {
      //   // axios.delete(app.storeItems.ruta_ip_marques + '/year/familias?seek={"codigo":"0"}&limit=100').then(
      //   axios.delete('http://' + dataSplit[i].substring(0, 18) + '/year/familias?seek={"codigo":"0"}&limit=100').then(
      //     function (response2) {
      //       app.$q.notify({ color: 'positive', message: 'familias eliminadas' })
      //     }
      //   )
      //   i = i + 1
      // }
    },
    exportarProductos () {
      axios.get(this.$store.state.jhsoft.url + 'api/productos/export/data').then(
        function (response) {
          localStorage.productosData = JSON.stringify(response.data)
        }
      )
    },
    importProductos () {
      var productosImport = []
      if (localStorage.productosData) {
        productosImport = JSON.parse(localStorage.productosData)
      }
      axios.post(this.$store.state.jhsoft.url + 'api/productos/import/data', productosImport).then(
        function (response) {
          console.log('done')
        }
      )
    },
    eliminarProductosMarques () {
      var app = this
      let ipMarquesMaster = app.storeItems.ruta_ip_marques.split('&')[0].split('-')
      // axios.delete(app.storeItems.ruta_ip_marques + '/year/artigos?seek={"codigo":"0"}&limit=100').then(
      axios.delete('http://' + ipMarquesMaster[0] + '/year/artigos?seek={"codigo":"0"}&limit=100').then(
        function (response3) {
          app.$q.notify({ color: 'positive', message: 'Articulos eliminados' })
        }
      )
    },
    getData () {
      var app = this
      axios.get(this.$store.state.jhsoft.url + 'api/productos/listadeprecios/estado/activos').then(
        function (response1) {
          app.listaprecios = response1.data
          axios.get(app.$store.state.jhsoft.url + app.urlAPI).then(
            function (response) {
              app.storeItems = response.data
              app.storeItems.prod_lista_precios_id = app.listaprecios.find(element => parseInt(element.id) === parseInt(response.data.prod_lista_precios_id))
              app.sucursal = response.data.tercero_sucursal_pos_id
              axios.get(app.$store.state.jhsoft.url + 'api/generales/municipios').then(
                function (response) {
                  app.municipios = response.data
                  app.storeItems.gen_municipios_id = app.municipios.find(element => parseInt(element.id) === parseInt(app.storeItems.gen_municipios_id))
                  if (app.storeItems.gen_municipios_id) {
                    app.datos.departamento_id = app.departamentos.find(element => parseInt(element.id) === parseInt(app.storeItems.gen_municipios_id.departamento_id))
                    axios.get(app.$store.state.jhsoft.url + 'api/generales/municipios/filtro/pordepartamento/' + app.datos.departamento_id.id).then(
                      function (response) {
                        app.municipios = response.data
                      }
                    )
                  }
                }
              )
            }
          )
        }
      )
    }
  },
  created: function () {
    this.getData()
    this.globalGetForSelect('api/generales/departamentos', 'departamentos')
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
    this.globalGetForSelect('api/generales/iva', 'impuestos')
  },
  computed: {
    isValid () {
      if (this.storeItems.ruta_archivo_tiquetes_dibal.indexOf('\\') >= 0) {
        return false
      } else {
        return true
      }
    }
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
    .no-margin {
      margin:0px;
    }
</style>
