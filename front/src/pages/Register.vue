<template>
    <div>
        <q-page padding>
            <h3>Usuarios</h3>
              <div class="alert alert-danger" v-if="has_error && !success">
                  <p v-if="error == 'registration_validation_error'">Erreur(s) de validation, veuillez consulter le(s) message(s) ci-dessous.</p>
                  <p v-else>Erreur, impossible de s'inscrire pour le moment. Si le problème persiste, veuillez contacter un administrateur.</p>
              </div>
            <div class="row q-col-gutter-md">
                  <form  class="row col-12 q-col-gutter-md" autocomplete="off" @submit.prevent="register" v-if="!success" method="post">
                    <div class="form-group col-12" v-bind:class="{ 'has-error': has_error && errors.name }">
                        <q-input type="text" required v-model="name" label="Nombre"/>
                        <span class="help-block" v-if="has_error && errors.name">{{ errors.name }}</span>
                    </div>
                    <div class="form-group col-6" v-if="!showForUpdate" v-bind:class="{ 'has-error': has_error && errors.password }">
                        <q-input type="password" required v-model="password" label="Contraseña"/>
                        <span class="help-block" v-if="has_error && errors.password">{{ errors.password }}</span>
                    </div>
                    <div class="form-group col-6" v-if="!showForUpdate" v-bind:class="{ 'has-error': has_error && errors.password }">
                        <q-input type="password" required v-model="password_confirmation" label="Confirmar Contraseña"/>
                    </div>
                    <div class="form-group col-6">
                        <div class="col">
                          <q-select
                            label="Seleccione Rol"
                            v-model="user_rol_id"
                            :options="roles"
                            option-value="id"
                            option-label="nombre"
                            option-disable="inactive"
                          />
                        </div>
                    </div>
                    <div class="form-group col-6">
                        <q-select
                          label="Impresora POS predeterminada"
                          v-model="gen_impresora_id"
                          :options="impresoras"
                          option-label="nombre"
                          option-value="id"
                        />
                    </div>
                    <div class="q-mt-sm">
                      <q-btn v-if="!showForUpdate" type="submit" color="primary" label="inscribir" />
                      <q-btn v-if="showForUpdate" color="primary" v-on:click="guardarEdicion()" label="Guardar Edición" />
                    </div>
                </form>
            </div>
            <div class="row q-col-gutter-md q-mt-md">
              <div class="col-md-12 col-12">
                <q-table
                    title="Usuarios"
                    :data="tableData"
                    :columns="columnsProductos"
                    :separator="separator"
                    :filter="filter"
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
                    <q-btn v-if="goblalValidarEstado(parseInt(props.value)) == 0" class="q-ml-xs" icon-right="add" v-on:click="editEstateItem('activar', props.value)" color="primary">Activar</q-btn>
                    <q-btn v-if="goblalValidarEstado(parseInt(props.value)) == 1" class="q-ml-xs" icon-right="remove" v-on:click="editEstateItem('inactivar', props.value)" color="negative">Desactivar</q-btn>
                    <q-btn class="q-ml-xs" icon-right="edit" v-on:click="globalValidate('editar', props.value)" color="warning">Editar</q-btn>
                    <q-btn class="q-ml-xs" icon-right="cached" v-on:click="reinitPassword(props.value)" color="positive">Reiniciar Contraseña</q-btn>
                  </q-td>
                </q-table>
              </div>
            </div>
        </q-page>
    </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'PageRegister',
  data () {
    return {
      urlAPI: 'api/users',
      tableData: [],
      roles: null,
      name: '',
      email: '',
      role: null,
      user_rol_id: null,
      password: '',
      password_confirmation: '',
      has_error: false,
      error: '',
      errors: {},
      showForUpdate: false,
      success: false,
      options: {
        grupos: this.grupos
      },
      gen_impresora_id: null,
      grupos: [],
      impresoras: [],
      columnsProductos: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'name', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'role', required: true, label: 'Rol', align: 'left', field: 'role', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'impresora_pos', required: true, label: 'Impresora POS', align: 'left', field: 'gen_impresora_id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    postEdit () {
      var app = this
      app.name = app.storeItems.user.name
      app.role = app.storeItems.user.role
      app.gen_impresora_id = app.impresoras.find(v => parseInt(v.id) === parseInt(app.storeItems.user.gen_impresora_id))
      app.showForUpdate = true
    },
    register () {
      var app = this
      this.$auth.register({
        data: {
          name: app.name,
          email: app.name + '@jhsoftlite.com',
          password: app.password,
          role: app.user_rol_id.nombre,
          user_rol_id: app.user_rol_id.id,
          gen_impresora_id: app.gen_impresora_id.id,
          password_confirmation: app.password_confirmation
        },
        success: function () {
          app.success = true
          app.name = ''
          app.email = ''
          app.role = null
          app.gen_impresora_id = null
          app.password = ''
          app.password_confirmation = ''
          app.$q.notify({ color: 'positive', message: 'Usuario Creado.' })
          this.getUsers()
        },
        error: function (res) {
          console.log(res.response.data.errors)
          app.has_error = true
          app.error = res.response.data.error
          app.errors = res.response.data.errors || {}
        }
      })
    },
    guardarEdicion () {
      var app = this
      app.$q.loading.show()
      var data = {
        name: app.name,
        role: app.role,
        gen_impresora_id: app.gen_impresora_id.id
      }
      axios.post(this.$store.state.jhsoft.url + this.urlAPI + '/' + app.storeItems.user.id, data).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'primary', message: 'Usuario Modificado!' })
            app.name = null
            app.role = null
            app.gen_impresora_id = null
            app.showForUpdate = false
            app.getUsers()
          } else {
            app.$q.notify({ color: 'negative', message: 'Hubo un error al Modificar el usuario.' })
          }
        }
      ).catch(function (error) {
        console.log(error)
        app.$q.notify({ color: 'negative', message: 'Hubo un error inesperado.' })
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    editEstateItem (text, id) {
      var app = this
      app.$q.loading.show()
      axios.get(this.$store.state.jhsoft.url + this.urlAPI + '/estado/' + id + '/' + text).then(
        function (response) {
          if (response.data === true) {
            app.$q.notify({ color: 'primary', message: 'item modificado!' })
            axios.get(app.$store.state.jhsoft.url + 'api/users').then(
              function (response) {
                app.tableData = response.data.users
              }
            ).catch(function (error) {
              console.log(error)
            }).finally(function () {
              app.$q.loading.hide()
            })
          } else {
            app.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo ' + text })
          }
        }
      ).catch(function (error) {
        console.log(error)
        app.$q.notify({ color: 'negative', message: 'Hubo un error inesperado.' })
      }).finally(function () {
      })
    },
    reinitPassword (id) {
      var app = this
      app.$q.loading.show()
      axios.get(this.$store.state.jhsoft.url + this.urlAPI + '/reinitpassword/' + id).then(
        function (response) {
          if (response.data === true) {
            app.$q.notify({ color: 'primary', message: 'Contraseña reiniciada!' })
          } else {
            this.$q.notify({ color: 'negative', message: 'Hubo un error no se pudo reiniciar la contraseña.' })
          }
        }
      ).catch(function (error) {
        console.log(error)
        app.$q.notify({ color: 'negative', message: 'Hubo un error inesperado.' })
      }).finally(function () {
        app.$q.loading.hide()
      })
    },
    getUsers () {
      var app = this
      app.$q.loading.show()
      axios.get(this.$store.state.jhsoft.url + 'api/users').then(
        function (response) {
          app.tableData = response.data.users
        }
      ).catch(function (error) {
        console.log(error)
      }).finally(function () {
        app.$q.loading.hide()
      })
    }
  },
  created: function () {
    this.getUsers()
    this.globalGetForSelect('api/generales/impresoras/estado/activos', 'impresoras')
    this.globalGetForSelect('api/users/permisos/roles', 'roles')
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
