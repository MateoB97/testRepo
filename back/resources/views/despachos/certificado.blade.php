<!DOCTYPE html>
<html>
<head>
{{-- 	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
	<title>Certificado</title>
<style>
	.table {
	  font-family: arial, sans-serif;
	  border-collapse: collapse;
	  
	}

	.table-info {
	  font-family: arial, sans-serif;
	}

	p,h3,h1,h5{
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
</style>
</head>
<body>
	<p>Fecha: {{ $itemsSumatoria[0]->fecha_sal_mercancia }}</p>
	<h3 style="text-align:center;">CERTIFICADO DE CALIDAD</h3> 

	<table class="table-info">
		<tbody>
			<tr>
				<td>

					<p><strong>{{ strtoupper($empresa->razon_social) }}</strong></p>
					<p>{{ $empresa->nit }}</p>
					<p>Tel {{ $empresa->telefono }}</p>
					<p>{{ $empresa->direccion }}</p>
					<p>{{ $empresa->municipio }} - {{ $empresa->departamento }}</p>
					<p>Codigo Inscripción 002DM</p>
			
				</td>
				<td>

					<p> <strong>Cliente: {{ $tercero->nombre}}</strong></p>
					<p><strong>Nit:</strong> {{ $tercero->documento}}</p>
					<p><strong>Sucursal:</strong> {{ $sucursal->nombre}}</p>
					<p><strong>Dirección:</strong> {{ $sucursal->direccion}}</p>
					<p><strong>Teléfono:</strong> {{ $sucursal->telefono}}</p>
					<p><strong>{{ $sucursal->municipio }} - {{ $sucursal->departamento }} </strong></p>
		
				</td>
			</tr>
		</tbody>
	</table>

	</br>
	<table class="table-info" style="text-align: center;">
		<tbody>
			<tr>
				<td>
					<p><strong>Temperatura de despacho:</strong> {{ number_format($salMercancia->temperatura, 2, ',', '.') }} °C</p>
				</td>
				<td>
					<p><strong>Vehículo de Transporte:</strong> {{ strtoupper($salMercancia->vehiculo) }}</p>
				</td>
			</tr>
		</tbody>
	</table>

	<h3 style="text-align: center; text-transform: uppercase;">CARNE DE {{ $itemsSumatoria[0]->grupo }}</h3>

	<table class="table table-even table-font">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Lote</th>
				<th>Peso (Kg)</th>
				<th># Canastas</th>
				<th>Fecha de Sacrificio</th>
				<th>Fecha de Desposte</th>
				<th>Fecha de Empaque</th>
				<th>Fecha de Vencimiento</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($itemsSumatoria as $element)
				<tr>
					<td>{{ $element->producto }}</td>
					<td style="text-align: right;">{{ $element->lote	}}</td>
					<td style="text-align: right;">{{ $element->peso	}}</td>
					<td style="text-align: right;">{{ $element->canastas }}</td>
					<td style="text-align: right;">{{ $element->fecha_sacrificio }}</td>
					<td style="text-align: right;">{{ $element->fecha_desposte }}</td>
					<td style="text-align: right;">{{ $element->fecha_empaque	}}</td>
					<td style="text-align: right;">{{ $element->fecha_vencimiento	}}</td>
				</tr>
			@endforeach

		</tbody>
	</table>

	<h5>Total Canastas: {{ $totalCanastas }}</h5>
	<h5>Total Kilos: {{ $totalKilos }}</h5>

	<h3 class="text-center">ESPECIFICACIONES DEL TRASLADO</h3>
	<table class="table table-font">
		<thead>
			<tr>
				<th>Características</th>
				<th>Resultado</th>
				<th>Especificaciones</th>
				<th>Método</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Olor</td>
				<td>Cumple</td>
				<td>Caracteristico a carne fresca</td>
				<td>Olfativo</td>
			</tr>
			<tr>
				<td>Color</td>
				<td>Cumple</td>
				<td>Color rosado intenso con tonalidades blancas para carne de cerdo y rojo claro para carne de res</td>
				<td>Visual</td>
			</tr>
			<tr>
				<td>Textura y/o Aspecto</td>
				<td>Cumple</td>
				<td>Firme/ a carne cruda</td>
				<td>Visual-Tacto</td>
			</tr>
			<tr>
				<td>Temperatura despacho</td>
				<td>Cumple</td>
				<td>Producto en refrigeración de 0-4°C
				Producto en Congelación a -18°C</td>
				<td>Toma directa centro masa muscular</td>
			</tr>
		</tbody>
	</table>

	<div style="display:block; width: 100%; margin-top: 1em">
		<div style="width: 49%; float: left">
			<img style="max-width: 100%" src="{{ asset('images/firma.png') }}">
		</div>
		<div style="width: 49%; float: right; text-align: justify;">
			<p class="text-footer">Los animales de los cuales proviene la carne son de animales beneficiados y despostados según la normatividad vigente.			El producto relacionado en el presente certificado cumple con todas las características de un producto apto para consumo humano.					Las caracteristicas sensoriales y vida útil de este producto se conservan siempre y cuando se almacene en las condiciones adecuadas(Refrigeración de 0-4ºC, Congelacion -18ºC).</p>
			<p class="text-footer">En caso de petición, queja o reclamo te puedes comunicar al correo pqrssupercarnesjh@gmail.com.</p>
		</div>
	</div>
	
</body>
</html>