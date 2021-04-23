<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/refresh-cache', function() {
    Artisan::call('cache:clear');
    Artisan::call('view:clear');
    Artisan::call('config:cache');
    return "Cache is refreshed";
});

Route::get('/backupsql', function() {
    exec("SqlCmd -E -S .\local -Q 'BACKUP DATABASE [sgc] TO DISK=’D:BackupsMyDB2.bak’'");
    return "backup done";
});

// Route::get('/migrate-refresh-seed', function() {
//     Artisan::call('migrate:refresh --seed');
// Artisan::call('db:seed --class=SoenacSeeder');
//     return "Migration done";
// });

Route::get('/migrate', function() {
    Artisan::call('migrate');
    return "Migration done";
});

Route::get('/seedsoenac', function() {
    Artisan::call('db:seed --class=SoenacSeeder');
    return "seed done";
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('auth')->group(function () {
    Route::post('login', 'AuthController@login');
    Route::get('refresh', 'AuthController@refresh');
    Route::group(['middleware' => 'auth:api'], function(){
    	Route::post('register', 'AuthController@register');
        Route::get('user', 'AuthController@user');
        Route::post('logout', 'AuthController@logout');
    });
});

Route::group(['prefix' => 'users', 'middleware' => 'auth:api'], function(){
    // Users
    Route::get('', 'UserController@index');
    Route::get('{id}', 'UserController@show');
    Route::post('{id}', 'UserController@update');
    Route::get('estado/{id}/{cambio}', 'UserController@estado')->middleware('isAdmin');
    Route::get('reinitpassword/{id}', 'UserController@reiniciarPassword')->middleware('isAdmin');
    Route::post('/editar/cambiarpass', 'UserController@cambiarPass')->middleware('isAdminOrSelf');
});

Route::group(['prefix' => 'lotes'/*, 'middleware' => 'auth'*/], function(){
// Route::group(['prefix' => 'lotes' , 'middleware' => 'auth'], function(){
	Route::apiResource('marcas', 'LotMarcaController');
	Route::apiResource('items', 'LotesController');
	// peso planta
	Route::apiResource('pesoplanta', 'LotPesoPlantaController');
	Route::get('pesoplanta/lotefilter/{idlote}', 'LotPesoPlantaController@GetData');
	Route::get('pesoplanta/deletebylote/{idlote}', 'LotPesoPlantaController@DeleteByLote');
	// peso marinacion
	Route::apiResource('pesomarinacion', 'LotMarinacionController');
	Route::get('pesomarinacion/programacionfilter/{idlote}', 'LotMarinacionController@GetData');
	// programaciones
	Route::get('programaciones/abiertas/{producto_empacado}', 'LotProgramacionController@programacionLotesAbiertos');
	Route::get('programaciones/abiertasporgrupo/{id}/{producto_empacado}', 'LotProgramacionController@programacionLotesAbiertosPorGrupo');
	Route::get('programaciones/abiertasporlote/{id}/{producto_empacado}', 'LotProgramacionController@programacionPorId');
	Route::get('/programaciones/pesosporprogramacion/{id}', 'LotProgramacionController@pesosPorProgramacion');
	Route::post('/programaciones/pesoprogramacion', 'LotProgramacionController@storePesoProgramacion');
	Route::delete('/programaciones/pesoprogramacion/{id}', 'LotProgramacionController@deletePesoProgramacion');
	// varios
	Route::apiResource('inventario', 'InventarioController');
	Route::get('programaciones/preliquidacion/{id}', 'LotesController@preLiquidacionProgramacion');
	Route::post('programaciones/liquidar/{id}', 'LotLiquidacionesController@store');
	Route::get('programaciones/liquidacion/imprimir/{id}', 'LotLiquidacionesController@printLiquidacion');
	Route::get('programaciones/liquidacion/eliminar/{id}', 'LotLiquidacionesController@eliminarLiquidacion');
});

Route::group(['prefix' => 'despachos'/*, 'middleware' => 'auth'*/], function(){
	Route::apiResource('items', 'SalMercanciaController');
	Route::get('/getdatacertificado/{id}', 'SalMercanciaController@generatePDF');
	Route::get('/datefilter/{fechaini}/{fechafinal}/{sucursal}', 'SalMercanciaController@DateFilter');
	Route::get('/getdataresumen/{id}', 'SalMercanciaController@GetDataResumen');
	Route::get('/sinfactura', 'SalMercanciaController@despachoSinFactura');
	Route::get('/sinfacturaparatraslados', 'SalMercanciaController@despachoSinFacturaParaTraslados');
	Route::get('/pesodespacho/{despacho_id}', 'SalMercanciaController@pesoDespacho');
	Route::post('/guardarpesodespacho', 'SalMercanciaController@guardarPesoDespacho');
	Route::post('/crearporlote', 'SalMercanciaController@CrearPorLote');
});

Route::group(['prefix' => 'productos'/*, 'middleware' => 'auth'*/], function(){

	Route::get('/export/data', 'ProductosController@exportData');
	Route::post('/import/data', 'ProductosController@importaData');

	Route::apiResource('almacenamiento', 'ProdAlmacenamientoController');
	Route::get('/almacenamiento/estado/{id}/{cambio}', 'ProdAlmacenamientoController@estado');
	Route::get('/almacenamiento/estado/activos', 'ProdAlmacenamientoController@activos');

	Route::apiResource('grupos', 'ProdGrupoController');
	Route::get('/grupos/filter/animalfilter', 'ProdGrupoController@animalFilter');
	Route::get('/grupos/estado/{id}/{cambio}', 'ProdGrupoController@estado');
	Route::get('/grupos/estado/activos', 'ProdGrupoController@activos');

	Route::apiResource('subgrupos', 'ProdSubgrupoController');
	Route::get('/subgrupos/estado/{id}/{cambio}', 'ProdSubgrupoController@estado');
	Route::get('/subgrupos/grupofilter/{id}', 'ProdSubgrupoController@groupFilter');
	Route::get('/subgrupos/estado/activos', 'ProdSubgrupoController@activos');

	Route::apiResource('items', 'ProductosController');
	Route::get('/todosconimpuestos', 'ProductosController@todosConImpuestos');
	Route::get('/items/subgrupofilter/{id}', 'ProductosController@subGroupFilter');
	Route::get('/items/estado/{id}/{cambio}', 'ProductosController@estado');

	Route::apiResource('listadeprecios', 'ProdListaPrecioController');
	Route::get('listadeprecios/estado/activos', 'ProdListaPrecioController@activos');
	Route::get('/preciosporlista/{id}', 'ProdListaPrecioController@preciosPorLista');
	Route::get('/preciosporsucursal/{id}', 'ProdListaPrecioController@preciosPorSucursal');
	Route::get('/listadeprecios/estado/{id}/{cambio}', 'ProdListaPrecioController@estado');

	Route::get('/informes/productosxlistaprecio/{listaprecio_id}', 'ProductosController@ProductosxListaprecio');
	Route::get('/informes/listadoproductos', 'ProductosController@ListadoProductos');


	Route::get('/listadodeproductosporlista/{listaprecio_id}', 'ProductosController@listadoDeProductosPorLista');
	Route::get('/modificarprecio/{listaprecio_id}/{codigo}/{precio_nuevo}', 'ProductosController@modificarPrecio');

	Route::get('/configuracion/cargarabascula', 'ProductosController@generarArchivoProductosBasculaDival');
	Route::get('/configuracion/subirpreciosepelsa', 'ProductosController@generarArchivoProductosBasculaEpelsa');
	Route::get('/configuracion/subirdatosbasculamarques', 'ProductosController@subirDatosBasculaMarques');
});

Route::group(['prefix' => 'generales'/*, 'middleware' => 'auth'*/], function(){
	Route::apiResource('tipoimpuestos', 'GenTipoImpuestoController');
	Route::get('/tipoimpuestos/estado/activos', 'GenTipoImpuestoController@activos');
	Route::get('/tipoimpuestos/estado/{id}/{cambio}', 'GenTipoImpuestoController@estado');

	Route::apiResource('iva', 'GenIvaController');
	Route::apiResource('genpuc', 'GenPucController');

	Route::apiResource('unidades', 'GenUnidadesController');
	Route::get('/unidades/estado/{id}/{cambio}', 'GenUnidadesController@estado');
	Route::get('/unidades/estado/activos', 'GenUnidadesController@activos');

	Route::get('/soenac/responsabilidades', 'SoenacController@responsabilidades');
	Route::get('/soenac/tiposdocumento', 'SoenacController@tiposDocumento');
	Route::get('/soenac/tiposorganizacion', 'SoenacController@tiposOrganizacion');
	Route::get('/soenac/regimenes', 'SoenacController@regimenes');


	Route::apiResource('impuestos', 'GenImpuestoController');
	Route::get('/impuestos/estado/{id}/{cambio}', 'GenImpuestoController@estado');
	Route::get('/impuestos/estado/activos', 'GenImpuestoController@activos');

	Route::apiResource('impresoras', 'GenImpresorasController');
	Route::get('/impresoras/estado/{id}/{cambio}', 'GenImpresorasController@estado');
	Route::get('/impresoras/estado/activos', 'GenImpresorasController@activos');

	Route::apiResource('basculas', 'GenBasculasController');
	Route::get('/basculas/estado/{id}/{cambio}', 'GenBasculasController@estado');
	Route::get('/basculas/estado/activos', 'GenBasculasController@activos');

	Route::apiResource('generalidades', 'GeneralidadesController');

	Route::get('municipios/filtro/pordepartamento/{id}', 'GenMunicipiosController@porDepartamento');
    Route::apiResource('departamentos', 'GenDepartamentosController');
    Route::apiResource('municipios', 'GenMunicipiosController');

    Route::apiResource('cuadrecaja', 'GenCuadreCajaController');
	Route::get('/cuadrecaja/abiertoporusuario/{id}', 'GenCuadreCajaController@abiertoPorUsuario');
	Route::get('/cuadrecaja/abrircaja/{monto_apertura}', 'GenCuadreCajaController@abrirCaja');
	Route::get('/cuadrecaja/cerrarcaja/{monto_cierre}', 'GenCuadreCajaController@cerrarCaja');

	Route::get('/cuadrecaja/imprimir/{id}', 'GenCuadreCajaController@printCuadre');
	Route::get('/cuadrecaja/imprimirpdf/{id}', 'GenCuadreCajaController@printCuadrePDF');

	Route::apiResource('vendedores', 'GenVendedoresController');

	Route::apiResource('empresa', 'GenEmpresaController');
	Route::get('/empresa/filter/terceropos', 'GenEmpresaController@terceroPOS');
	Route::get('/empresa/configuracion/obtenermac', 'GenEmpresaController@obtenerMAC');
});

Route::group(['prefix' => 'basculas'/*, 'middleware' => 'auth'*/], function(){

	Route::get('readtiquetedibal/{tiquete}', 'GenBasculasController@readTiqueteDibal');
	Route::get('readtiqueteepelsa/{tiquete}', 'GenBasculasController@readTiqueteEpelsa');

	Route::get('/tiquetenofacturadosmarques/{fecha}', 'GenBasculasController@tiquetesNoFacturadosMarques');
	Route::get('/tiquetenofacturados/{fecha}', 'GenBasculasController@tiquetesNoFacturados');

	Route::get('/tiquetenofacturadospdf/{fecha}', 'GenBasculasController@tiquetesNoFacturadosPDF');

	Route::get('/verificartiquetemarques/{tiquete}/{puesto}/{fecha}', 'GenBasculasController@verificarTiqueteMarques');

});

Route::group(['prefix' => 'terceros', 'middleware' => 'auth'], function(){
	Route::apiResource('items', 'TerceroController');
	Route::get('/items/estado/{id}/{cambio}', 'TerceroController@estado');
	Route::get('/items/estado/activos', 'TerceroController@activos');

	Route::apiResource('sucursales', 'TerceroSucursalController');
	Route::get('/sucursales/tercerofilter/{id}', 'TerceroSucursalController@terceroFilter');
});

// Route::group(['prefix' => 'inventario', 'middleware' => 'auth'], function(){
Route::group(['prefix' => 'inventario'], function(){
	Route::apiResource('items', 'InventariosController');
	Route::get('produccion', 'InventariosController@inventarioProduccion');
	Route::delete('produccion/{id}', 'InventariosController@destroy');
	Route::get('/productonprogram/{idproducto}/{idprogramacion}', 'InventariosController@GetDataExistentes');
	Route::get('/idfilter/{id}', 'InventariosController@GetInfoSalMercancia');
    Route::post('/etiqueta-interna', 'InventariosController@imprimirEtiquetaInterna');
	Route::post('/reimprimir', 'InventariosController@reimprimir');
	Route::post('/dividircanasta', 'InventariosController@dividir');
	Route::get('/piezasimpresas/{idprogramacion}/{idproducto}', 'InventariosController@GetPiezasImpresas');
	Route::get('/indexfilter/{lote}/{fecha_ini}/{fecha_fin}/{idproducto}/{estado}', 'InventariosController@indexFilter');
});

Route::group(['prefix' => 'facturacion'/*, 'middleware' => 'auth'*/], function(){

	// // Route::get('importar/cargarcartera', 'FacMovimientosController@importarDatos');

    Route::get('tiquetesnofacturados/limpiartiquetes', 'FacMovimientosController@limpiarTiquetesBascula');

	Route::get('editarfactura/{tipodoc}/{consecmov}', 'FacMovimientosController@editarFactura');
	Route::get('consultarultimo/{tipodoc}', 'FacMovimientosController@ultimoPorTipoDoc');

    Route::get('tipos/estado/{estado}', 'FacTipoDocController@facTipoDocPorEstado');

	Route::apiResource('tipos', 'FacTipoDocController');
	Route::apiResource('tiposrecibocaja', 'FacTipoRecCajaController');
	Route::apiResource('movimientos', 'FacMovimientosController');
	Route::apiResource('reciboscaja', 'FacRecibosCajaController');
	Route::apiResource('formaspago', 'FacFormasPagoController');
	Route::get('tipos/filtro/facturas', 'FacTipoDocController@facturas');

	Route::get('/recibocaja/anular/{id}', 'FacRecibosCajaController@anular');

	Route::get('movimientos/filtro/portipo/{id}', 'FacMovimientosController@porTipos');
	Route::get('movimientos/filtro/porsucursalytipo/{idsucursal}/{idtipo}', 'FacMovimientosController@porSucursal');
	Route::get('movimientos/filtro/facturasporsucursal/{idsucursal}', 'FacMovimientosController@facturasPendientesPorSucursal');
	Route::get('movimientos/filtro/facturasporsucursalytipodoc/{idsucursal}/{idtipodoc}', 'FacMovimientosController@facturasPendientesPorSucursalYTipo');
	Route::get('movimientos/filtro/todotiposucursalgrupo', 'FacMovimientosController@todosConTipoSucursalGrupo');
	Route::get('movimientos/filtro/facturaspendientesparanotas/{idsucursal}/{idtipodoc}', 'FacMovimientosController@FacturasPendientesParaNotas');
	Route::get('movimientositems/filtro/pormovimiento/{id}', 'FacPivotMovProductosController@porMovimiento');
    Route::get('movimientos/filtro/allnotas/', 'FacMovimientosController@allNotas');
    Route::get('movimientos/filtro/notaporid/{id}', 'FacMovimientosController@notaPorId');
    Route::get('movimientos/filtro/reciboporid/{id}', 'FacMovimientosController@reciboPorId');



	Route::get('/imprimir/{id}', 'FacMovimientosController@generatePDF');
	Route::get('/imprimirpos/{id}/{copia}', 'FacMovimientosController@printPOS');
	Route::get('/recibocaja/imprimir/{id}', 'FacRecibosCajaController@generatePDF');
	Route::get('/recibocaja/imprimirpos/{id}', 'FacRecibosCajaController@printPOS');

	Route::get('/movrelacionado/{id}', 'FacMovRelacionadosController@getMovPrimario');
	Route::get('/movrelacionado/{id}/items-abiertos', 'FacMovRelacionadosController@getItemsMovPrimarioAbiertos');
	Route::get('/movrelacionado/{id}/items', 'FacMovRelacionadosController@getItemsMovPrimarios');

	Route::get('readdespacho/{despacho_id}', 'SalMercanciaController@despachoParaFactura');
	Route::get('/facturacionelectronica/eliminardatoshabilitacion', 'FacMovimientosController@eliminarDatosHabilitacion');
	Route::get('/datafacturacionelectronica/{id}', 'FacMovimientosController@dataFacturaElectronica');
	Route::post('/agregarcufe/{id}', 'FacMovimientosController@agregarCufe');
	Route::post('/enviarfacturasoenac','FacMovimientosController@sendFactToSoenac');

});

Route::group(['prefix' => 'compras'], function(){
	Route::apiResource('tipos', 'ComTipoComprasController');
	Route::apiResource('tiposcomproegreso', 'ComTipoComproEgresosController');
	Route::apiResource('items', 'ComComprasController');
	Route::apiResource('comproegreso', 'ComComproEgresoController');
	Route::get('tipos/filtro/compras', 'ComTipoComprasController@compras');

	Route::get('filtro/comprasporsucursalytipodoc/{idsucursal}/{idtipodoc}', 'ComComprasController@comprasPendientesPorSucursalYTipo');
	Route::get('comprasitems/filtro/porcompra/{id}', 'ComPivotCompraProductosController@porCompra');

	Route::get('/imprimir/{id}', 'ComComprasController@generatePDF');
	Route::get('/comprobanteegreso/imprimir/{id}', 'ComComproEgresoController@generatePDF');
	Route::get('/comprobanteegreso/imprimirpos/{id}', 'ComComproEgresoController@printPOS');

	Route::get('/movrelacionado/{id}/items', 'ComTipoCompraRelacionadoController@getItemsMovPrimarios');

	Route::get('/informes/cuentasporpagar', 'ComComprasController@cuentasPorPagar');
	Route::get('/informes/cxpxcliente/{tercerdo_id}', 'ComComprasController@cuentasPorPagarxTercero');
	Route::get('/informes/comprasnetasporfecha/{fechaini}/{fechafin}', 'ComComprasController@comprasNetasPorFecha');
	Route::get('/informes/pagosporfecha/{fechaini}/{fechafin}', 'ComComprasController@pagosPorFecha');
});

Route::group(['prefix' => 'ordenes'], function(){
	Route::apiResource('tipos', 'OrdTipoOrdenesController');
	Route::apiResource('items', 'OrdOrdenesController');
	Route::get('/imprimir/{id}', 'OrdOrdenesController@generatePDF');
	Route::get('/readordenfactura/{cosec_orde}/{tipodoc_id}', 'OrdOrdenesController@ordenParaFactura');
	Route::get('/readordencompra/{cosec_orde}/{tipodoc_id}', 'OrdOrdenesController@ordenParaCompra');
});

Route::group(['prefix' => 'egresos', 'middleware' => 'auth'], function(){
	Route::apiResource('items', 'EgreEgresosController');
	Route::apiResource('tipos', 'EgreTipoEgresoController');
	Route::get('/imprimir/{id}', 'EgreEgresosController@generatePDF');
	Route::get('/reporteporcuadre/{id}', 'EgreEgresosController@generateReporteEgresosPorCuadre');
});

Route::group(['prefix' => 'informes', 'middleware' => 'auth'], function(){
	Route::get('/productosporlote/{id}', 'InventariosController@GetProductosPorLotePDF');
});



Route::group(['prefix' => 'reportesgenerados'/*, 'middleware' => 'auth'*/], function(){

    Route::get('/reportes/testing', 'ReportesGeneradosController@testing');

    Route::get('/reportes/compilejrxml', 'ReportesGeneradosController@compileJrXml');
    Route::get('/reportes/relaciontiquetefactura', 'ReportesGeneradosController@reporteTiqueteFactura');
    Route::get('/reportes/saldocartera', 'ReportesGeneradosController@saldosCartera');
    Route::get('/reportes/saldocarteratr', 'ReportesGeneradosController@saldosCarteraTR');
	Route::get('/reportes/movimientosporfecha', 'ReportesGeneradosController@movimientosPorFecha');
    Route::get('/reportes/movimientosporfechagrupo', 'ReportesGeneradosController@movimientosPorFechaGrupo');
	Route::get('/reportes/ivas/{fecha_ini}/{fecha_fin}', 'ReportesGeneradosController@vistaInterfazContadoras');
    Route::get('/reportes/movimientosPorProducto', 'ReportesGeneradosController@movimientosPorProducto');
    Route::get('/reportes/pesoplantalote/{lote_id}', 'ReportesGeneradosController@pesoPlantaLote');

    Route::get('/cxct80', 'ReportesGeneradosController@saldosEnCarteraT80');
    Route::get('/movsporfechat80/{fechaini}/{fechafin}', 'ReportesGeneradosController@movimientosPorFechaT80');

    Route::get('/ventasnetasporfecha/{fechaini}/{fechafin}', 'ReportesGeneradosController@ventasNetasPorFecha');
    Route::get('/recaudoporfecha/{fechaini}/{fechafin}', 'ReportesGeneradosController@recaudoPorFecha');

    Route::get('/formasdepagopormovimientoporfecha/{fechaini}/{fechafin}', 'ReportesGeneradosController@movimientosConFormaPagoPorFecha');
});


