<!DOCTYPE html>
<html>
<head>
{{-- 	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous"> --}}
	<title>Reporte Fiscal</title>
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
	<h3 style="text-align:center">REPORTE FISCAL</h3>
	<h4 style="text-align:center">Fecha Inicial: {{ $fechaIni }} || Fecha Final: {{ $fechaFin }}  || Fecha Impresion: {{ $hoy }} </h4>
    <h3 style="text-align:center"> {{$empresa->nombre . ' - ' . $empresa->nit}}</h3>
    <br>
			 <table class="table table-font">
			 	<thead>
					<tr>
                        <th><strong>Tipo Documento</strong></th>
						<th><strong>Consecutivo Inicial</strong></th>
						<th><strong>Consecutivo Final</strong></th>
					</tr>
				</thead>
			<tbody>
					@foreach ($headers as $header)
						<tr>
                            <td style="text-align: left">{{ $header->tipo }}</td>
							<td style="text-align: left">{{ $header->consec_ini }}</td>
							<td style="text-align: left">{{ $header->consec_fin }}</td>
						</tr>
					@endforeach
				</tbody>
                <tbody>
                </tbody>
			</table>
		<br>
        <br>
        <table class="table table-font">
            <thead>
               <tr>
                   <th style="text-align: center"><strong>FECHA</strong></th>
                   <th style="text-align: center"><strong>TOTAL EFECTIVO</strong></th>
                   <th style="text-align: center"><strong>TOTAL CREDITOS</strong></th>
                   <th style="text-align: center"><strong>VENTA TOTAL EN EL DIA</strong></th>
                   <th style="text-align: center"><strong>IMPUESTO</strong></th>

               </tr>
           </thead>
       <tbody>
               @foreach ($details as $detail)
                   <tr>
                       <td style="text-align: center">{{ $detail->fecha }}</td>
                       <td style="text-align: center">{{ $detail->TotalEfectivo }}</td>
                       <td style="text-align: center">{{ $detail->TotalCreditos }}</td>
                       <td style="text-align: center">{{ $detail->VentaTotalDia }}</td>
                       <td style="text-align: center">{{ $detail->Impuesto }}</td>
                   </tr>
               @endforeach
           </tbody>
           <tbody>
           </tbody>
       </table>
   <br>
   <br>
   <table class="table table-font">
       <thead>
          <tr>
              <th><strong>TOTAL FACTURAS EN EFECTIVO</strong></th>
              <th><strong>TOTAL FACTURAS A CREDITO</strong></th>
              <th><strong>VENTA TOTAL MES</strong></th>
          </tr>
      </thead>
  <tbody>
        <tr>
            <td style="text-align: left">{{ $totalEfectivo }}</td>
            <td style="text-align: left">{{ $totalCredito }}</td>
            <td style="text-align: left">{{ $totalVenta }}</td>
        </tr>
      </tbody>
      <tbody>
      </tbody>
  </table>
<br>
<br>
<table class="table table-font">
    <thead>
       <tr>
           <th><strong>INFORMACION PARCIAL</strong></th>
           <th><strong>TOTAL</strong></th>
           <th><strong>BASE</strong></th>
       </tr>
   </thead>
<tbody>
    @foreach ($taxes as $tax)
     <tr>
         <td style="text-align: left">{{ $tax->nombre_iva }}</td>
         <td style="text-align: left">{{ $tax->base }}</td>
         <td style="text-align: left">{{ $tax->impuestos }}</td>
     </tr>
     @endforeach
   </tbody>
   <tbody>
   </tbody>
</table>
<br>
<br>
<h3 style="text-align:center">IMPUESTO BOLSAS</h3>
<table class="table table-font">
    <thead>
       <tr>
           <th><strong>Impuestos</strong></th>
           <th><strong>Devoluciones</strong></th>
           <th><strong>Ventas</strong></th>
           <th><strong>Precio</strong></th>
           <th><strong>Sub_Total</strong></th>
           <th><strong>Tipo Doc</strong></th>
       </tr>
   </thead>
<tbody>
       @foreach ($bolsas as $bolsa)
           <tr>
               <td style="text-align: left">{{ $bolsa->nombre }}</td>
               <td style="text-align: left">{{ $bolsa->devoluciones }}</td>
               <td style="text-align: left">{{ $bolsa->ventas }}</td>
               <td style="text-align: left">{{ number_format($bolsa->precio, 2) }}</td>
               <td style="text-align: left">{{ $bolsa->ventas * $bolsa->precio }}</td>
               <td style="text-align: left">{{ $bolsa->tipo_doc }}</td>
           </tr>
       @endforeach
    <h3  style="text-align:center"><strong>IMPUESTO BOLSAS {{$impuestoBolsas}}</strong></h3>
   </tbody>
   <tbody>
   </tbody>
</table>
<br>
<br>
<h3 style="text-align:center">INFORMACION PARCIAL NOTAS CREDITO</h3>
<table class="table table-font">
    <thead>
       <tr>
           <th><strong>INFORMACION PARCIAL NOTAS CREDITO</strong></th>
           <th><strong>Total</strong></th>
           <th><strong>Impuesto</strong></th>
       </tr>
   </thead>
<tbody>
       @foreach ($notas as $nota)
           <tr>
               <td style="text-align: left">{{ $nota->nombre_iva }}</td>
               <td style="text-align: left">{{ $nota->base }}</td>
               <td style="text-align: left">{{ $nota->impuestos }}</td>
           </tr>
       @endforeach
   </tbody>
   <tbody>
   </tbody>
</table>
<br>
<br>
<h3 style="text-align:center">INFORMACION FORMAS DE PAGO</h3>
<table class="table table-font">
    <thead>
       <tr>
           <th><strong>Tipo de pago</strong></th>
           <th><strong>Total</strong></th>
       </tr>
   </thead>
<tbody>
       @foreach ($formas as $forma)
           <tr>
               <td style="text-align: left">{{ $forma->nombre }}</td>
               <td style="text-align: left">{{ $forma->valor }}</td>
           </tr>
       @endforeach
   </tbody>
   <tbody>
   </tbody>
</table>
<br>
</body>
</html>
