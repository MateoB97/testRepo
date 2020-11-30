<template>
    <div>
        <q-page padding>
            <h3>Usuarios</h3>
            <div class="row q-col-gutter-md">
                  <form  class="col-12 row q-col-gutter-md" autocomplete="off" @submit.prevent="changePass" method="post">
                    <div class="form-group col-4">
                        <q-input type="password" required v-model="storeItems.old_pass" label="Password Anterior"/>
                    </div>
                    <div class="form-group col-4">
                        <q-input type="password" required v-model="storeItems.new_pass" label="Contraseña Nueva"/>
                    </div>
                    <div class="form-group col-4">
                        <q-input type="password" required v-model="storeItems.new_pass_confirmation" label="Confirmar Contraseña Nueva"/>
                    </div>
                    <div class="q-mt-sm">
                      <q-btn type="submit" color="primary" label="Cambiar" />
                    </div>
                </form>
            </div>
        </q-page>
    </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'
const axios = require('axios')

export default {
  name: 'PageCambiarPass',
  data () {
    return {
      urlAPI: 'api/users',
      storeItems: {
        old_pass: null,
        new_pass: null,
        new_pass_confirmation: null,
        email: this.$auth.user().email
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    changePass () {
      var app = this
      if ((this.storeItems.new_pass === this.storeItems.new_pass_confirmation) && (this.storeItems.new_pass !== null)) {
        axios.post(this.$store.state.jhsoft.url + 'api/users/editar/cambiarpass', this.storeItems).then(
          function (response) {
            if (response.data === 'done') {
              app.$q.notify({ color: 'positive', message: 'La contraseña ha cambiado!' })
            } else {
              app.$q.notify({ color: 'negative', message: response.data })
            }
            app.storeItems = {
              old_pass: null,
              new_pass: null,
              new_pass_confirmation: null
            }
          }
        ).catch(function (error) {
          console.log(error)
        }).finally(function () {
        })
      } else {
        app.$q.notify({ color: 'negative', message: 'Las constraseñas nuevas no coinciden!' })
      }
    }
  },
  created: function () {
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
