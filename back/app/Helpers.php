<?php
use Carbon\Carbon;

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
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$body = substr($response, $header_size);

		curl_close($ch);

        return $body;
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

}
