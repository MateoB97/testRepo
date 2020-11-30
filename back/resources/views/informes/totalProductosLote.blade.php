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

	p,h3,h1{
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
	<h3 style="text-align:center;">INFORME DE PRODUCTOS POR LOTE : {{ $lote }}</h3> 

	</br>

	<table class="table table-even table-font">
		<thead>
			<tr>
				<th>Producto</th>
				<th>Peso</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($itemsSumatoria as $element)
				<tr>
					<td>{{ $element->producto }}</td>
					<td>{{ $element->peso	}}</td>
				</tr>
			@endforeach

		</tbody>
	</table>

	<br>
	<br>
	
</body>
</html>