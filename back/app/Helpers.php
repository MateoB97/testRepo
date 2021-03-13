<?php
use Carbon\Carbon;
use App\GenEmpresa;
use App\GenMunicipio;
use App\GenDepartamento;

if (! function_exists('current_user')) {

	function dateTimeSql(){
		// return 'Y-m-d H:i:s';
		return 'Y-d-m H:i:s.v';
	}

    function current_user()
    {
        return auth()->user();
    }

    function formato_fecha ($element) {
    	return Carbon::create(substr($element, 0, 19))->format('d-m-Y H:i');
    }

	function eliminar_acentos($cadena){

		//Reemplazamos la A y a
		$cadena = str_replace(
		array('Á', 'À', 'Â', 'Ä', 'á', 'à', 'ä', 'â', 'ª'),
		array('A', 'A', 'A', 'A', 'a', 'a', 'a', 'a', 'a'),
		$cadena
		);

		//Reemplazamos la E y e
		$cadena = str_replace(
		array('É', 'È', 'Ê', 'Ë', 'é', 'è', 'ë', 'ê'),
		array('E', 'E', 'E', 'E', 'e', 'e', 'e', 'e'),
		$cadena );

		//Reemplazamos la I y i
		$cadena = str_replace(
		array('Í', 'Ì', 'Ï', 'Î', 'í', 'ì', 'ï', 'î'),
		array('I', 'I', 'I', 'I', 'i', 'i', 'i', 'i'),
		$cadena );

		//Reemplazamos la O y o
		$cadena = str_replace(
		array('Ó', 'Ò', 'Ö', 'Ô', 'ó', 'ò', 'ö', 'ô'),
		array('O', 'O', 'O', 'O', 'o', 'o', 'o', 'o'),
		$cadena );

		//Reemplazamos la U y u
		$cadena = str_replace(
		array('Ú', 'Ù', 'Û', 'Ü', 'ú', 'ù', 'ü', 'û'),
		array('U', 'U', 'U', 'U', 'u', 'u', 'u', 'u'),
		$cadena );

		//Reemplazamos la N, n, C y c
		$cadena = str_replace(
		array('Ñ', 'ñ', 'Ç', 'ç'),
		array('N', 'n', 'C', 'c'),
		$cadena
		);

		return $cadena;
	}

	function caracteres_linea_pos () {

		return 48;
    }

    function caracteres_linea_posT80FontOne () {

		return 64;
	}

	function lineas_pos ($string, $carac_encabezado, $carac_linea) {

		$clienteSize = strlen($string) + $carac_encabezado;
		$numLineas = ceil($clienteSize / $carac_linea) * $carac_linea;

		return $numLineas;
	}

	function tercero_pos ($tercero, $carac_linea) {
		return str_pad("CLIENTE: ".eliminar_acentos($tercero), lineas_pos($tercero, 9,$carac_linea), " ", STR_PAD_RIGHT);
    }

    function salto_linea(){
        $string ='';
        $caracLinea = caracteres_linea_pos();
        return pad($string,$caracLinea, 0);
    }

	function pad ($text, $site, $charpad) {
        $caracLinea = caracteres_linea_posT80FontOne();
		if ($site == 0){
			return str_pad(strtoupper(substr($text,0, 63)), $caracLinea, $charpad, STR_PAD_BOTH);
		} elseif ($site == -1) {
			return str_pad(strtoupper(substr($text,0, 63)), $caracLinea, $charpad, STR_PAD_LEFT);
		} elseif ($site == 1) {
            return str_pad(strtoupper(substr($text,0, 63)), $caracLinea, $charpad, STR_PAD_RIGHT);
            // return str_pad(strtoupper(substr($text,0, 47)), $caracLinea, $charpad, STR_PAD_RIGHT);
		}
	}

	// TODO - revisar si sirve o sino borrar
    function http_post($url, $body){

        $ch = curl_init();

		$curlConfig = [
	        CURLOPT_URL            => $url,
	        CURLOPT_CUSTOMREQUEST  => "POST",
	        CURLOPT_RETURNTRANSFER => true,
	        CURLOPT_HTTPHEADER => TRUE,
	        CURLOPT_HTTPHEADER => array('Content-Type:application/json', 'Accept:application/json'),
	        CURLOPT_POSTFIELDS => json_encode($body)
	    ];

	    curl_setopt_array($ch, $curlConfig);

	    $response = curl_exec($ch);

		curl_close($ch);

        return $response;
    }

    function http_get($url){

        $ch = curl_init();

        $curlConfig = [
            CURLOPT_URL            => $url,
            CURLOPT_CUSTOMREQUEST  => "GET",
            CURLOPT_RETURNTRANSFER => true
        ];

        curl_setopt_array($ch, $curlConfig);

        $response = curl_exec($ch);

        $response = json_decode($response, true);
        curl_close($ch);

        return $response;
    }


    function posLineaCentro ($string, $relleno = ' ', $toUpper = true) {

		$caractLinea = GenEmpresa::getCaractLinea();

		$string = substr(eliminar_acentos($string), 0, $caractLinea);

		if ($toUpper) {
			return str_pad(strtoupper($string), $caractLinea, $relleno, STR_PAD_BOTH);
		} else {
			return str_pad($string, $caractLinea, $relleno, STR_PAD_BOTH);
		}
		
	}

	function posLineaDerecha ($string, $relleno = ' ', $toUpper = true) {

		$caractLinea = GenEmpresa::getCaractLinea();

		$string = substr(eliminar_acentos($string), 0, $caractLinea);

		if ($toUpper) {
			return str_pad(strtoupper($string), $caractLinea, $relleno, STR_PAD_RIGHT);
		} else {
			return str_pad($string, $caractLinea, $relleno, STR_PAD_RIGHT);
		}
		
	}

	function posLineaIzquierda ($string, $relleno = ' ', $toUpper = true) {

		$caractLinea = GenEmpresa::getCaractLinea();

		$string = substr(eliminar_acentos($string), 0, $caractLinea);

		if ($toUpper) {
			return str_pad(strtoupper($string), $caractLinea, $relleno, STR_PAD_LEFT);
		} else {
			return str_pad($string, $caractLinea, $relleno, STR_PAD_LEFT);
		}
		
	}

	function toNumber($number){
		return number_format($number, 0, ',', '.');
	}

	function posArrayCentro ($array, $relleno = ' ' , $toUpper = true) {

		$string = '';

		foreach ($array as $line) {
			$string .= posLineaCentro($line, $relleno, $toUpper);
		}

		return $string;
		
	}


	function posHeaderEmpresa(){

		$empresa = GenEmpresa::find(1);
		$municipio = GenMunicipio::find($empresa->gen_municipios_id);
        $departamento = GenDepartamento::find($municipio->departamento_id);

		$header = array(
			$empresa->razon_social,
			$empresa->nombre,
			'NIT: '.$empresa->nit,
			$empresa->tipo_regimen,
			$empresa->direccion,
			$municipio->nombre.' - '.$departamento->nombre,
			'TEL: '.$empresa->telefono
		);

		$string = posArrayCentro($header);

		return $string;
	}

	function posLineaBlanco () {

		$caractLinea = GenEmpresa::getCaractLinea();

		return str_pad("", $caractLinea, " ", STR_PAD_BOTH);
	}

	function posLineaGuion () {

		$caractLinea = GenEmpresa::getCaractLinea();

		return str_pad("", $caractLinea, "-", STR_PAD_BOTH);
	}

	function posLineaTresItems ($item1, $item2, $lengthItem3, $item3) {

		$item1 = eliminar_acentos($item1);
		$item2 = eliminar_acentos($item2);
		$item3 = eliminar_acentos($item3);

		$caractLinea = GenEmpresa::getCaractLinea();

		$lengthItem1 = strlen($item2) + $lengthItem3;

		//$lengthItem1 + strlen($item2) + $lengthItem3 must be equeal to $caractLinea

		$string = str_pad($item1, $caractLinea - $lengthItem1, " ", STR_PAD_RIGHT);
		$string .= $item2;
		$string .= str_pad($item3, $space2, " ", STR_PAD_LEFT);

		return substr($string, 0, $caractLinea);
	}


}