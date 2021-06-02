<template>
  <div>
    <q-page padding>

      <!-- Agregar programacion -->
        <q-dialog v-model="openedProgramacion" persistent :content-css="{minWidth: '80vw', minHeight: '40vh'}">
          <q-layout view="Lhh lpR fff" container style="height: 600px; max-width: 800px" class="bg-white">
            <q-page-container>
              <q-page padding>
                <h3>Agregar Programación</h3>
              <div class="overflow-hidden">
                <div class="row q-col-gutter-sm">
                  <div class="col-6">
                    <q-input label="Fecha de Desposte" v-model="temp.fecha_desposte" mask="date" :rules="['date']">
                        <template v-slot:append>
                          <q-icon name="event" class="cursor-pointer">
                            <q-popup-proxy ref="qDateProxy3" transition-show="scale" transition-hide="scale">
                              <q-date v-model="temp.fecha_desposte" @input="() => $refs.qDateProxy3.hide()" />
                            </q-popup-proxy>
                          </q-icon>
                        </template>
                      </q-input>
                  </div>
                  <div class="col">
                    <q-input color="primary" type="number" v-model="temp.num_animales" label="Numero de animales">
                    </q-input>
                  </div>
                  <div class="col">
                    <q-checkbox class="q-mt-md" v-model="temp.producto_canal" left-label label="Venta Canal?" />
                  </div>
                </div>
                <div class="row q-col-gutter-sm">
                  <SelectTerceroSucursal v-model="sucursal" :editor="sucursal" labelTercero='Cliente'/>
                </div>
              </div>
              <q-btn v-if="!showForUpdateProgramacion" class="q-mt-sm"
                color="primary"
                v-close-popup
                label="Guardar"
                @click="addProgramacion"
              />

              <q-btn v-if="showForUpdateProgramacion" class="q-mt-sm"
                  color="primary"
                  v-close-popup
                  label="Guardar Edición"
                  @click="saveEditProgramacion(temp.id)"
                />
              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
      <!-- Fin Agregar programacion -->

      <!-- inicio popup impresion al guardar liqudacion -->
        <q-dialog v-model="showPrintLiquidacion" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
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
                    <h3>Se ha guardado la liquidacion de la programacion N° {{ datosPreliquidacion.programacion.id }}</h3>
                  </div>
                  <div class="row text-center">
                    <a target="_blank" :href="$store.state.jhsoft.url+'api/lotes/programaciones/liquidacion/imprimir/'+ datosPreliquidacion.programacion.id "><q-btn v-close-popup class="q-ml-xs" icon="assignment" color="primary"></q-btn> </a>
                  </div>
                </div>
              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
      <!-- fin popup impresion al guardar -->

      <!-- Liquidar programaciones -->
        <q-dialog v-model="OpenedLiquidarProgramaciones" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
          <q-layout view="Lhh lpR fff" container style="height: 600px; max-width: 800px" class="bg-white">
            <q-header class="bg-primary">
              <q-toolbar>
                <q-btn flat v-close-popup round dense icon="close" />
              </q-toolbar>
            </q-header>

            <q-page-container>
              <q-page padding>
              <div class="overflow-hidden">
                <div class="row q-m-xl col-12">
                  <div class="col">
                    <q-table
                        title="Listado de programaciones"
                        :data="programacionesPorLote"
                        :columns="columnsProgramacionesPorLote"
                        :filter="filterProgramacionesPorLote"
                        :visible-columns="visibleColumnsProgramacionesPorLote"
                        :separator="separator"
                        row-key="id"
                        color="secondary"
                        table-style="width:100%"
                    >
                        <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                            <a v-if="validarLiquidacionProgramacion(props.value) == true"  target="_blank"  :href="$store.state.jhsoft.url+'api/lotes/programaciones/liquidacion/imprimir/'+ props.value">
                              <q-btn v-close-popup class="q-ml-xs" icon="assignment" color="positive"></q-btn>
                            </a>
                            <q-btn v-close-popup v-if="validarLiquidacionProgramacion(props.value) == false" class="q-ml-xs" icon="local_atm" v-on:click="preliquidacion(props.value)" color="primary"></q-btn>
                            <q-btn v-close-popup v-if="validarLiquidacionProgramacion(props.value) == true" class="q-ml-xs" icon="delete" v-on:click="eliminarLiquidacion(props.value)" color="negative"></q-btn>
                        </q-td>
                    </q-table>
                  </div>
                </div>
              </div>
              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
      <!-- Fin liquidar programacion -->

      <!-- inicio popup ingreso de productos manualmente -->
        <q-dialog v-model="openedAddProducto" :content-css="{minWidth: '80vw', minHeight: '10vh'}">
          <q-layout view="Lhh lpR fff" container style="height: 400px; max-width: 800px" class="bg-white">
            <q-header class="bg-primary">
              <q-toolbar>
                <q-btn flat v-close-popup round dense icon="close" />
              </q-toolbar>
            </q-header>

            <q-page-container>
              <q-page padding>
                <h3>Agregar Producto</h3>
                <div class="overflow-hidden">
                  <div class="row q-col-gutter-sm">
                    <div class="col">
                      <q-select
                        v-model="temp.producto"
                        use-input
                        hide-selected
                        fill-input
                        option-value="id"
                        option-label="nombre"
                        label="Producto"
                        option-disable="inactive"
                        input-debounce="0"
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
                      </q-select>
                    </div>
                    <div class="col">
                      <q-input color="primary" type="number" v-model="temp.cantidad" label="Cantidad"></q-input>
                    </div>
                    <div class="col">
                      <q-input color="primary" type="number" v-model="temp.precio" label="Precio"></q-input>
                    </div>
                  </div>
                </div>
              <q-btn class="q-mt-sm"
                color="primary"
                label="Guardar"
                @click="addProducto"
              />

              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
      <!-- fin popup ingreso de productos manualmente -->

      <!-- Preliquidacion -->
        <q-dialog v-model="OpenedPreliquidacion" :content-css="{minWidth: '80vw', minHeight: '90vh'}">
          <q-layout view="Lhh lpR fff" container style="height: 95vh; max-width: 80vw" class="bg-white">
            <q-header class="bg-primary">
              <q-toolbar>
                <q-btn flat v-close-popup round dense icon="close" />
                <h5 style="margin:0px; padding:0px">PRELIQUIDACION</h5>
              </q-toolbar>
            </q-header>

            <q-page-container>
              <q-page padding>
              <div class="overflow-hidden">
                <div class="row q-m-xl col-12 q-col-gutter-md">
                  <div class="col-4">
                    <div class="col-12">
                      <div class="row">
                        <div class="col-12 text-center">
                          <strong>DESCRIPCIÓN</strong>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-6">
                          <strong>Cantidad:</strong>
                        </div>
                        <div class="col-6 text-right">
                          {{ parseInt(datosPreliquidacion.programacion.num_animales).toLocaleString('de-DE') }} /Und
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Peso en pie:</strong>
                        </div>
                        <div class="col-4 text-right">
                          <q-input class="input-pesos" suffix="Kg" dense v-model="datosPreliquidacion.pesosCompraTotal.ppe"></q-input>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Peso canal caliente:</strong>
                        </div>
                        <div class="col-4 text-right">
                          <q-input class="input-pesos" suffix="Kg" dense v-model="datosPreliquidacion.pesosCompraTotal.pcc"></q-input>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Peso canal frio:</strong>
                        </div>
                        <div class="col-4 text-right">
                          <q-input class="input-pesos" suffix="Kg" dense v-model="datosPreliquidacion.pesosCompraTotal.pcr"></q-input>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Kg x Animal (Carne):</strong>
                        </div>
                        <div class="col-4 text-right">
                          {{ (kgTotalesCarne / datosPreliquidacion.programacion.num_animales).toFixed(2) }} KG
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Costo Kilo Canal:</strong>
                        </div>
                        <div class="col-4 text-right">
                          $ {{ parseInt(costoCanal).toLocaleString('de-DE') }}
                        </div>
                      </div>
                    </div>
                    <div class="col-12 q-mt-xl" style="background:#aded87; padding:10px; border-radius:10px">
                      <div class="row">
                        <div class="col-12 text-center">
                          <strong>UTILIDADES</strong>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-6">
                          <strong>Costo prog:</strong>
                        </div>
                        <div class="col-6 text-right">
                          $ {{ parseInt(costoProgramacion).toLocaleString('de-DE') }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Valor venta:</strong>
                        </div>
                        <div class="col-4 text-right">
                          $ {{ parseInt(ventaTotal).toLocaleString('de-DE') }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Utilidad prog:</strong>
                        </div>
                        <div class="col-4 text-right">
                          $ {{ parseInt(ventaTotal - costoProgramacion).toLocaleString('de-DE') }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col-8">
                          <strong>Utilidad x animal:</strong>
                        </div>
                        <div class="col-4 text-right">
                          $ {{ parseInt((ventaTotal - costoProgramacion) / datosPreliquidacion.programacion.num_animales).toLocaleString('de-DE') }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="col-12" style="background:#ed6868; padding:10px; border-radius:10px">
                      <div class="row">
                        <div class="col text-center">
                          <strong>COSTOS</strong>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                        </div>
                        <div class="col text-right">
                          <strong>Valor Unidad</strong>
                        </div>
                        <div class="col text-right">
                          <strong>Cantidad</strong>
                        </div>
                        <div class="col text-right">
                          <strong>Total</strong>
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Precio</strong>
                        </div>
                        <div class="col text-right">
                          <money v-model="datosPreliquidacion.costoPrecio" v-bind="money" class="v-money preliquidacion-costos-table"></money>
                        </div>
                        <div class="col text-right">
                          {{ datosPreliquidacion.pesosCompraTotal.ppe }} KG
                        </div>
                        <div class="col text-right">
                          $ {{ parseInt(costoTotalPie).toLocaleString('de-DE')  }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Sacrificio</strong>
                        </div>
                        <div class="col text-right">
                          <money v-model="datosPreliquidacion.costoSacrificio" v-bind="money" class="v-money preliquidacion-costos-table"></money>
                        </div>
                        <div class="col text-right">
                          {{ datosPreliquidacion.programacion.num_animales }} / und
                        </div>
                        <div class="col text-right">
                          $ {{ parseInt(costoTotalSacrificio).toLocaleString('de-DE')  }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Desposte</strong>
                        </div>
                        <div class="col text-right">
                          <money v-model="datosPreliquidacion.costoDesposte" v-bind="money" class="v-money preliquidacion-costos-table"></money>
                        </div>
                        <div class="col text-right">
                          {{ datosPreliquidacion.programacion.num_animales }} / und
                        </div>
                        <div class="col text-right">
                          $ {{ parseInt(costoTotalDesposte).toLocaleString('de-DE')  }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Empaque</strong>
                        </div>
                        <div class="col text-right">
                          <money v-model="datosPreliquidacion.costoEmpaque" v-bind="money" class="v-money preliquidacion-costos-table"></money>
                        </div>
                        <div class="col text-right">
                          {{ datosPreliquidacion.vacioTotal }} KG
                        </div>
                        <div class="col text-right">
                          $ {{ parseInt(costoTotalEmpaque).toLocaleString('de-DE')  }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Trans Bene - Planta</strong>
                        </div>
                        <div class="col text-right">
                          <money v-model="datosPreliquidacion.costoTransporte" v-bind="money" class="v-money preliquidacion-costos-table"></money>
                        </div>
                        <div class="col text-right">
                          {{ datosPreliquidacion.programacion.num_animales }} / und
                        </div>
                        <div class="col text-right">
                          $ {{ parseInt(costoTotalTransporte).toLocaleString('de-DE')  }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Trans Planta - Punto</strong>
                        </div>
                        <div class="col text-right">
                          <money v-model="datosPreliquidacion.costoTransportePlantaPunto" v-bind="money" class="v-money preliquidacion-costos-table"></money>
                        </div>
                        <div class="col text-right">
                          {{ datosPreliquidacion.programacion.num_animales }} / und
                        </div>
                        <div class="col text-right">
                          $ {{ parseInt(costoTotalTransportePlantaPunto).toLocaleString('de-DE')  }}
                        </div>
                      </div>
                    </div>
                    <div class="col-12 q-mt-md">
                      <div class="row">
                        <div class="col">
                          <strong>Rendimiento PPE -> PCC</strong>
                        </div>
                        <div class="col">
                          {{ (parseFloat(datosPreliquidacion.pesosCompraTotal.pcc/datosPreliquidacion.pesosCompraTotal.ppe).toFixed(2))*100 }}%
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Rendimiento PCC -> PCR</strong>
                        </div>
                        <div class="col">
                          {{ (parseFloat(datosPreliquidacion.pesosCompraTotal.pcr/datosPreliquidacion.pesosCompraTotal.pcc).toFixed(2))*100 }}%
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Rentabilidad</strong>
                        </div>
                        <div class="col">
                          {{ (parseFloat((ventaTotal - costoProgramacion)/ventaTotal).toFixed(2))*100 }}%
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Proveedor</strong>
                        </div>
                        <div class="col">
                          {{ datosPreliquidacion.procedencia.proveedor }}
                        </div>
                      </div>
                      <div class="row">
                        <div class="col">
                          <strong>Sucursal</strong>
                        </div>
                        <div class="col">
                          {{ datosPreliquidacion.procedencia.sucursal }}
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-2">
                    <div class="col-12 text-center" style="background: #7e7e7e; color: white; font-size: 14px; border-radius:5px">
                      <div class="col-12">
                        PROGRAMACION:
                      </div>
                      <div class="col-12">
                        {{ datosPreliquidacion.programacion.id }}
                      </div>
                      <div class="col-12">
                        LOTE:
                      </div>
                      <div class="col-12">
                        {{ datosPreliquidacion.programacion.lote_id }}
                      </div>
                    </div>
                    <div class="col-12 text-center q-mt-md">
                      <div class="col-12">
                        <q-btn v-close-popup color="primary" v-on:click="guardarLiquidacion()" label="Liquidar" />
                      </div>
                    </div>
                  </div>
                </div>
                <div class="q-mt-lg col-12">
                  <div class="col">
                    <q-table
                      title="Listado de productos"
                      :data="datosPreliquidacion.productosSumatoria"
                      :columns="columnsPreliquidacionProductos"
                      row-key="name"
                      class="my-sticky-header-table"
                      binary-state-sort
                      hide-bottom
                      flat
                      table-style="max-height: 400px"
                      virtual-scroll
                      :pagination.sync="pagination"
                      :rows-per-page-options="[0]"
                    >
                      <template v-slot:body="props">
                        <q-tr :props="props">
                          <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                          <q-td key="peso" :props="props">{{ parseFloat(props.row.peso).toFixed(2) }}</q-td>
                          <q-td key="precio" :props="props">
                            {{ parseInt(props.row.precio).toLocaleString('de-DE') }}
                            <q-popup-edit v-model="props.row.precio" title="Precio" buttons>
                              <money v-model="props.row.precio" v-bind="money" class="v-money"></money>
                            </q-popup-edit>
                          </q-td>
                          <q-td key="totalventa" :props="props">{{ parseInt(props.row.precio * props.row.peso).toLocaleString('de-DE') }}</q-td>
                          <q-td key="porcentaje" :props="props">{{ ((parseFloat((props.row.precio * props.row.peso)/ventaTotal))*100).toFixed(2) }}%</q-td>
                          <q-td key="costounit" :props="props">{{  parseInt((costoProgramacion/ventaTotal) * props.row.precio).toLocaleString('de-DE') }}</q-td>
                          <q-td key="costototal" :props="props">{{  parseInt((costoProgramacion/ventaTotal) * (props.row.precio*props.row.peso)).toLocaleString('de-DE') }}</q-td>
                        </q-tr>
                      </template>
                    </q-table>
                  </div>
                </div>
                <div class="q-mt-lg col-12">
                  <div class="col">
                    <q-table
                      title="Listado de recuperaciones"
                      :data="productosRecuperaciones"
                      :columns="columnsPreliquidacionProductosRecuperaciones"
                      row-key="name"
                      class="my-sticky-header-table"
                      binary-state-sort
                      hide-bottom
                      flat
                      table-style="max-height: 400px"
                      virtual-scroll
                      :pagination.sync="pagination"
                      :rows-per-page-options="[0]"
                    >
                      <template slot="top-right">
                        <q-btn color="positive" v-on:click="openedAddProducto = true" label="Agregar Producto" />
                      </template>

                      <template v-slot:body="props">
                        <q-tr :props="props">
                          <q-td key="producto_id" :props="props"><q-checkbox v-if="!props.row.despacho" v-model="selected" :val="props.row.producto_id" /></q-td>
                          <q-td key="producto" :props="props">{{ props.row.producto }}</q-td>
                          <q-td key="peso" :props="props">{{ parseFloat(props.row.peso).toFixed(2) }}</q-td>
                          <q-td key="precio" :props="props">
                            {{ parseInt(props.row.precio).toLocaleString('de-DE') }}
                            <q-popup-edit v-model="props.row.precio" title="precio" buttons>
                              <money v-model="props.row.precio" v-bind="money" class="v-money"></money>
                            </q-popup-edit>
                          </q-td>
                          <q-td key="totalventa" :props="props">{{ parseInt(props.row.precio * props.row.peso).toLocaleString('de-DE') }}</q-td>
                          <q-td key="porcentaje" :props="props">{{ ((parseFloat((props.row.precio * props.row.peso)/ventaTotal))*100).toFixed(2) }}%</q-td>
                          <q-td key="costounit" :props="props">{{  parseInt((costoProgramacion/ventaTotal) * props.row.precio).toLocaleString('de-DE') }}</q-td>
                          <q-td key="costototal" :props="props">{{  parseInt((costoProgramacion/ventaTotal) * (props.row.precio*props.row.peso)).toLocaleString('de-DE') }}</q-td>
                        </q-tr>
                      </template>
                    </q-table>
                    <div class="col-4 q-mt-md">
                      <q-btn color="negative" v-on:click="eliminarSelected()" label="Eliminar Seleccionados" />
                    </div>
                  </div>
                </div>
              </div>
              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
      <!-- Fin Preliquidacion -->

      <!-- Resumen lote -->
        <q-dialog v-model="openedLote" :content-css="{minWidth: '80vw', minHeight: '40vh'}">
          <q-layout view="Lhh lpR fff" container style="height: 80vh; max-width: 90VW" class="bg-white">
            <q-header class="bg-primary">
              <q-toolbar>
                <q-btn flat v-close-popup round dense icon="close" />
              </q-toolbar>
            </q-header>

            <q-page-container>
              <q-page v-if="show.id" padding>
                <h3 style="margin: 5px 0px;">Lote: {{ show.id }}</h3>
                <h4 style="margin: 5px 0px;">Marca: {{ show.marca }}</h4>
                <h4 style="margin: 5px 0px;">Grupo: {{ show.ProdGrupo_id.nombre }}</h4>
              <div v-if="show.id" class="overflow-hidden">
                <div class="row q-col-gutter-sm">
                  <div class="col-4">
                    <strong>N° Animales:</strong> {{ show.num_animales }}
                  </div>
                  <div class="col-4">
                    <strong>Fecha Peso Pie:</strong> {{ show.fecha_peso_pie }}
                  </div>
                  <div class="col-4">
                    <strong>Marinado:</strong> {{ show.marinado }}
                  </div>
                </div>
                <div class="row q-col-gutter-sm">
                  <div class="col-4">
                    <strong>Fecha Sacrificio:</strong> {{ show.fecha_sacrificio }}
                  </div>
                  <div v-if="this.storeItems.producto_empacado" class="col-4">
                    <strong>Fecha Empaque Lote Tercero:</strong> {{ show.fecha_empaque_lote_tercero }}
                  </div>
                </div>
                <div class="row q-col-gutter-sm">
                  <div class="column col-12 q-mt-lg">
                    <div class="col-6 self-center q-mb-lg">
                      <h5 style="margin: 0px">PROGRAMACIONES</h5>
                    </div>
                  </div>
                  <div class="col-12 row" v-for="programacion in show.programaciones" :key="programacion.id">
                    <div class="col-4">
                      <strong>id: </strong> {{ programacion.id }}
                    </div>
                    <div class="col-4">
                      <strong>Fecha Desposte: </strong> {{ programacion.fecha_desposte }}
                    </div>
                    <div class="col-4">
                      <strong>N° Animales: </strong> {{ programacion.num_animales }}
                    </div>
                  </div>
                </div>
                <div class="row q-col-gutter-sm">
                  <div class="column col-12 q-mt-lg">
                    <div class="col-6 self-center q-mb-lg">
                      <h5 style="margin: 0px">ENTRADA / COMPRA: {{ show.consec_compra }}</h5>
                    </div>
                  </div>
                </div>
                <div class="row q-col-gutter-sm">
                  <div class="column col-12 q-mt-lg">
                    <div class="col-6 self-center q-mb-lg">
                      <h5 style="margin: 0px">PESO PLANTA</h5>
                    </div>
                  </div>
                  <div class="q-pa-md col-12">
                    <q-table
                      title="Peso Planta"
                      :data="show.lot_peso_planta"
                      :columns="columnsShowPesoPlanta"
                      row-key="name"
                      :visible-columns="visibleColumnsShowPesoPlanta"
                      table-style="width:100%"
                    >
                      <template v-slot:top="props">
                        <q-select
                          v-model="visibleColumnsShowPesoPlanta"
                          multiple
                          borderless
                          dense
                          options-dense
                          :display-value="$q.lang.table.columns"
                          emit-value
                          map-options
                          :options="columnsShowPesoPlanta"
                          option-value="name"
                          style="min-width: 150px"
                        />
                      </template>

                    </q-table>
                  </div>
                </div>
                <div class="row q-col-gutter-sm">
                  <div class="column col-12 q-mt-lg">
                    <div class="col-6 self-center q-mb-lg">
                      <h5 style="margin: 0px">PRODUCTO</h5>
                    </div>
                  </div>
                  <div class="q-pa-md col-12">
                    <q-table
                      title="Peso Planta"
                      :data="show.productos"
                      :columns="columnsShowProductos"
                      row-key="name"
                      :visible-columns="visibleColumnsShowProductos"
                      table-style="width:100%"
                      :filter="filterShow"
                    >
                      <template slot="top-right" slot-scope="props">
                          <q-input
                              hide-underline
                              color="secondary"
                              v-model="filterShow"
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

                    </q-table>
                  </div>
                </div>
              </div>
              </q-page>
            </q-page-container>
          </q-layout>
        </q-dialog>
      <!-- Fin Resumen Lote -->

      <h3><span v-if="showForUpdate">Editar</span> <span v-if="!showForUpdate">Crear</span> Lote</h3>
        <div class="overflow-hidden">
            <div class="row q-col-gutter-sm">
                <div class="col-3">
                    <q-select
                      label="Seleccione Entrada"
                      v-model="storeItems.com_compras_id"
                      :options="entradas"
                      option-value="id"
                      option-label="proveedor">
                      <template v-slot:option="scope">
                        <q-item
                          v-bind="scope.itemProps"
                          v-on="scope.itemEvents"
                        >
                          <q-item-section>
                            <q-item-label v-html="scope.opt.consecutivo + ' - ' + scope.opt.tercero + ' - ' + scope.opt.sucursal" />
                          </q-item-section>
                        </q-item>
                      </template>
                      <template v-if="storeItems.com_compras_id" v-slot:selected>
                        {{ storeItems.com_compras_id.id }} - {{ storeItems.com_compras_id.tercero }} - {{ storeItems.com_compras_id.sucursal }}
                      </template>
                    </q-select>
                </div>
                <div class="col-3">
                    <!-- <q-checkbox class="q-mt-md" v-model="storeItems.producto_empacado" @input="showForAnimalsMethod()" left-label label="Producto terminado" /> -->
                    <q-checkbox class="q-mt-md" v-model="storeItems.producto_empacado" left-label label="Producto terminado" />
                </div>
                <div v-if="showForAnimals" class="col-3">
                    <q-checkbox class="q-mt-md" v-model="storeItems.marinado" left-label label="Es Marinado?" />
                </div>
                <div v-if="showForAnimals" class="col-3">
                    <q-checkbox class="q-mt-md" v-model="storeItems.genero" left-label label="Es Hembra?" />
                </div>
            </div>
            <div class="row q-col-gutter-sm q-mt-md">
                <div v-if="showForAnimals" class="col-6">
                    <q-input label="Fecha de peso en pie" v-model="storeItems.fecha_peso_pie" mask="date" :rules="['date']">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy2" transition-show="scale" transition-hide="scale">
                            <q-date v-model="storeItems.fecha_peso_pie" @input="() => $refs.qDateProxy2.hide()" />
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                </div>
                <div class="col-6">
                    <q-input label="Fecha de Sacrificio" v-model="storeItems.fecha_sacrificio" mask="date" :rules="['date']">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                            <q-date v-model="storeItems.fecha_sacrificio" @input="() => $refs.qDateProxy1.hide()" />
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                </div>
                <div v-if="this.storeItems.producto_empacado" class="col-6">
                    <q-input label="Fecha Empaque Lote Tercero" v-model="storeItems.fecha_empaque_lote_tercero" mask="date" :rules="['date']">
                      <template v-slot:append>
                        <q-icon name="event" class="cursor-pointer">
                          <q-popup-proxy ref="qDateProxy1" transition-show="scale" transition-hide="scale">
                            <q-date v-model="storeItems.fecha_empaque_lote_tercero" @input="() => $refs.qDateProxy1.hide()" />
                          </q-popup-proxy>
                        </q-icon>
                      </template>
                    </q-input>
                </div>
            </div>
            <div class="row q-col-gutter-sm q-mt-sm">
              <div v-if="showForAnimals" class="col-6">
                <q-input color="primary" type="number" v-model="storeItems.num_animales" label="N° de animales"></q-input>
              </div>
              <div class="col-3">
                <q-select
                      label="Seleccione Tipo de animales"
                      v-model="storeItems.ProdGrupo_id"
                      :options="grupos"
                      option-value="id"
                      option-label="nombre"
                />
              </div>
              <div class="col-3">
                  <q-checkbox class="q-mt-md" v-model="storeItems.producto_aprobado" left-label label="Aprobado ?" />
              </div>
            </div>
            <div class="row q-col-gutter-sm q-mt-sm">
              <div  class="col-4">
                <q-input color="primary" type="text" v-model="storeItems.marca" label="Marca"></q-input>
              </div>
              <div v-if="!showForAnimals" class="col-4">
                <q-input color="primary" type="text" v-model="storeItems.lote_tercero" label="Lote Tercero"></q-input>
              </div>
              <div v-if="showForAnimals" class="col-4">
                <q-input color="primary" type="number" v-model="storeItems.ppe" label="Peso Pie"></q-input>
              </div>
              <div v-if="showForAnimals" class="col-4">
                <q-input color="primary" type="number" v-model="storeItems.pcc" label="Peso Canal Caliente"></q-input>
              </div>
            </div>
            <div class="row q-col-gutter-sm q-mt-sm">
              <div class="col">
                <SelectTerceroSucursal v-model="storeItems.transportador_id" :editor="storeItems.transportador_id" labelTercero='Transportador'/>
              </div>
            </div>
            <q-separator class="q-mt-md q-mb-md" color="orange"/>
            <div v-if="showForAnimals">
              <div class="row">
                <h4>Programaciones</h4>
              </div>
              <div class="row">
                <q-btn color="primary" v-on:click="showAddProgramacion()" label="Agregar programacion" />
              </div>
              <div class="row">
                <div v-if="showTableProgram" class="col q-ma-sm">
                  <q-table
                      :data="storeItems.programaciones"
                      :columns="ColumnsProgramaciones"
                      :separator="separator"
                      row-key="id"
                      color="secondary"
                      table-style="width:100%"
                  >
                      <q-td slot="body-cell-actions" slot-scope="props" :props="props">
                          <q-btn v-if="validarEstadoProgramacion(props.value)" class="q-ml-xs" icon="delete" v-on:click="eliminarFila(props.value)" color="negative"></q-btn>
                          <q-btn v-if="validarEstadoLiquidacion(props.value)" class="q-ml-xs" icon="edit" v-on:click="editProgramacion(props.value)" color="warning"></q-btn>
                      </q-td>
                  </q-table>
                </div>
                <div v-if="!showTableProgram">
                  <p>No hay programaciones creadas para este lote.</p>
                </div>
              </div>
            </div>

            <div class="row q-col-gutter-sm q-mt-sm">
                <div class="col-3">
                  <q-btn v-if="!showForUpdate" color="primary" v-on:click="globalValidate('guardar')" label="Guardar" />
                  <q-btn v-if="showForUpdate" color="primary" v-on:click="globalValidate('guardar-edicion', storeItems.id, 1)" label="Guardar Edición" />
                </div>
            </div>
        </div>
        <div class="row q-mt-xl col-12">
            <q-table
                title="Listado de Lotes"
                :data="tableData"
                :columns="columns"
                :filter="filter"
                :visible-columns="visibleColumns"
                :separator="separator"
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
                    <q-btn class="q-ml-xs" icon="local_atm" v-on:click="getProgramacionesLiquidar(props.value)" color="primary"></q-btn>
                    <q-btn class="q-ml-xs" icon="remove_red_eye" v-on:click="globalGetShowItem(props.value)" color="positive"></q-btn>
                    <q-btn class="q-ml-xs" icon="edit" v-on:click="globalValidate('editar', props.value)" color="warning"></q-btn>
                </q-td>
            </q-table>
        </div>
    </q-page>
  </div>
</template>

<script>
const axios = require('axios')
import { globalFunctions } from 'boot/mixins.js'
import SelectTerceroSucursal from 'components/terceros/SelectTerceroSucursal.vue'
import { Money } from 'v-money'

export default {
  name: 'PageLotes',
  components: {
    SelectTerceroSucursal,
    Money
  },
  data: function () {
    return {
      money: {
        decimal: ',',
        thousands: '.',
        prefix: '$ ',
        suffix: '',
        precision: 0,
        masked: false
      },
      showForUpdate: false,
      urlAPI: 'api/lotes/items',
      titulo: 'Lotes',
      showList: true,
      openedProgramacion: false,
      sucursal: null,
      openedLote: false,
      OpenedPreliquidacion: false,
      OpenedLiquidarProgramaciones: false,
      openedAddProducto: false,
      showForUpdateProgramacion: false,
      showPrintLiquidacion: false,
      liquidacionToPrint: null,
      selected: [],
      programaciones_counter: 0,
      datosPreliquidacion: {
        programacion: {},
        pesosCompraTotal: {},
        productosSumatoria: [],
        procedencia: {},
        costoPrecio: 0,
        costoSacrificio: 0,
        costoDesposte: 0,
        costoEmpaque: 0,
        costoTransporte: 0,
        costoTransportePlantaPunto: 0
      },
      productosRecuperaciones: [],
      storeItems: {
        fecha_sacrificio: null,
        fecha_peso_pie: null,
        marinado: false,
        genero: false,
        productos: [],
        marca: null,
        transportador_id: null,
        producto_empacado: false,
        ppe: null,
        pcc: null,
        num_animales: null,
        programaciones: [],
        ProdGrupo_id: null,
        fecha_empaque_lote_tercero: null
      },
      datos: {
        entrada_id: null
      },
      temp: {
        num_animales: null,
        fecha_desposte: null,
        producto_canal: false,
        cantidad: null,
        precio: null,
        producto: null
      },
      listas: {
        productos: []
      },
      entradas: [],
      grupos: [],
      productos: [],
      options: {
        productos: this.productos
      },
      programacionesPorLote: [],
      itemsPreliquidacionRecuperacionesCounter: 0,
      show: [],
      tableData: [],
      columns: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'grupo', required: true, label: 'Grupo', align: 'left', field: 'grupo', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'marca', required: true, label: 'Marca', align: 'left', field: 'marca', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'num_animales', required: true, label: 'N° Animales', align: 'left', field: 'num_animales', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      columnsShowPesoPlanta: [
        { name: 'peso', label: 'Peso', align: 'right', field: 'peso', sortable: true, format: val => this.globalFormatPeso(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'ph', label: 'PH', align: 'left', field: 'ph', sortable: true, format: val => this.globalFormatPeso(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'temperatura', label: 'Temperatura', align: 'left', field: 'temperatura', format: val => this.globalFormatPeso(val), sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cumple', label: 'Cumple', align: 'left', field: 'cumple', sortable: true, format: val => this.formatBool(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'sin_sustancias', label: 'Sin sustan', align: 'left', field: 'sin_sustancias', sortable: true, format: val => this.formatBool(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'color', label: 'Color', align: 'left', field: 'color', sortable: true, format: val => this.formatBool(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'olor', label: 'Olor', align: 'left', field: 'olor', sortable: true, format: val => this.formatBool(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'observaciones', label: 'Observaciones', align: 'left', field: 'observaciones', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      columnsShowProductos: [
        { name: 'id', required: true, label: 'id', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'peso', align: 'left', field: 'peso', sortable: true, format: val => this.globalFormatPeso(val), classes: 'my-class', style: 'width: 200px' },
        { name: 'programacion', required: true, label: 'Programacion', align: 'left', field: 'programacion', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'despacho', required: true, label: 'despacho', align: 'left', field: 'despacho', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'empaque', required: true, label: 'Tipo Empaque', align: 'left', field: 'empaque', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'fecha_empaque', required: true, label: 'Fecha Empaque', align: 'left', field: 'fecha_empaque', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      ColumnsProductos: [
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'cantidad', required: true, label: 'Cantidad en kilogramos', align: 'left', field: 'cantidad', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actionsProductos', required: true, label: 'Agregar al Lote', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      ColumnsProgramaciones: [
        { name: 'fecha_desposte', required: true, label: 'Fecha Desposte', align: 'left', field: 'fecha_desposte', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'num_animales', required: true, label: 'N° de Animales', align: 'left', field: 'num_animales', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'producto_canal', required: true, label: 'Venta en Canal', align: 'left', field: 'producto_canal', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      columnsProgramacionesPorLote: [
        { name: 'programacion_id', required: true, label: 'N° Programación', align: 'left', field: 'programacion_id', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'num_animales_programacion', required: true, label: 'N° Animales', align: 'left', field: 'num_animales_programacion', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'actions', required: true, label: 'Acciones', align: 'left', field: 'programacion_id', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      columnsPreliquidacionProductos: [
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Peso', align: 'right', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'precio', required: true, label: 'Precio', align: 'right', field: 'precio', classes: 'my-class', style: 'width: 200px' },
        { name: 'totalventa', required: true, label: 'Total Venta', align: 'right', field: 'totalventa', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'porcentaje', required: true, label: 'Porcentaje', align: 'right', field: 'porcentaje', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'costounit', required: true, label: 'Costo Unit', align: 'right', field: 'costounit', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'costototal', required: true, label: 'Costo Total', align: 'right', field: 'costototal', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      columnsPreliquidacionProductosRecuperaciones: [
        { name: 'producto_id', required: true, label: 'Producto id', align: 'left', field: 'producto_id', sortable: true, classes: 'my-class', style: 'width: 80px' },
        { name: 'producto', required: true, label: 'Producto', align: 'left', field: 'producto', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'peso', required: true, label: 'Peso', align: 'right', field: 'peso', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'precio', required: true, label: 'Precio', align: 'right', field: 'precio', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'totalventa', required: true, label: 'Total Venta', align: 'right', field: 'totalventa', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'porcentaje', required: true, label: 'Porcentaje', align: 'right', field: 'porcentaje', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'costounit', required: true, label: 'Costo Unit', align: 'right', field: 'costounit', sortable: true, classes: 'my-class', style: 'width: 200px' },
        { name: 'costototal', required: true, label: 'Costo Total', align: 'right', field: 'costototal', sortable: true, classes: 'my-class', style: 'width: 200px' }
      ],
      visibleColumns: ['id', 'nombre', 'actions'],
      visibleColumnsShowPesoPlanta: ['peso', 'ph', 'temperatura', 'cumple', 'sin_sustancias', 'color', 'olor', 'observaciones'],
      visibleColumnsShowProductos: ['id', 'producto', 'peso', 'programacion', 'fecha_empaque', 'empaque', 'despacho'],
      visibleColumnsProgramacionesPorLote: ['programacion_id', 'num_animales_programacion', 'actions'],
      visibleColumnsPreliquidacionProductos: ['programacion_id', 'num_animales_programacion', 'actions'],
      separator: 'horizontal',
      filter: '',
      filterShow: '',
      filterProgramacionesPorLote: '',
      filterPreliquidacionProductos: '',
      pagination: {
        rowsPerPage: 0
      }
    }
  },
  mixins: [globalFunctions],
  methods: {
    preSave () {
      if (this.storeItems.fecha_empaque_lote_tercero) {
        this.storeItems.fecha_empaque_lote_tercero = this.storeItems.fecha_empaque_lote_tercero
      } else {
        this.storeItems.fecha_empaque_lote_tercero = '1900/01/01'
      }
      this.storeItems.producto_aprobado = this.storeItems.producto_aprobado
      this.storeItems.ProdGrupo_id = this.storeItems.ProdGrupo_id.id
      if (this.storeItems.transportador_id.id) {
        this.storeItems.transportador_id = this.storeItems.transportador_id.id
      }
      this.storeItems.com_compras_id = this.storeItems.com_compras_id.id
    },
    postSave () {
      this.listas.productos = []
      this.datos.tercero_id = null
      this.datos.entrada_id = null
      this.storeItems = {
        fecha_sacrificio: null,
        fecha_peso_pie: null,
        marinado: false,
        genero: false,
        productos: [],
        marca: null,
        transportador_id: null,
        producto_empacado: false,
        num_animales: null,
        programaciones: [],
        ProdGrupo_id: null
      }
    },
    postGetShowItem () {
      this.openedLote = true
    },
    postEdit () {
      this.storeItems.com_compras_id = this.entradas.find(v => parseInt(v.id) === parseInt(this.storeItems.com_compras_id))
    },
    formatBool (value) {
      if (value === '1') {
        return 'OK'
      } else {
        return 'No'
      }
    },
    getProgramacionesLiquidar (id) {
      this.globalGetForSelect('api/lotes/programaciones/abiertasporlote/' + id + '/null', 'programacionesPorLote')
      this.OpenedLiquidarProgramaciones = true
    },
    preliquidacion (id) {
      this.globalGetForSelect('api/lotes/programaciones/preliquidacion/' + id, 'datosPreliquidacion')
      this.OpenedPreliquidacion = true
    },
    guardarLiquidacion () {
      var app = this
      app.datosPreliquidacion.productosRecuperaciones = app.productosRecuperaciones
      axios.post(app.$store.state.jhsoft.url + 'api/lotes/programaciones/liquidar/' + app.datosPreliquidacion.programacion.id, app.datosPreliquidacion).then(
        function (response) {
          if (response.data[0] === 'done') {
            app.showPrintLiquidacion = true
            app.liquidacionToPrint = response.data[1]
          }
        }
      )
    },
    filterProductoManual (val, update, abort) {
      update(() => {
        const needle = val.toLowerCase()
        this.options.productos = this.productos.filter(v => v.nombre.toLowerCase().indexOf(needle) > -1)
      })
    },
    addProducto () {
      var app = this
      if (app.temp.cantidad === null) {
        app.$q.notify({ color: 'negative', message: 'La cantidad debe ser diferente de 0.' })
      } else {
        var newProduct = {
          id: app.itemsPreliquidacionRecuperacionesCounter,
          producto: app.temp.producto.nombre,
          producto_id: app.temp.producto.id,
          peso: app.temp.cantidad,
          precio: app.temp.precio
        }
        app.productosRecuperaciones.push(newProduct)
        app.itemsPreliquidacionRecuperacionesCounter = app.itemsPreliquidacionRecuperacionesCounter + 1
        app.temp.producto = null
        app.temp.cantidad = null
        app.temp.precio = null
      }
    },
    validarLiquidacionProgramacion (id) {
      const item = this.programacionesPorLote.find(item => item.programacion_id === id)
      if (parseInt(item.estado) === 2) {
        return true
      } else {
        return false
      }
    },
    eliminarLiquidacion (id) {
      var app = this
      axios.get(app.$store.state.jhsoft.url + 'api/lotes/programaciones/liquidacion/eliminar/' + id).then(
        function (response) {
          if (response.data === 'done') {
            app.$q.notify({ color: 'positive', message: 'La liquidación ha sido eliminada' })
          }
        }
      )
    },
    eliminarSelected () {
      var app = this
      this.selected.forEach(function (elementSelected, j) {
        app.productosRecuperaciones.forEach(function (element, i) {
          if (elementSelected === element.producto_id) {
            app.productosRecuperaciones.splice(i, 1)
          }
        })
      })
      this.selected = []
    },
    validarEstado (id) {
      const item = this.tableData.find(item => item.id === id)
      return item.estado
    },
    async SelectedEntrada () {
      this.storeItems.productos = []
      try {
        let data = await axios.get(this.$store.state.jhsoft.url + 'api/entradas/entradafilter/' + this.datos.entrada_id.id)
        var tempData = data.data
        this.listas.productos = tempData
      } catch (error) {
        this.$q.notify({ type: 'negative', message: 'Hubo un error al filtrar las entradas!' })
      } finally {
      }
    },
    showForAnimalsMethod () {
      if (this.storeItems.producto_empacado) {
        this.storeItems.fecha_peso_pie = '1900/01/01'
        this.storeItems.precio_sacrificio_unit = 0
        this.storeItems.genero = false
        this.storeItems.marinado = false
        this.storeItems.num_animales = 0
        this.storeItems.pcc = 0
        this.storeItems.ppe = 0
        this.storeItems.fecha_sacrificio = null
      } else {
        this.storeItems.fecha_peso_pie = null
      }
      this.storeItems.programaciones.push({
        id: 'nuevo' + 1,
        fecha_desposte: '1900/01/01',
        num_animales: 0,
        producto_canal: false,
        terceroSucursal_id: 1
      })
    },
    showAddProgramacion () {
      this.openedProgramacion = true
    },
    eliminarFila (id) {
      var index = null
      this.$q.dialog({
        message: '¿ Quieres eliminar esta fila ?',
        cancel: true,
        persistent: true
      }).onOk(() => {
        this.storeItems.programaciones.forEach(function (element, i) {
          if (id === element.id) {
            index = i
          }
        })
        this.storeItems.programaciones.splice(index, 1)
      }).onCancel(() => {
        this.$q.notify('Cancelado...')
      }).onDismiss(() => {
      })
    },
    validarEstadoProgramacion (id) {
      const item = this.storeItems.programaciones.find(item => item.id === id)
      if (item.id.toString().indexOf('nuevo') !== -1) {
        return true
      } else {
        return false
      }
    },
    validarEstadoLiquidacion (id) {
      const item = this.storeItems.programaciones.find(item => item.id === id)
      if (parseInt(item.estado) !== 2) {
        return true
      } else {
        return false
      }
    },
    addProgramacion () {
      if (this.temp.fecha_desposte) {
        if (!this.storeItems.fecha_sacrificio) {
          this.$q.notify({ type: 'negative', message: 'Ingresar primero fecha de sacrificio' })
          this.openedProgramacion = false
        } else if (this.temp.fecha_desposte < this.storeItems.fecha_sacrificio) {
          this.$q.notify({ type: 'negative', message: 'La fecha de desposte debe ser mayor a la fecha de sacrificio' })
        } else {
          this.storeItems.programaciones.push({
            id: 'nuevo' + this.programaciones_counter,
            fecha_desposte: this.temp.fecha_desposte,
            num_animales: this.temp.num_animales,
            producto_canal: this.temp.producto_canal,
            terceroSucursal_id: this.sucursal
          })
          this.programaciones_counter++
        }
      }
      this.temp.fecha_desposte = null
      this.temp.num_animales = null
      this.sucursal = null
      this.temp.producto_canal = false
    },
    editProgramacion (id) {
      this.temp.fecha_desposte = null
      this.temp.num_animales = null
      this.temp.producto_canal = false
      this.sucursal = null
      this.openedProgramacion = true
      this.showForUpdateProgramacion = true
      const item = this.storeItems.programaciones.find(item => item.id === id)
      this.temp.fecha_desposte = item.fecha_desposte
      this.temp.id = item.id
      this.temp.num_animales = item.num_animales
      this.temp.producto_canal = item.producto_canal
      this.sucursal = item.terceroSucursal_id
    },
    saveEditProgramacion (id) {
      var index = null
      this.storeItems.programaciones.forEach(function (element, i) {
        if (id === element.id) {
          index = i
        }
      })
      this.storeItems.programaciones.splice(index, 1)
      this.storeItems.programaciones.push({
        id: this.temp.id,
        fecha_desposte: this.temp.fecha_desposte,
        num_animales: this.temp.num_animales,
        producto_canal: this.temp.producto_canal,
        terceroSucursal_id: this.sucursal
      })
      this.temp.fecha_desposte = null
      this.temp.num_animales = null
      this.temp.producto_canal = false
      this.sucursal = null
      this.showForUpdateProgramacion = false
    }
  },
  created: function () {
    this.globalGetItems()
    this.globalGetForSelect('api/compras/items', 'entradas')
    this.globalGetForSelect('api/productos/grupos', 'grupos')
    this.globalGetForSelect('api/productos/todosconimpuestos', 'productos')
  },
  computed: {
    showTableProgram: function () {
      if (this.storeItems.programaciones.length > 0) {
        return true
      } else {
        return false
      }
    },
    showForAnimals: function () {
      var response = 1
      // if (this.storeItems.producto_empacado) {
      //   response = 0
      // }
      return response
    },
    costoCanal: function () {
      var response = (this.costoTotalPie + this.costoTotalSacrificio + this.costoTotalTransporte) / this.datosPreliquidacion.pesosCompraTotal.pcr
      return response
    },
    costoTotalPie: function () {
      var response = this.datosPreliquidacion.costoPrecio * this.datosPreliquidacion.pesosCompraTotal.ppe
      return response
    },
    costoTotalSacrificio: function () {
      var response = this.datosPreliquidacion.costoSacrificio * this.datosPreliquidacion.programacion.num_animales
      return response
    },
    costoTotalDesposte: function () {
      var response = this.datosPreliquidacion.costoDesposte * this.datosPreliquidacion.programacion.num_animales
      return response
    },
    costoTotalEmpaque: function () {
      var response = this.datosPreliquidacion.costoEmpaque * this.datosPreliquidacion.vacioTotal
      return response
    },
    costoTotalTransporte: function () {
      var response = this.datosPreliquidacion.costoTransporte * this.datosPreliquidacion.programacion.num_animales
      return response
    },
    costoTotalTransportePlantaPunto: function () {
      var response = this.datosPreliquidacion.costoTransportePlantaPunto * this.datosPreliquidacion.programacion.num_animales
      return response
    },
    costoProgramacion: function () {
      var response = this.costoTotalPie + this.costoTotalSacrificio + this.costoTotalDesposte + this.costoTotalEmpaque + this.costoTotalTransporte + this.costoTotalTransportePlantaPunto
      return response
    },
    ventaTotal: function () {
      var response = 0
      this.datosPreliquidacion.productosSumatoria.forEach(function (element, i) {
        response = (element.precio * element.peso) + response
      })
      this.productosRecuperaciones.forEach(function (element, i) {
        response = (element.precio * element.peso) + response
      })
      return response.toFixed(2)
    },
    kgTotalesCarne: function () {
      var response = 0
      this.datosPreliquidacion.productosSumatoria.forEach(function (element, i) {
        response = parseFloat(element.peso) + response
      })
      return response
    }
  }
}
</script>

<style>
    .q-table-container{
        width: 100%;
    }
    .text-right {
      text-align: right;
    }
    .preliquidacion-costos-table > div > div {
      height: 20px!important;
    }
    .v-money:focus{
      outline: none;
      border-bottom: 1px solid #027be3;
    }
    .v-money{
      padding: 0px;
      border: none;
      width: 100%;
      background-color: azure;
      border-bottom: 1px solid rgba(0,0,0,0.24);
    }
    .input-pesos{
      padding: 0px;
      border: none;
      width: 100%;
      background-color: azure;
      border-bottom: 1px solid rgba(0,0,0,0.24);
    }
</style>
