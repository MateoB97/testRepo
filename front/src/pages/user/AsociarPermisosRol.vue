<template>
  <div>
    <q-page padding>

        <h3>Asociar Permisos a Rol</h3>
        <div class="row">
            <div class="col">
                    <q-select
                      label="Seleccione Rol"
                      v-model="rol"
                      :options="roles"
                      option-value="id"
                      option-label="nombre"
                      option-disable="inactive"
                      @input="setPermisos"
                    />
                </div>
        </div>
        <div class="row">
            <div v-for="(categoria, key) in permisosAgrupados" :key="key" class="col-12">
              <ul id="example-2">
                {{ key }}
                <li v-for="permiso in categoria" :key="permiso.id">
                  <q-checkbox v-model="storeItems.permisos" :val="permiso.id" :label="permiso.nombre" />
                </li>
              </ul>
            </div>
        </div>
        <div class="row q-mt-sm">
            <div class="col-2">
                <q-btn color="primary" v-on:click="globalValidate('guardar-edicion', rol.id)" label="Guardar Edición" />
            </div>
        </div>
    </q-page>
  </div>
</template>

<script>
import { globalFunctions } from 'boot/mixins.js'

export default {
  name: 'CreateTipoAlmacen',
  data: function () {
    return {
      storeItems: {
        permisos: []
      },
      urlAPI: 'api/users/permisos/roles',
      rol: null,
      permisosAgrupados: [],
      roles: [],
      showForUpdate: false,
      tableData: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'nombre', required: true, label: 'Nombre', align: 'left', field: 'nombre', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'actions'],
      separator: 'horizontal',
      filter: ''
    }
  },
  mixins: [globalFunctions],
  methods: {
    postSave () {
      this.storeItems.permisos = []
      this.rol = null
      this.globalGetForSelect('api/users/permisos/roles', 'roles')
    },
    preSave () {
      this.storeItems.permisos = this.storeItems.permisos.join()
    },
    postEdit () {
    },
    setPermisos () {
      if (this.rol.permisos !== null) {
        this.storeItems.permisos = this.rol.permisos.split(',')
      } else {
        this.storeItems.permisos = []
      }
    }
  },
  created: function () {
    this.globalGetForSelect('api/users/permisos/roles', 'roles')
    this.globalGetForSelect('api/users/permisos/permisos-agrupados-categorias', 'permisosAgrupados')
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
