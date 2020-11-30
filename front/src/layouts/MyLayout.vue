<template>
  <q-layout view="lHh Lpr lFf">
    <q-header>
      <q-toolbar
        color="primary"
        :glossy="$q.theme === 'mat'"
        :inverted="$q.theme === 'ios'"
      >
        <q-btn
          flat
          dense
          round
          @click="leftDrawerOpen = !leftDrawerOpen"
          aria-label="Menu"
        >
          <q-icon name="menu" />
        </q-btn>

        <q-toolbar-title>
          SGC
          <div slot="subtitle">Sistema de Gestion Carnico</div>
        </q-toolbar-title>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      :content-class="$q.theme === 'mat' ? 'bg-grey-2' : null"
    >
    <MenuPrincipal/>
    </q-drawer>
    <q-page-container class="page-general">
      <router-view />
    </q-page-container>
  </q-layout>
</template>

<script>
import { openURL } from 'quasar'
import MenuPrincipal from 'components/generales/MenuPrincipal.vue'

export default {
  name: 'MyLayout',
  data () {
    return {
      leftDrawerOpen: this.$q.platform.is.desktop
    }
  },
  components: {
    MenuPrincipal
  },
  methods: {
    openURL
  }
}
</script>

<style>
  .q-toolbar{
    background: #2E4DD4;
  }
  .page-general{
    background: #2E4DD4;
    color: #2E4765;
  }
  .page-general .q-page{
    background: #F5F6FA;
    border-radius: 50px 0px 0px 50px;
  }
  .page-general .q-page .q-field{
    background: #ffffff;
    border-radius: 10px;
  }
  .page-general .q-page .q-field .q-field__control{
    border-radius: 10px;
  }
  .page-general .q-page .q-field .q-field__control .q-field__label{
    padding-left: 5px;
  }
  .date-field{
    padding-bottom: 0px;
  }
  .page-general .q-page .q-field .q-field__control .q-select__input{
    padding-left: 5px;
  }
  .page-general .q-page .q-field .q-field__control .q-field__native{
    padding-left: 5px;
  }
  .w-100{
    width: 100%;
  }
  .btn-coral{
      background: #FF7373;
      color: #ffffff;
      border-radius: 6px;
    }
  .btn-naranja{
    background: #FFBC6E;
    color: #ffffff;
    border-radius: 6px;
  }
  .btn-limon{
    background: #84E0BE;
    border: 1px solid #00BF78;
    border-radius: 6px;
    color: #ffffff;
  }
  .btn-azul{
    background: #9FD4FD;
    border: 1px solid #9AC4E3;
    border-radius: 6px;
    color: #ffffff;
  }
  .boton-delete{
    margin-top: 0px;
    font-size: 6px!important;
  }
  .box-resumen-pagos{
      border: 1px dashed #B7B7B7;
      color: #2E4765;
      padding: 5px;
    }
  .box-resumen-movimiento{
    margin-top: 30px;
    padding-top: 30px;
    border-top: 2px dashed #2E4765;
  }
  .box-resumen-abonos .q-field__control,
  .box-resumen-abonos .q-field{
    border-radius: 0px!important;
  }
  .box-resumen-abonos .span-abono{
    padding: 15px 10px 0px 0px;
  }
  .div-86{
    width:86%;
    margin-left:7%
  }
</style>
