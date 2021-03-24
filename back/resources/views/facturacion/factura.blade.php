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

	p,h3,h1,h4,h5{
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
    h6{ font-size: 5 }
</style>
</head>
<body>
	<table class="table-info">
		<tbody>
			<tr>
				<td class="wtercio">
					@if ($movimiento->qr)
						{!! DNS2D::getBarcodeHTML( $movimiento->qr , 'QRCODE',2,2) !!}
					@else
						<img style="width: 200px; height:120px;" src="{{ asset('images/logo.png') }}">
					@endif
				</td>
				<td class="wtercio text-center">
					<h3 class="no-margin">{{ $empresa->razon_social }}</h3>
					<h4 class="no-margin">{{ $empresa->nombre }}</h4>
					<h4 class="no-margin">NIT: {{ $empresa->nit }}</h4>
					<p class="no-margin">Teléfono: {{ $empresa->telefono }}</p>
					<p class="no-margin">Dirección: {{ $empresa->direccion }}</p>
				</td>
				<td class="wtercio text-center">

					<table class="table-info">
						<tbody>
							<tr>
								<td colspan="2" class="text-center">
									@if ($movimiento->qr)
										<img style="width: 200px; height:120px;" src="{{ asset('images/logo.png') }}">
									@endif
									<h3>{{  $tipoDoc->nombre_alt }}</h3>
								</td>
							</tr>
							<tr>
								<td class="text-center">
									@if ( $tipoDoc->prefijo != null)
										<p>N° {{ strtoupper($tipoDoc->prefijo) }}{{ $movimiento->consecutivo }}</p>
									@else
										<p>N° {{ $movimiento->consecutivo }}</p>
									@endif
								</td>
								<td class="text-center">
									<p>{{ $movimiento->fecha_facturacion }}</p>
								</td>
							</tr>

						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>

	@if ($movimiento->cufe)
		<h5 style="font-size:12px">CUFE: {{ $movimiento->cufe }}</h5>
	@endif

	<table class="table-info">
		<tbody>
			<tr>
				<td class="w2tercio">
				@if ( $tipoDoc->naturaleza == 1)
					<h3>{{ $tipoDoc->nombre_alt }} para:</h3>
				@else
					<h3>MOVIMIENTO PARA:</h3>
				@endif
					<h3>{{ strtoupper($tercero->nombre) }}</h3>
					<p><strong>Nit:</strong> {{ $tercero->documento}}</p>
					<p><strong>Sucursal:</strong> {{ $sucursal->nombre}}</p>
					<p><strong>Dirección:</strong> {{ $sucursal->direccion}}</p>
                    <p><strong>Teléfono:</strong> {{ $sucursal->telefono}}</p>
                        @if($relatedDocument != null)
                            <p><strong>Factura:</strong> {{ $relatedDocument->consecutivo }} </p>
                        @endif
				</td>
				<td class="wtercio">

				@if ( $tipoDoc->naturaleza == 1)
					<p><strong>Fecha Facturación:</strong> {{ $movimiento->fecha_facturacion }} </p>
					<p><strong>Fecha Vencimiento:</strong> {{ $movimiento->fecha_vencimiento }} </p>
                    @if ( $movimiento->sal_mercancia_consec != null)
                        <p><strong>N° de Guía 002DM - {{str_pad($movimiento->sal_mercancia_consec, 6, "0", STR_PAD_LEFT)}} - {{substr(date("Y"),2,4)}}</strong></p>
                    @endif
				@else
                    <p><strong>Fecha Movimiento:</strong> {{ $movimiento->fecha_facturacion }} </p>
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
					<td style="text-align: right">{{  number_format( (($linea->precio - ($linea->precio * ($linea->descporcentaje/100))) + (($linea->precio - ($linea->precio * ($linea->descporcentaje/100))) * ($linea->iva/100))) * $linea->cantidad  , 0, ',', '.') }}</td>
				</tr>
			@endforeach

		</tbody>
	</table>

	<table class="table-info">
		<tbody>
			<tr>
				<td class="wtercio">
					<p class="text-footer">Factura impresa por computador en {{ $empresa->razon_social }} NIT: {{ $empresa->nit }} Numeración autorizada desde {{ strtoupper($tipoDoc->prefijo)}} {{ $tipoDoc->ini_num_fac }} al {{ strtoupper($tipoDoc->prefijo)}} {{ $tipoDoc->fin_num_fac }} Resolucion Nro: {{ $tipoDoc->resolucion }} de {{ $tipoDoc->fec_resolucion }}</p>
					@if ($tipoDoc->nota != null)
						<p>{{ strtoupper($tipoDoc->nota) }}</p>
					@endif
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
					<p>${{ number_format($movimiento->subtotal, 0, ',', '.') }}</p>
					<p>${{ number_format($movimiento->descuento, 0, ',', '.') }}</p>
					<p>${{ number_format($movimiento->ivatotal, 0, ',', '.') }}</p>
					<p>${{ number_format($movimiento->total, 0, ',', '.') }}</p>
				</td>
			</tr>
		</tbody>
	</table>

	@if ($movimiento->nota != null)
		<table class="table-info">
			<tbody>
				<tr>
					<td>
						<p><strong>Nota:</strong> {{ $movimiento->nota }}</p>
					</td>
				</tr>
			</tbody>
		</table>
	@endif

	<h4 style="font-size: 14px">TÉRMINOS Y CONDICIONES</h4>

	<table class="table-info">
		<tbody>
			<tr>
				<td class="wmedio" >
					<p class="text-footer">No se aceptan reclamos luego de 1 día e recibida la mercancia. Los pagos efectuados despues de la fecha de vencimiento tendrán un recargo por mes o fracción a la tasa maxima de interés de mora permitida por la ley. Esta factura cambiaria de compraventa se asimia en todos sus efectos a una letra de cambio segun artículo 774 del codigo de comercio.</p>
				</td>
			</tr>
		</tbody>
    </table>

    <table class="table-info">
		<tbody>
			<tr>
				<td class="wmedio" >
                    <br>
                    <p style="text-align:center" class="text-footer"> Documento impreso desde SGC de Byteco S.A.S  -  Nit: 901389565-8 </p>
				</td>
			</tr>
		</tbody>
	</table>

	<br>



</body>
</html>
