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
	<h3 style="text-align:center">Cierre de Inventarios - Variación</h3>
	<h4 style="text-align:center">Fecha Inicial: {{ $fechaIni }} || Fecha Fin: {{ $fechaFin }} || Fecha Impresion: {{ $hoy }} </h4>
    <br>
	 @foreach ($subgrupos as $movimiento)
		<h4 style="text-align:center">{{ $movimiento->SubGrupo }}</h4>
			 <table class="table table-font">
			 	<thead>
					<tr>
						<th><strong>Producto</strong></th>
						<th><strong>Inv Actual</strong></th>
						<th><strong>Inv 1er Cierre</strong></th>
						<th><strong>Cant Entradas</strong></th>
						<th><strong>Cant Ventas</strong></th>
						<th><strong>Cant Devs</strong></th>
                        <th><strong>Cant Notas</strong></th>
						<th><strong>Inv 2do Cierre</strong></th>
						<th><strong>Inv Teorico</strong></th>
                        <th><strong>Merma</strong></th>
					</tr>
				</thead>
			<tbody>
					@foreach ($details->where('SubGrupoID','=', $movimiento->SubGrupoID) as $mov)
						<tr>
							<td style="text-align: right">{{ $mov->Producto }}</td>
							<td style="text-align: left">{{ $mov->InvActual }}</td>
							<td style="text-align: left">{{ $mov->InvInicial }}</td>
							<td style="text-align: right">{{ $mov->QtyEntradas }}</td>
                            <td style="text-align: right">{{ $mov->QtyVentas }}</td>
                            <td style="text-align: right">{{ $mov->QtyDevs }}</td>
                            <td style="text-align: right">{{ $mov->QtyNotas }}</td>
                            <td style="text-align: right">{{ $mov->InvFinal }}</td>
                            <td style="text-align: right">{{ $mov->InvTeorico }}</td>
                            <td style="text-align: right">{{ $mov->Merma }}</td>

						</tr>
					@endforeach
				</tbody>
                <tbody>
                <tr>
                    <td style="text-align: left"><strong>Totales: </strong></td>
                    <td style="text-align: left"> </td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('InvInicial')}}</strong></td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('QtyEntradas')}}</strong></td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('QtyVentas')}}</strong></td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('QtyDevs')}}</strong></td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('QtyNotas')}}</strong></td>
                    {{--  <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('QtyNotas') + $details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('QtyDevs')}}</strong></td>  --}}
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('InvFinal')}}</strong></td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('InvTeorico')}}</strong></td>
                    <td style="text-align: right"><strong>{{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('Merma')}}</strong></td>
                </tr>
                </tbody>
			</table>
        {{--  <h4 style="text-align:center">Total {{$movimiento->SubGrupo }}: {{$details->where('SubGrupoID','=', $movimiento->SubGrupoID)->sum('InvActual')}}</h4>  --}}
		<br>
	@endforeach
</body>
</html>
