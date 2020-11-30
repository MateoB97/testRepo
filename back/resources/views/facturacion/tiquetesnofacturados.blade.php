<!DOCTYPE html>
<html>
<head>
{{-- 	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
	<title>Certificado</title>
<style>
	.table {
	  font-family: Courier, sans-serif;
	  border-collapse: collapse;
	  
	}

	.table-info {
	  font-family: arial, sans-serif;
	}

	p,h3,h1,h4{
		font-family: "Lucida Console", Courier, monospace;
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

	<table class="table table-font">
		<thead>
			<tr>
				<th>Linea</th>
				<th>Cod Producto</th>
				<th>Producto</th>
				<th>Precio ($)</th>
				<th>cantidad</th>
				<th>unidades</th>
				<th>Total ($)</th>
			</tr>
		</thead>
		<tbody>

			@foreach ($etiqueta as $key => $mov)
				@if ($key == 0)
					<tr>
						<td colspan="8" style="text-align: center"><strong>N° Tiquete: {{ $mov['tiquete'] }} - Vendedor: {{ $mov['vendedor'] }} @if ($mov['bascula']) - bascula: {{ $mov['bascula'] }} @endif </strong></td>
					</tr>
					@php
    					$totalCounter = intval($mov['total']);
					@endphp
				@elseif ( $mov['tiquete'] != $etiqueta[$key -1]['tiquete'])
					<tr>
						<td colspan="6" rowspan="" headers=""><strong>Total Tiquete:</strong></td>
						<td style="text-align: right"><strong>$ {{ number_format($totalCounter, 0, ',', '.') }}</strong></td>
					</tr>
				 	<tr>
						<td colspan="8" style="text-align: center"><strong>N° Tiquete: {{ $mov['tiquete'] }} - Vendedor: {{ $mov['vendedor'] }}  @if ($mov['bascula']) - bascula: {{ $mov['bascula'] }} @endif </strong></td>
					</tr>
					@php
    					$totalCounter = intval($mov['total']);
					@endphp
				@else
					@php
    					$totalCounter += intval($mov['total']);
					@endphp
				@endif
				<tr>
					<td style="text-align: right">{{ $mov['linea_tiquete'] }}</td>
					<td style="text-align: right">{{ $mov['codigo'] }}</td>
					<td style="text-align: right">{{ $mov['producto'] }}</td>
					<td style="text-align: right">{{ number_format($mov['precio'], 0, ',', '.') }}</td>
					<td style="text-align: right">{{ $mov['cantidad'] }}</td>
					<td style="text-align: right">{{ $mov['unidades'] }}</td>
					<td style="text-align: right">{{ number_format($mov['total'], 0, ',', '.') }}</td>
				</tr>
			@endforeach
			<tr>
				<td colspan="6" rowspan="" headers=""><strong>Total Tiquete:</strong></td>
				<td style="text-align: right"><strong>$ {{ number_format($totalCounter, 0, ',', '.')  }}</strong></td>
			</tr>
		</tbody>
	</table>
</body>
</html>