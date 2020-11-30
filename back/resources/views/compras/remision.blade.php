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
		font-size: 10px !important;
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
				<td class="w2tercio">
				@if ( $tipoCompra->naturaleza == 1)
					<h3>{{ $tipoCompra->nombre_alt }} de:</h3>
				@else
					<h3>COMPRA PARA:</h3>
				@endif
					<h3>{{ strtoupper($tercero->nombre) }}</h3>
					<p><strong>Nit:</strong> {{ $tercero->documento}}</p>
					<p><strong>Sucursal:</strong> {{ $sucursal->nombre}}</p>
					<p><strong>Dirección:</strong> {{ $sucursal->direccion}}</p>
					<p><strong>Teléfono:</strong> {{ $sucursal->telefono}}</p>
				</td>
				<td class="wtercio">
					
				@if ( $tipoCompra->naturaleza == 1)
					<p><strong>Fecha Compra:</strong> {{ $compra->fecha_compra }} </p>
					<p><strong>Fecha Vencimiento:</strong> {{ $compra->fecha_vencimiento }} </p>
				@else
					<p><strong>Fecha compra:</strong> {{ $compra->fecha_compra }} </p>
				@endif
				</td>
			</tr>
		</tbody>
	</table>

	<table class="table table-even table-font">
		<thead>
			<tr>
				<th>Descripción</th>
				<th>Cantidad</th>
				<th>V. Unitario</th>
				<th>Impuesto</th>
				<th>Descuento</th>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($lineas as $linea)
				<tr>
					<td>{{ $linea->producto }}</td>
					<td style="text-align: right">{{ number_format($linea->cantidad, 2, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($linea->precio, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ $linea->iva	}}%</td>
					<td style="text-align: right">{{ $linea->descporcentaje	}}</td>
					<td style="text-align: right">{{  number_format( (($linea->precio - ($linea->precio * $linea->descporcentaje)) + (($linea->precio - ($linea->precio * $linea->descporcentaje)) * ($linea->iva/100))) * $linea->cantidad  , 0, ',', '.') }}</td>
				</tr>
			@endforeach

		</tbody>
	</table>

	<table class="table-info">
		<tbody>
			<tr>
				<td class="wtercio">
				</td>
				<td class="wtercio">
				</td>
				<td class="wmediotercio text-center">
					<p><strong>SUBTOTAL</strong></p>
					<p><strong>DESCUENTO</strong></p>
					<p><strong>IVA</strong></p>
					<p><strong>TOTAL</strong></p>
				</td>
				<td class="wmediotercio text-center">
					<p>${{ number_format($compra->subtotal, 0, ',', '.') }}</p>
					<p>${{ number_format($compra->descuento, 0, ',', '.') }}</p>
					<p>${{ number_format($compra->ivatotal, 0, ',', '.') }}</p>
					<p>${{ number_format($compra->total, 0, ',', '.') }}</p>
				</td>
			</tr>
		</tbody>
	</table>

</body>
</html>