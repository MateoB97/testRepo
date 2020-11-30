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

	p,h3,h1,h4{
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
	.w50 {
		width: 50%;
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
					<h3 class="no-margin">{{ $empresa->razon_social }}</h3>
					<h4 class="no-margin">{{ $empresa->nombre }}</h4>
					<h4 class="no-margin">NIT: {{ $empresa->nit }}</h4>
					<p class="no-margin">Teléfono: {{ $empresa->telefono }}</p>
					<p class="no-margin">Dirección: {{ $empresa->direccion }}</p>
				</td>
				<td class="wtercio text-center">
					<p>{{ $tipoRecCaja->nombre }}</p>
					<p>N° {{ $recibo->consecutivo }}</p>
					<p>Fecha: {{ $recibo->fecha_recibo }}</p>
				</td>
			</tr>
		</tbody>
	</table>

	<br>
	<br>

	<table class="table-info">
		<tbody>
			<tr>
				<td class="wmediotercio">
					<p><strong>Recibimos de:</strong></p>
					<p><strong>Sucursal: </strong></p>
					<p><strong>NIT:</strong></p>
					<p><strong>Dirección</strong></p>
				</td>
				<td class="wmediotercio">
					<p>{{ $tercero->nombre }} </p>
					<p>{{ $sucursal->nombre }} </p>
					<p>{{ $tercero->documento }} </p>
					<p>{{ $sucursal->direccion }} </p>
				</td>
				<td class="wmediotercio">
					<p><strong>Telefono</strong></p>
					{{-- <p><strong>Ciudad</strong></p> --}}
				</td>
				<td class="wmediotercio">
					<p>{{ $sucursal->telefono }} </p>
					{{-- <p> XXXXXXXXXX </p> --}}
				</td>
			</tr>
		</tbody>
	</table>

	<br>
	<br>

	<table class="table table-even table-font">
		<thead>
			<tr>
				<th>Consecutivo</th>
				<th>Concepto</th>
				<th>Saldo ($ COP)</th>
				<th>Valor ($ COP)</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($lineas as $linea)
				<tr>
					<td>{{ $linea->consec_mov }}</td>
					<td style="text-align: center">{{ $linea->tipo_mov }} - {{ $linea->fecha_mov }}</td>
					<td style="text-align: right">{{ number_format($linea->saldo_mov, 2, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($linea->valor, 2, ',', '.') }}</td>
				</tr>
			@endforeach

		</tbody>
	</table>

	<table class="table-info">
		<tbody>
			<tr>
				<td class="wtercio" style="border: 1px solid">
					<p><strong>FORMAS DE PAGO</strong></p>
					<table class="table-info">
						<tbody>
							@foreach ($pagos as $pago)
								<tr>
									<td><p>{{ $pago->forma_nombre }}</p></td>
									<td style="text-align: right">
										<p>${{ number_format($pago->valor, 2, ',', '.') }}</p>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</td>
				<td class="wtercio">
					
				</td>
				<td class="wmediotercio text-center">
					<p><strong>TOTAL RECIBIDO</strong></p>
				</td>
				<td class="wmediotercio text-center">
					<p>${{ number_format($recibo->total, 2, ',', '.') }}</p>
				</td>
			</tr>
		</tbody>
	</table>

	<table class="table table-info">
		<thead>
			<tr>
				<th><p><strong>Observaciones</strong></p></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><p>{{ $recibo->ajuste_observacion }}</p></td>
			</tr>
		</tbody>
	</table>

</body>
</html>