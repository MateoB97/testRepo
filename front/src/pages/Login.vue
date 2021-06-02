<template>
    <div class="container main-login">
      <div class="column q-pa-md row items-center q-gutter-md">
        <q-card class="my-card">
          <q-card-section>
            <div class="alert alert-danger" v-if="has_error">
                <p style="color: red">Error, Imposible conectar con estas credenciales.</p>
            </div>
            <form autocomplete="off" @submit.prevent="login" method="post">
                <div class="form-group">
                    <q-input type="text" required v-model="email" label="Usuario"/>
                </div>
                <div class="form-group">
                    <q-input type="password" required v-model="password" label="Contraseña"/>
                </div>
                <div class="q-mt-sm">
                  <q-btn type="submit" color="primary" label="Ingresar" />
                </div>
            </form>
          </q-card-section>
        </q-card>
      </div>
    </div>
</template>
<script>
export default {
  data () {
    return {
      email: null,
      password: null,
      has_error: false
    }
  },
  mounted () {
    //
  },
  methods: {
    login () {
      this.has_error = false
      this.$q.loading.show()
      // get the redirect object
      var app = this
      this.$auth.login({
        params: {
          email: app.email + '@jhsoftlite.com',
          password: app.password
        },
        success: function (data) {
          localStorage.user = JSON.stringify(this.$auth.user())
          this.$q.loading.hide()
          this.$router.push({ name: 'home' })
        },
        error: function () {
          this.$q.loading.hide()
          this.has_error = true
        },
        rememberMe: true,
        fetchUser: true
      })
    }
  }
}
</script>

<style lang="stylus" scoped>
  .my-card {
    width: 100%
    max-width: 500px
  }
  .main-login{
    background-image: url("~assets/logo.png")
    background-size: contain;
    background-repeat: no-repeat
    min-height: 100vh
    background-position-x: center
  }
  .titulo-login {
    padding: 0px
    margin 0px
    text-align: center
  }
  #logo {
    width:100%
  }

  @media screen and (max-width: 600px) {
    .q-card {
      box-shadow: none;
    }
  }
</style>
