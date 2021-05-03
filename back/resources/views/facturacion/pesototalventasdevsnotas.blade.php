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
				<td class="wtercio text-center">
					<h3 class="no-margin">{{ $empresa->razon_social }}</h3>
					<h4 class="no-margin">{{ $empresa->nombre }}</h4>
					<h4 class="no-margin">NIT: {{ $empresa->nit }}</h4>
					<p class="no-margin">Teléfono: {{ $empresa->telefono }}</p>
					<p class="no-margin">Dirección: {{ $empresa->direccion }}</p>
				</td>
            </tr>
		</tbody>
	</table>

    <p class="no-margin"><strong>Fecha Inicial:</strong> {{ $fecha_ini }}</p>
    <p class="no-margin"><strong>Fecha Final:</strong>  {{ $fecha_fin }}</p>

	<h3 style="text-align:center">Listado de productos con Peso</h3>
	<table class="table table-font">
		<thead>
			<tr>
				<th><strong>Nombre Producto</th>
				<th>Peso Ventas</th>
				<th>Peso Devoluciones</th>
				<th>Peso Notas</th>
                <th>Peso Total</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($lineas as $linea)
				<tr>
					<td>{{ $linea->nombre }}</td>
                    <td>{{ number_format($linea->PesoVenta,2, ',', '.') }}</td>
                    <td>{{ number_format($linea->PesoDevs,2, ',', '.') }}</td>
                    <td>{{ number_format($linea->PesoNotas,2, ',', '.') }}</td>
                    <td>{{ number_format($linea->PesoTotal,2, ',', '.') }}</td>
				</tr>
			@endforeach
		</tbody>
	</table>

</body>
</html>
