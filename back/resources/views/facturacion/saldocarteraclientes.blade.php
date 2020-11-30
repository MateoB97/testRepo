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

	<h3 style="text-align:center">CUENTAS POR COBRAR</h3>

	<h4>Fecha Impresion: {{ $fecha }}</h4>

	<table class="table table-font">
		<thead>
			<tr>
				<th>Tipo Factura</th>
				<th>N°</th>
				<th>Fec Gen</th>
				<th>Fec Venc</th>
				<th>Total ($)</th>
				<th>Abonado ($)</th>
				<th>Saldo ($)</th>
			</tr>
		</thead>
		<tbody>
			@php
				$i = 0;
				$countTotal = 0;
				$countAbo = 0;
				$countSaldo = 0;
				$granTotal = 0;
				$granSaldo = 0;
				$sucursalTotal= 0;
				$sucursalSaldo= 0;
			@endphp
			@foreach ($lineas as $linea)
				@if ($i == 0)
					<tr style="background-color: #76DC45">
						<td colspan="2"><strong>{{ strtoupper($linea->documento) }}</strong></td>
						<td colspan="5"><strong>{{ strtoupper($linea->tercero) }}</strong></td>
					</tr>
					<tr style="background-color: #a0dadb">
						<td colspan="2"><strong>{{ strtoupper($linea->sucursal) }}</strong></td>
						<td colspan="2"><strong>{{ strtoupper($linea->telefono) }}</strong></td>
						<td colspan="3"><strong>{{ strtoupper($linea->direccion) }}</strong></td>
					</tr>
				@elseif ($linea->documento != $lineas[$i-1]->documento)
					<tr>
						<td colspan="4"><strong>Total sucursal</strong></td>
						<td colspan="1" style="text-align: center"><strong>{{ number_format($sucursalTotal, 0, ',', '.') }}</strong></td>
						<td colspan="1" style="text-align: center"><strong>{{ number_format( ($sucursalTotal - $sucursalSaldo), 0, ',', '.') }}</strong></td>
						<td colspan="1" style="text-align: center"><strong>{{ number_format($sucursalSaldo, 0, ',', '.') }}</strong></td>
					</tr>
					<tr>
						<td colspan="4"><strong>TOTAL</strong></td>
						<td colspan="1" style="text-align: center"><strong>{{ number_format($countTotal, 0, ',', '.') }}</strong></td>
						<td colspan="1" style="text-align: center"><strong>{{ number_format( ($countTotal - $countSaldo), 0, ',', '.') }}</strong></td>
						<td colspan="1" style="text-align: center"><strong>{{ number_format($countSaldo, 0, ',', '.') }}</strong></td>
					</tr>
					<tr>
						<td colspan="7"> <br> </td>
					</tr>
					@php
						$sucursalTotal = 0;
						$sucursalSaldo = 0;
						$granTotal = $granTotal +  $countTotal;
						$granSaldo = $granSaldo +  $countSaldo;
						$countTotal = 0;
						$countSaldo = 0;
					@endphp
					<tr style="background-color: #76DC45">
						<td colspan="2"><strong>{{ strtoupper($linea->documento) }}</strong></td>
						<td colspan="5"><strong>{{ strtoupper($linea->tercero) }}</strong></td>
					</tr>
					<tr style="background-color: #a0dadb">
						<td colspan="2"><strong>{{ strtoupper($linea->sucursal) }}</strong></td>
						<td colspan="2"><strong>{{ strtoupper($linea->telefono) }}</strong></td>
						<td colspan="3"><strong>{{ strtoupper($linea->direccion) }}</strong></td>
					</tr>
				@else
					@if ($linea->sucursal_id != $lineas[$i-1]->sucursal_id)
						<tr>
							<td colspan="4"><strong>total sucursal</strong></td>
							<td colspan="1" style="text-align: center"><strong>{{ number_format($sucursalTotal, 0, ',', '.') }}</strong></td>
							<td colspan="1" style="text-align: center"><strong>{{ number_format( ($sucursalTotal - $sucursalSaldo), 0, ',', '.') }}</strong></td>
							<td colspan="1" style="text-align: center"><strong>{{ number_format($sucursalSaldo, 0, ',', '.') }}</strong></td>
						</tr>
						@php
							$sucursalTotal = 0;
							$sucursalSaldo = 0;
						@endphp
						<tr style="background-color: #a0dadb">
							<td colspan="2"><strong>{{ strtoupper($linea->sucursal) }}</strong></td>
							<td colspan="2"><strong>{{ strtoupper($linea->telefono) }}</strong></td>
							<td colspan="3"><strong>{{ strtoupper($linea->direccion) }}</strong></td>
						</tr>
					@endif
				@endif
				<tr>
					<td>{{ $linea->tipo }}</td>
					<td>{{ $linea->consecutivo }}</td>
					<td>{{ $linea->fecha_facturacion }}</td>
					<td>{{ $linea->fecha_vencimiento }}</td>
					<td style="text-align: right">{{ number_format($linea->total, 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format( (intval($linea->total) - intval($linea->saldo)), 0, ',', '.') }}</td>
					<td style="text-align: right">{{ number_format($linea->saldo, 0, ',', '.') }}</td>
				</tr>
				@php
					$countTotal = $linea->total + $countTotal;
					$countSaldo = $linea->saldo + $countSaldo;

					$sucursalTotal = $linea->total + $sucursalTotal;
					$sucursalSaldo = $linea->saldo + $sucursalSaldo;
					$i= $i +1;
				@endphp
			@endforeach
			<tr>
				<td colspan="4"><strong>Total sucursal</strong></td>
				<td colspan="1" style="text-align: center"><strong>{{ number_format($sucursalTotal, 0, ',', '.') }}</strong></td>
				<td colspan="1" style="text-align: center"><strong>{{ number_format( ($sucursalTotal - $sucursalSaldo), 0, ',', '.') }}</strong></td>
				<td colspan="1" style="text-align: center"><strong>{{ number_format($sucursalSaldo, 0, ',', '.') }}</strong></td>
			</tr>
			<tr>
				<td colspan="4"><strong>TOTAL</strong></td>
				<td colspan="1" style="text-align: center"><strong>{{ number_format($countTotal, 0, ',', '.') }}</strong></td>
				<td colspan="1" style="text-align: center"><strong>{{ number_format( ($countTotal - $countSaldo), 0, ',', '.') }}</strong></td>
				<td colspan="1" style="text-align: center"><strong>{{ number_format($countSaldo, 0, ',', '.') }}</strong></td>
			</tr>
			@php
				$granTotal = $granTotal +  $countTotal;
				$granSaldo = $granSaldo +  $countSaldo;
			@endphp

		</tbody>
	</table>

	<p><h3>Total Cartera: ${{ number_format($granTotal, 0, ',', '.')}}</h3></p>
	<p><h3>Total Abonado: ${{ number_format($granTotal-$granSaldo, 0, ',', '.')}}</h3></p>
	<p><h3>Total Saldo: ${{ number_format($granSaldo, 0, ',', '.')}}</h3></p>

</body>
</html>