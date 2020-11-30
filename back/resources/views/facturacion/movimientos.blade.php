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
		font-size: 8px;
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

	<h3 style="text-align:center">Ventas Netas Por Fecha Movs</h3>
	<h4 style="text-align:center">Fecha Inicial: {{ $fechaIni }} || Fecha Fin: {{ $fechaFin }} || Fecha Impresion: {{ $hoy }} </h4>
	@foreach ($movimientosTotal as $movimiento)

		<h4 style="text-align:center">{{ $movimiento[0] }}</h4>

		
			<table class="table table-font">
				<thead>
					<tr>
						<th>Consec</th>
						<th>Doc</th>
						<th>Tercero</th>
						<th>Sucursal</th>
						<th>Fecha</th>
						<th>Subtotal</th>
{{-- 						<th>Descuento</th>
						<th>IvaTotal</th> --}}
						<th>Total</th>
						<th>Abonado</th>
						<th>Saldo</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($movimiento[1] as $mov)
						<tr>
							<td style="text-align: right">{{ $mov->consecutivo }}</td>
							<td style="text-align: right">{{ $mov->documento }}</td>
							<td style="text-align: left">{{ $mov->tercero }}</td>
							<td style="text-align: left">{{ $mov->sucursal }}</td>
							<td style="text-align: right">{{ $mov->fecha }}</td>
							<td style="text-align: right">{{ number_format($mov->subtotal, 0, ',', '.') }}</td>
{{-- 							@if ($mov->descuento)
								<td style="text-align: right">{{ number_format($mov->descuento, 0, ',', '.') }}</td>
							@else
								<td style="text-align: right">0</td>
							@endif
							@if ($mov->ivatotal)
								<td style="text-align: right">{{ number_format($mov->ivatotal, 0, ',', '.') }}</td>
							@else
								<td style="text-align: right">0</td>
							@endif --}}
							<td style="text-align: right">{{ number_format($mov->total, 0, ',', '.') }}</td>
							<td style="text-align: right">{{ number_format($mov->total - $mov->saldo, 0, ',', '.') }}</td>
							<td style="text-align: right">{{ number_format($mov->saldo, 0, ',', '.') }}</td>

						</tr>
					@endforeach
					<tr>
						<td style="text-align: right"><strong>$ {{  number_format($movimiento[2], 0, ',', '.') }}</strong></td>
						<td style="text-align: right"><strong>$ {{  number_format($movimiento[3], 0, ',', '.') }}</strong></td>
						<td style="text-align: right"><strong>$ {{  number_format($movimiento[4], 0, ',', '.') }}</strong></td>
						<td style="text-align: right"><strong>$ {{  number_format($movimiento[5], 0, ',', '.') }}</strong></td>
					</tr>
				</tbody>
			</table>
		

		<br>
	@endforeach

</body>
</html>