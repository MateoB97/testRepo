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
    hr{
		page-break-after: always;
		border: none;
		margin: 0;
		padding: 0;
	}
</style>
</head>
<body>
{{-- {{set_time_limit(-1)}} --}}
	<h3 style="text-align:center">Ventas Netas Por Fecha xx</h3>
    <h4 style="text-align:center">Fecha Inicial: {{ $fechaIni }} || Fecha Fin: {{ $fechaFin }} || Fecha Impresion: {{ $hoy }} </h4>
    @foreach ($sucursales as $sucursal)
        <h3 style="text-align:center"> {{strtoupper($tercero->tercero)}} - {{$tercero->documento}}</h3>
        <h4 style="text-align:center"> {{strtoupper($sucursal->sucursal)}} - {{$sucursal->telefono}} - {{$sucursal->direccion}}</h3>
            {{-- INICIO VENTAS--}}
            @foreach ($tiposdocs as $tipoDoc)
            @if ( $detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id )->count() > 0 )
                <h3 style="text-align:left"> {{strtoupper($tipoDoc->nombre)}}</h3>
                <table class="table table-font">
                    <thead>
                        <tr style="background-color: #76DC45">
                            <th>Consec</th>
                            <th>Fecha</th>
                            <th>Subtotal</th>
                            <th>Iva Total</th>
                            <th>Total</th>
                            <th>Abonado</th>
                            <th>Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                @foreach ($detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id) as $ventas)
                    <tr>
                        <td style="text-align: right">{{ $ventas->consecutivo }}</td>
                        <td style="text-align: right">{{ $ventas->fecha }}</td>
                        <td style="text-align: right">{{ number_format($ventas->subtotal, 0, ',', '.') }}</td>
                        <td style="text-align: right">{{ number_format($ventas->ivatotal, 0, ',', '.') }}</td>
                        <td style="text-align: right">{{ number_format($ventas->total, 0, ',', '.') }}</td>
                        <td style="text-align: right">{{ number_format($ventas->total - $ventas->saldo, 0, ',', '.') }}</td>
                        <td style="text-align: right">{{ number_format($ventas->saldo, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
                <tr>
                <td colspan="2" rowspan="" headers=""></td>
                    <td style="text-align: right"> <b> {{ number_format($detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('subtotal'), 0, ',', '.') }}</td>
                    <td style="text-align: right"> <b> {{ number_format($detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('ivatotal'), 0, ',', '.') }}</td>
                    <td style="text-align: right"> <b> {{ number_format($detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('total'), 0, ',', '.') }}</td>
                    <td style="text-align: right"> <b> {{ number_format( ($detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('total') -  $detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('saldo')), 0, ',', '.') }}</td>
                    <td style="text-align: right"> <b> {{ number_format($detallesventasporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('saldo'), 0, ',', '.') }}</td>
                </tr>
            @endif
        </tbody>
        </table>
        @endforeach
        {{-- FIN VENTAS--}}
        {{-- INICIO DEVOLUCIONES--}}
         @foreach ($tiposdocs as $tipoDoc)
         @if ( $detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id )->count() > 0 )
             <h3 style="text-align:left">Devolución - {{strtoupper($tipoDoc->nombre)}}</h3>
             <table class="table table-font">
                 <thead>
                     <tr style="background-color: #76DC45">
                         <th>Consec</th>
                         <th>Fecha</th>
                         <th>Subtotal</th>
                         <th>Iva Total</th>
                         <th>Total</th>
                         <th>Abonado</th>
                         <th>Saldo</th>
                     </tr>
                 </thead>
                 <tbody>
             @foreach ($detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id) as $ventas)
                 <tr>
                     <td style="text-align: right">{{ $ventas->consecutivo }}</td>
                     <td style="text-align: right">{{ $ventas->fecha }}</td>
                     <td style="text-align: right">{{ number_format($ventas->subtotal, 0, ',', '.') }}</td>
                     <td style="text-align: right">{{ number_format($ventas->ivatotal, 0, ',', '.') }}</td>
                     <td style="text-align: right">{{ number_format($ventas->total, 0, ',', '.') }}</td>
                     <td style="text-align: right">{{ number_format($ventas->total - $ventas->saldo, 0, ',', '.') }}</td>
                     <td style="text-align: right">{{ number_format($ventas->saldo, 0, ',', '.') }}</td>
                 </tr>
             @endforeach
             <tr>
             <td colspan="2" rowspan="" headers=""></td>
                 <td style="text-align: right"> <b> {{ number_format($detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('subtotal'), 0, ',', '.') }}</td>
                 <td style="text-align: right"> <b> {{ number_format($detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('ivatotal'), 0, ',', '.') }}</td>
                 <td style="text-align: right"> <b> {{ number_format($detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('total'), 0, ',', '.') }}</td>
                 <td style="text-align: right"> <b> {{ number_format(($detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('total') -  $detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('saldo')), 0, ',', '.') }}</td>
                 <td style="text-align: right"> <b> {{ number_format($detallesdevolporfecha->where('tipoid', $tipoDoc->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('saldo'), 0, ',', '.') }}</td>
             </tr>
         @endif
     </tbody>
     </table>
     @endforeach
        {{--FIN DEVOLUCIONES --}}
        {{--INICIO RECIBOS --}}
        @foreach ($tiposrec as $tiporRec)
        @if ( $detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id )->count() > 0 )
            <h3 style="text-align:left">{{strtoupper($tiporRec->nombre)}}</h3>
            <table class="table table-font">
                <thead>
                    <tr style="background-color: #76DC45">
                        <th>Consec</th>
                        <th>Fecha</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                        <th>Abonado</th>
                        <th>Saldo</th>
                    </tr>
                </thead>
                <tbody>
            @foreach ($detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id) as $ventas)
                <tr>
                    <td style="text-align: right">{{ $ventas->consecutivo }}</td>
                    <td style="text-align: right">{{ $ventas->fecha }}</td>
                    <td style="text-align: right">{{ number_format($ventas->subtotal, 0, ',', '.') }}</td>
                    <td style="text-align: right">{{ number_format($ventas->total, 0, ',', '.') }}</td>
                    <td style="text-align: right">{{ number_format($ventas->total - $ventas->saldo, 0, ',', '.') }}</td>
                    <td style="text-align: right">{{ number_format($ventas->saldo, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
            <td colspan="2" rowspan="" headers=""></td>
                <td style="text-align: right"> <b> {{ number_format($detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('subtotal'), 0, ',', '.') }}</td>
                <td style="text-align: right"> <b> {{ number_format($detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('total'), 0, ',', '.') }}</td>
                <td style="text-align: right"> <b> {{ number_format( ($detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('total') -  $detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('saldo')), 0, ',', '.') }}</td>
                <td style="text-align: right"> <b> {{ number_format($detallesrecibosporfecha->where('tipoid', $tiporRec->id)->where('sucursal_id',$sucursal->sucursal_id)->sum('saldo'), 0, ',', '.') }}</td>
            </tr>
        @endif

    </tbody>
    </table>
    @endforeach
        {{--FIN RECIBOS --}}
    <hr>
    @endforeach

</body>
</html>
