import axios from 'axios'
import VueAxios from 'vue-axios'
import VueAuth from '@websanova/vue-auth'
// import vuex from '../store/module-jhsoft/state.js'

export default ({ Vue }) => {
  Vue.prototype.$axios = axios
  Vue.use(VueAxios, axios)
  Vue.use(VueAuth, {
    auth: {
      request (req, token) {
        if (req.url.indexOf('/year/') > 0) {
        } else if (req.url.indexOf('apifacturacionelectronica') > 0) {
          // this.options.http._setHeaders.call(this, req, { Authorization: 'Bearer ' + '1twr3CRtCM96n9cyL9giEIYk5nIO7MYqsDcx9DYoSj4PKbdKsUK6iksVMHhJJT75I8AIJk7l9ynrPTEP' })
        } else {
          this.options.http._setHeaders.call(this, req, { Authorization: 'Bearer ' + token })
        }
      },
      response: function (res) {
        return (res.data.data || {}).token
      }
    },
    http: require('@websanova/vue-auth/drivers/http/axios.1.x.js'),
    router: require('@websanova/vue-auth/drivers/router/vue-router.2.x.js'),
    tokenDefaultName: 'sgc_jwt_token',
    notFoundRedirect: false,
    rolesVar: 'role',
    registerData: { url: 'auth/register', method: 'POST', redirect: '/' },
    refreshData: { url: 'auth/refresh', method: 'GET', enabled: true, interval: 5 }
  })
  // Vue.axios.defaults.baseURL = 'http://fusion.test/api'
  // Vue.axios.defaults.baseURL = 'http://desktop-caermcs/sgc/back/public/api'
  // Vue.axios.defaults.baseURL = 'http://192.168.1.8/sgcdev/back/public/api'
  // Vue.axios.defaults.baseURL = 'http://192.168.1.169/sgc/back/public/api'
  // Vue.axios.defaults.baseURL = 'http://192.168.1.184/sgc/back/public/api'
  // Vue.axios.defaults.baseURL = 'http://localhost/sgcdev/back/public/api'
  Vue.axios.defaults.baseURL = 'http://localhost/sgc/back/public/api'

  axios.interceptors.request.use(function (config) {
    return config
  }, function (error) {
    // Do something with request error
    return Promise.reject(error)
  })

  Vue.axios.interceptors.response.use(function (response) {
    // if (response.data.error === 'Unauthorized') {
    //   localStorage.removeItem('default_auth_token')
    //   document.location.reload()
    // }
    if (response.data.message === 'Token has expired and can no longer be refreshed') {
      localStorage.removeItem('default_auth_token')
      document.location.reload()
    }
    return response
  }, function (error) {
    if (error.response.status === 401) {
      console.log('default_auth_token')
      Vue.auth.refresh()
    }
  })
}
