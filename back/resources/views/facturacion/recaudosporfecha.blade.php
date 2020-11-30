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

	<h3 style="text-align:center">Recaudos por fecha</h3>
	<h4>Fecha Inicial: {{ $fechaIni }}</h4>
	<h4>Fecha Fin: {{ $fechaFin }}</h4>
	<h4>Fecha Impresion: {{ $hoy }}</h4>

	@foreach ($recaudoTotales as $recaudo)

		<h4 style="text-align:center">{{ $recaudo[0] }}</h4>
		<table class="table table-font">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Recibos de caja</td>
					<td style="text-align: right">{{ number_format($recaudo[1], 0, ',', '.') }}</td>

				</tr>
				<tr>
					<td>POS</td>
					<td style="text-align: right">{{ number_format($recaudo[2], 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>TOTAL</td>
					<td style="text-align: right">{{ number_format($recaudo[1] + $recaudo[2], 0, ',', '.') }}</td>
				</tr>
			</tbody>
		</table>

		<br>
	@endforeach

	<h4 style="text-align:center">RECAUDO TOTAL</h4>
		<table class="table table-font">
			<thead>
				<tr>
					<th>Tipo</th>
					<th>Valor</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>POS</td>
					<td style="text-align: right">{{ number_format($totalPOS, 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>Recibos de caja</td>
					<td style="text-align: right">{{ number_format($totalRecibos, 0, ',', '.') }}</td>
				</tr>
				<tr>
					<td>TOTAL</td>
					<td style="text-align: right">{{ number_format($totalPOS + $totalRecibos, 0, ',', '.') }}</td>
				</tr>
			</tbody>
		</table>

		<br>
</body>
</html>