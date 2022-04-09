<!DOCTYPE html>
<html>
<head>
	<title>Liquidación</title>
<style>
	.table {
	  font-family: arial, sans-serif;
	  border-collapse: collapse;
	  border: 1px solid #000;
	}

	.table-info {
	  font-family: arial, sans-serif;
	}

	p,h3,h1{
		font-family: arial, sans-serif;
	}

	table{
		width: 100%;
		margin-top: 10px 0px;
	}

	table p{
		margin: 0px;
	}

	table h3{
		margin: 0px;
	}

	.table td, .table th {
	  border: 1px solid #dddddd;
	  text-align: left;
	  padding: 8px;
	}

	.align-r{
		text-align: right!important;
	}

	.table-even tr:nth-child(even) {
	  background-color: #dddddd;
	}

	.w100{
		width: 100%;
		display: table;
	}

	.w33{
		width: 33%;

	}

	.text-center{
		text-align: center;
	}

	.table-font td,.table-font th{
		font-size: 10px;
		margin: 3px 0px;
		padding: 3px;
	}

	.text-footer{
		font-size: 12px;
	}

	.table-info p{
		font-size: 12px;
	}

	.row{
		display: table;
		width: 100%;
	}

	/* Create three equal columns that floats next to each other */
	.column {
	  float: left;
	  width: 48%;
	  padding: 5px;
	}

	.col-12{
	  float: left;
	  width: 100%;
	}

	/* Clear floats after the columns */
	.row:after {
	  content: "";
	  display: table;
	  clear: both;
	}

	.wtercio{
		width: 232px;
	}
	.wmediotercio{
		width: 116px;
	}
	.w2tercio{
		width: 464px
	}

	.no-margin{
		margin:  0px;
	}
