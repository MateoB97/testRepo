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

	p,h3,h1,h4, span{
		font-family: "Lucida Console", Courier, monospace;
		font-size: 8px;
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
		width: 236px;

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
	span {
	    white-space: pre-wrap;
	    display: inline;
	    word-wrap:break-word;
	}
</style>
</head>
<body>
	@php 
		$ventasTotal = 0;
		$ingresosTotales = 0;
        $ingresosEfectivoTotal = 0;
	@endphp

	<div class="wtercio">
		<span>{{ str_pad(strtoupper($empresa->razon_social), $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad(strtoupper($empresa->nombre), $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad("NIT: ".$empresa->nit, $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad(strtoupper($empresa->tipo_regimen), $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad(strtoupper($empresa->direccion), $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad(strtoupper($municipio->nombre)." - ".strtoupper($departamento->nombre), $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad("TEL: ".$empresa->telefono, $caracLinea, " ", STR_PAD_BOTH) }}</span>
		<span>{{ str_pad("", $caracLinea, " ", STR_PAD_BOTH)}}</span>
		<span> {{ str_pad('CUADRE CAJA', $caracLinea - 18, " ", STR_PAD_RIGHT) . str_pad($id, 18, " ", STR_PAD_LEFT) }} </span>
        <span> {{'Fecha Apertura:' . str_pad($cuadre->created_at, $caracLinea - 15, " ", STR_PAD_LEFT)}} </span>
        <span> {{'Fecha Cierre:' . str_pad($cuadre->updated_at, $caracLinea - 13, " ", STR_PAD_LEFT)}} </span>
        <span> {{'Usuario cuadre:' . str_pad(App\User::find($cuadre->usuario_id)->name, $caracLinea - 15, " ", STR_PAD_LEFT) }} </span>
        <span> {{'+ Monto Apertura' . str_pad( number_format($cuadre->monto_apertura, 0, ',', '.'), $caracLinea - 16, " ", STR_PAD_LEFT)}} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('VENTAS', $caracLinea, " ", STR_PAD_BOTH)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>

        @foreach ($ventas as $venta)
        	<span> {{str_pad('- '.$venta->tipodoc, $caracLinea, " ", STR_PAD_RIGHT)}} </span>
            <span> {{str_pad('-- Total', $caracLinea-18, ".", STR_PAD_RIGHT) . str_pad( number_format($venta->total, 0, ',', '.'), 18, ".", STR_PAD_LEFT)}} </span>
            <span> {{str_pad('-- Dev Total', $caracLinea-18, ".", STR_PAD_RIGHT) . str_pad( number_format($venta->devolucion_total, 0, ',', '.'), 18, ".", STR_PAD_LEFT)}} </span>
            <span> {{str_pad('-- Gran Total', $caracLinea-18, ".", STR_PAD_RIGHT) . str_pad( number_format($venta->total - $venta->devolucion_total, 0, ',', '.'), 18, ".", STR_PAD_LEFT)}} </span>
            <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>

            @php
            	$ventasTotal = $ventasTotal + ($venta->total - $venta->devolucion_total);
            @endphp

        @endforeach

        <span> {{ str_pad('VENTAS TOTALES', $caracLinea-18, " ", STR_PAD_RIGHT) . str_pad( number_format($ventasTotal, 0, ',', '.'), 18, " ", STR_PAD_LEFT) }} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{ str_pad('TOTAL RECIBOS', $caracLinea-18, " ", STR_PAD_RIGHT) . str_pad( number_format($recibos->valor, 0, ',', '.'), 18, " ", STR_PAD_LEFT) }} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('INGRESOS', $caracLinea, " ", STR_PAD_BOTH)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>

 		@foreach ( $ingresos as $ingreso )

    		<span> {{str_pad($ingreso[0], $caracLinea-18, ".", STR_PAD_RIGHT) . str_pad( number_format($ingreso[1], 0, ',', '.'), 18, ".", STR_PAD_LEFT)}} </span>

    	@endforeach

    	<span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
    	<span> {{ str_pad('TOTAL INGRESOS', $caracLinea-18, " ", STR_PAD_RIGHT) . str_pad( number_format($totalIngresos, 0, ',', '.'), 18, " ", STR_PAD_LEFT) }} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('EGRESOS', $caracLinea, " ", STR_PAD_BOTH)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>

        @foreach ($tiposEgreso as $tipo)

        	@php
        		$egresos = App\EgreEgreso::where('egre_tipo_egreso_id',$tipo->id)->where('gen_cuadre_caja_id', $cuadre->id)->get();
        	@endphp

        	@if (count($egresos) > 0) 

        		@php 
        			$total = 0;
        		@endphp

        		<span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
                <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
                <span> {{str_pad(strtoupper($tipo->nombre), $caracLinea, " ", STR_PAD_BOTH)}} </span>

                @foreach ($egresos as $egreso)

                	@php 
	        			$total = intval($egreso->valor) + $total;
	        		@endphp

	        		<span> {{str_pad($egreso->consecutivo.' - '. substr($egreso->descripcion, 0, 25), $caracLinea - 12, " ", STR_PAD_RIGHT). str_pad('$'.number_format($egreso->valor, 0, ',', '.'), 12, " ", STR_PAD_LEFT)}}</span>

                @endforeach

                <span>{{'*Total'}}</span>
                <span>{{str_pad('$'.number_format($total, 0, ',', '.'), $caracLinea-6, " ", STR_PAD_LEFT)}}</span>

            @endif

        @endforeach

        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{'EGRESOS EFECT. TOT.' . str_pad( number_format($cuadre->total_egresos, 0, ',', '.'), $caracLinea-19, " ", STR_PAD_LEFT)}} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, "-", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('RESUMEN CUADRE', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{str_pad('', $caracLinea, " ", STR_PAD_RIGHT)}} </span>
        <span> {{'= BALANCE' . str_pad( number_format($ingresos[0][1] - $cuadre->total_egresos, 0, ',', '.'),  $caracLinea- 9, " ", STR_PAD_LEFT)}} </span>
        <span> {{'- Monto Cierre' . str_pad( number_format($cuadre->monto_cierre, 0, ',', '.'), $caracLinea- 14, " ", STR_PAD_LEFT) }} </span>
        <span> {{'DIFERENCIA' . str_pad( number_format($cuadre->monto_cierre - ($ingresos[0][1] - $cuadre->total_egresos), 0, ',', '.'), $caracLinea- 10, " ", STR_PAD_LEFT)}} </span>

	</div>
</body>
</html>