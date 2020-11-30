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

	<h3 style="text-align:center">Compras Netas Por Fecha</h3>
	<h4>Fecha Inicial: {{ $fechaIni }}</h4>
	<h4>Fecha Fin: {{ $fechaFin }}</h4>
	<h4>Fecha Impresion: {{ $hoy }}</h4>

	@foreach ($comprasTotales as $compraTotal)

		<h4 style="text-align:center">{{ $compraTotal[0] }}</h4>
		<table class="table table-font">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Subtotal</th>
					<th>Descuento</th>
					<th>Iva</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>VENTAS</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->subtotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->descuento, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->ivatotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->total, 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>DEVOLUCIONES</td>
					<td style="text-align: right">{{ number_format($compraTotal[2]->subtotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[2]->descuento, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[2]->ivatotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[2]->total, 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>TOTAL</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->subtotal - $compraTotal[2]->subtotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->descuento - $compraTotal[2]->descuento, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->ivatotal - $compraTotal[2]->ivatotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($compraTotal[1]->total - $compraTotal[2]->total, 0, ',', '.') }}</td>
				</tr>
			</tbody>
		</table>

		<br>
	@endforeach

	<h4 style="text-align:center">GRAN TOTAL</h4>
		<table class="table table-font">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Subtotal</th>
					<th>Descuento</th>
					<th>Iva</th>
					<th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>VENTAS</td>
					<td style="text-align: right">{{ number_format($granSubtotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granDescuento, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granIva, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granTotal, 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>DEVOLUCIONES</td>
					<td style="text-align: right">{{ number_format($granDevSubtotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granDevDescuento, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granDevIva, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granDevTotal, 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>TOTAL</td>
					<td style="text-align: right">{{ number_format($granSubtotal - $granDevSubtotal, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granDescuento - $granDevDescuento, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granIva - $granDevIva, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($granTotal - $granDevTotal, 0, ',', '.') }}</td>
				</tr>
			</tbody>
		</table>

		<br>
</body>
</html>