</style>
</head>
<body>
	<table class="table-info">
		<tbody>
			<tr>
				<td class="wtercio">
					<img style="width: 200px; height:120px;" src="{{ asset('images/logo.png') }}">
				</td>
				<td class="wtercio text-center">
					<h3 class="no-margin">{{ strtoupper($empresa->razon_social) }}</h3>
					<h4 class="no-margin">NIT: {{ $empresa->nit }}</h4>
					{{-- <h4 class="no-margin">Mayor Calidad, Mayor Nutrición</h4> --}}
					<p class="no-margin">Teléfono: {{ $empresa->telefono }}</p>
					<p class="no-margin">Dirección: {{ $empresa->direccion }}</p>
				</td>
				<td class="wtercio text-center">
					<h3>LIQUIDACION</h3>
                    <p>Fecha liquidación: {{$liquidacion->date}}</p>
					<br>
					<h4 class="no-margin">PROGRAMACIÓN:</h4>
					<p>{{ $programacion->id }}</p>
					<h4 class="no-margin">LOTE:</h4>
					<p>{{ $programacion->lote_id}}</p>
				</td>
			</tr>
		</tbody>
	</table>

	<table>
		<tbody>
			<tr>
				<td>
					<table class="table table-even table-font">
						<caption><h3>DESCRIPCION</h3></caption>
						<tbody>
							<tr>
								<td><strong>Cantidad:</strong></td>
								<td class="align-r">{{ $programacion->num_animales}} Und.</td>
							</tr>
							<tr>
								<td><strong>Peso en pie:</strong></td>
								<td class="align-r">{{ number_format( $ppe, 2 , "," , "." ) }} Kg</td>
							</tr>
							<tr>
								<td><strong>Peso canal caliente:</strong></td>
								<td class="align-r"> {{ number_format( $pcc, 2 , "," , "." ) }} Kg</td>
							</tr>
							<tr>
								<td><strong>Peso canal frio:</strong></td>
								<td class="align-r">{{ number_format( $pcr, 2 , "," , "." ) }} Kg</td>
							</tr>
							<tr>
								<td><strong>Kg x Animal (Carne):</strong></td>
								<td class="align-r">{{ number_format($kilosCarneProm, 2 , "," , "." ) }} Kg</td>
							</tr>
							<tr>
								<td><strong>Costo Kilo Canal:</strong></td>
								<td class="align-r">$ {{ number_format( ( ($ppe * $liquidacion->costoPrecio) + ($programacion->num_animales * $liquidacion->costoSacrificio) + ($programacion->num_animales * $liquidacion->costoTransporte) ) /  $pcr, 0 , "," , "." ) }}</td>
							</tr>
							<tr>
								<td><strong>Proveedor</strong></td>
								<td class="align-r">{{ $procedencia->proveedor }}</td>
							</tr>
							<tr>
								<td><strong>Sucursal</strong></td>
								<td class="align-r">{{ $procedencia->sucursal }}</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table class="table table-even table-font">
						<caption><h3>COSTOS</h3></caption>
						<tbody>
							<tr>
								<td><strong></strong></td>
								<td><strong>Valor Unidad</strong></td>
								<td><strong>Cantidad</strong></td>
								<td><strong>Total</strong></td>
							</tr>
							<tr>
								<td><strong>Precio</strong></td>
								<td class="align-r">$ {{ number_format( $liquidacion->costoPrecio, 0 , "," , "." ) }}</td>
								<td class="align-r">{{ $ppe }} Kg</td>
								<td class="align-r">$ {{ number_format( $ppe * $liquidacion->costoPrecio, 0 , "," , "." ) }}</td>
							</tr>
							<tr>
								<td><strong>Sacrificio</strong></td>
								<td class="align-r">$ {{ number_format( $liquidacion->costoSacrificio, 0 , "," , "." ) }}</td>
								<td class="align-r">{{ $programacion->num_animales}} Und</td>
								<td class="align-r">$ {{ number_format( $programacion->num_animales * $liquidacion->costoSacrificio, 0 , "," , "." )  }}</td>
							</tr>
							<tr>
								<td><strong>Desposte</strong></td>
								<td class="align-r">$ {{ number_format( $liquidacion->costoDesposte, 0 , "," , "." )  }}</td>
								<td class="align-r">{{ $programacion->num_animales}} Und</td>
								<td class="align-r">$ {{ number_format( $programacion->num_animales * $liquidacion->costoDesposte, 0 , "," , "." )  }}</td>
							</tr>
							<tr>
								<td><strong>Empaque</strong></td>
								<td class="align-r"> $ {{ number_format( $liquidacion->costoEmpaque, 0 , "," , "." ) }}</td>
								<td class="align-r">{{ $kilosVacio }} Kg</td>
								<td class="align-r"> $ {{ number_format( $liquidacion->costoEmpaque*$kilosVacio, 0 , "," , "." )  }}</td>
							</tr>
							<tr>
								<td><strong>Trans Bene - Planta</strong></td>
								<td class="align-r"> $ {{ number_format( $liquidacion->costoTransporte, 0 , "," , "." ) }}</td>
								<td class="align-r">{{ $programacion->num_animales}} Und</td>
								<td class="align-r"> $ {{ number_format( $programacion->num_animales * $liquidacion->costoTransporte, 0 , "," , "." )  }}</td>
							</tr>
							<tr>
								<td><strong>Trans Planta - Punto</strong></td>
								<td class="align-r"> $ {{ number_format( $liquidacion->costoTransportePlantaPunto, 0 , "," , "." ) }}</td>
								<td class="align-r">{{ $programacion->num_animales}} Und</td>
								<td class="align-r"> $ {{ number_format( $programacion->num_animales * $liquidacion->costoTransportePlantaPunto, 0 , "," , "." )  }}</td>
							</tr>
							<tr>
								<td><strong>-</strong></td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<table>
		<tbody>
			<tr>
				<td>
					<table class="table table-even table-font">
						<caption><h3>UTILIDADES</h3></caption>
						<tbody>
							<tr>
								<td><strong>Costo prog:</strong></td>
								<td class="align-r">$ {{ number_format( $costoTotal, 0 , "," , "." ) }}</td>
							</tr>
							<tr>
								<td><strong>Valor venta:</strong></td>
								<td class="align-r">$ {{ number_format( $ventaTotal, 0 , "," , "." ) }}</td>
							</tr>
							<tr>
								<td><strong>Utilidad prog:</strong></td>
								<td class="align-r">$ {{ number_format( $ventaTotal - $costoTotal, 0 , "," , "." )    }}</td>
							</tr>
							<tr>
								<td><strong>Utilidad x animal:</strong></td>
								<td class="align-r">$ {{ number_format( ($ventaTotal - $costoTotal) / $programacion->num_animales, 0 , "," , "." ) }}</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td>
					<table class="table table-even table-font">
						<caption><h3>RENDIMIENTOS</h3></caption>
						<tbody>
							<tr>
								<td><strong>Rendimiento PPE -> PCC</strong></td>
								<td class="align-r">{{ number_format( ($pcc/$ppe)*100, 2 , "," , "." )  }}%</td>
							</tr>
							<tr>
								<td><strong>Rendimiento PCC -> PCR</strong></td>
								<td class="align-r">{{  number_format( ($pcr/$pcc)*100, 2 , "," , "." )  }}%</td>
							</tr>
							<tr>
								<td><strong>Rentabilidad</strong></td>
								<td class="align-r">{{ number_format( (($ventaTotal - $costoTotal)/$ventaTotal)*100, 0 , "," , "." ) }}%</td>
							</tr>
							<tr>
								<td><strong></strong></td>
								<td class="align-r">.</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	<h3>Productos</h3>
	<table class="table table-even table-font">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Peso (Kg)</th>
				<th>Precio Venta Unit ($)</th>
				<th>Precio Total Venta ($)</th>
				<th>Porcentaje (%)</th>
				<th>Costo Unit ($)</th>
				<th>Costo Total ($)</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($productos as $element)
				<tr>
					<td>{{ $element->nombre }}</td>
					<td class="align-r">{{  number_format( $element->cantidad, 2 , "," , "." ) 	}}</td>
					<td class="align-r">{{ number_format( $element->precio_venta, 0 , "," , "." )	}}</td>
					<td class="align-r">{{ number_format( $element->precio_venta * $element->cantidad, 0 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( (($element->precio_venta * $element->cantidad)/$ventaTotal)*100, 2 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( ($costoTotal/$ventaTotal)*$element->precio_venta  , 0 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( ($costoTotal/$ventaTotal)*$element->precio_venta*$element->cantidad  , 0 , "," , "." ) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

	<h3>Recuperaciones</h3>
	<table class="table table-even table-font">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Peso (Kg)</th>
				<th>Precio Venta Unit ($)</th>
				<th>Precio Total Venta ($)</th>
				<th>Porcentaje (%)</th>
				<th>Costo Unit ($)</th>
				<th>Costo Total ($)</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($recuperaciones as $element)
				<tr>
					<td>{{ $element->nombre }}</td>
					<td class="align-r">{{  number_format( $element->cantidad, 2 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( $element->precio_venta, 0 , "," , "." )	}}</td>
					<td class="align-r">{{ number_format( $element->precio_venta * $element->cantidad, 0 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( (($element->precio_venta * $element->cantidad)/$ventaTotal)*100, 2 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( ($costoTotal/$ventaTotal)*$element->precio_venta  , 0 , "," , "." ) }}</td>
					<td class="align-r">{{ number_format( ($costoTotal/$ventaTotal)*$element->precio_venta*$element->cantidad  , 0 , "," , "." ) }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>


</body>
</html>
