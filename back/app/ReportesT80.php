<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Mike42\Escpos\EscposImage;

class ReportesT80 extends Model
{

	private $caractLinea;

	public function __construct () {

        $empresa = GenEmpresa::find(1);
		$this->caractLinea = $empresa->cantidad_caracteres;

	}

	public function limitAndCleanStringLinea ($string, $limit= '') {

		if ($limit == '') {
			$limit = $this->caractLinea;
		}

		$string = substr(eliminar_acentos($string), 0, $limit);

		return $string;

	}

    public function posLineaCentro ($string, $relleno = ' ', $toUpper = true) {

		$string = $this->limitAndCleanStringLinea($string);

		if ($toUpper) {
			return str_pad(strtoupper($string), $this->caractLinea, $relleno, STR_PAD_BOTH);
		} else {
			return str_pad($string, $this->caractLinea, $relleno, STR_PAD_BOTH);
		}

	}

	public function posLineaDerecha ($string, $relleno = ' ', $toUpper = true) {

		$string = $this->limitAndCleanStringLinea($string);

		if ($toUpper) {
			return str_pad(strtoupper($string), $this->caractLinea, $relleno, STR_PAD_RIGHT);
		} else {
			return str_pad($string, $this->caractLinea, $relleno, STR_PAD_RIGHT);
		}

	}

	public function posLineaIzquierda ($string, $relleno = ' ', $toUpper = true) {

		$string = $this->limitAndCleanStringLinea($string);

		if ($toUpper) {
			return str_pad(strtoupper($string), $this->caractLinea, $relleno, STR_PAD_LEFT);
		} else {
			return str_pad($string, $this->caractLinea, $relleno, STR_PAD_LEFT);
		}

	}

	public function toNumber($number){
		return number_format($number, 0, ',', '.');
	}

	public function posArrayCentro ($array, $relleno = ' ' , $toUpper = true) {

		$string = '';

		foreach ($array as $line) {
			$string .= $this->posLineaCentro($line, $relleno, $toUpper);
		}

		return $string;

	}

    public function posFooterSgc(){
        $footer = array(
            'Impreso desde SGC de Byteco S.A.S.',
            'Nit: 901389565-8',
            'Fecha Impresion: '. date('Y-m-d H:i:s')
        );
        $string = $this->posArrayCentro($footer);
		return $string;
    }

	public function posHeaderEmpresa(){

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

		$string = $this->posArrayCentro($header);

		return $string;
	}

	public function posLineaBlanco () {

		return str_pad("", $this->caractLinea, ' ', STR_PAD_BOTH);
	}

	public function posLineaGuion () {

		return str_pad("", $this->caractLinea, "-", STR_PAD_BOTH);
	}

	public function posLineaTresItems ($item1, $item2, $lengthItem3, $item3) {

		$item1 = eliminar_acentos($item1);
		$item2 = eliminar_acentos($item2);
		$item3 = eliminar_acentos($item3);

		$lengthItem1 = strlen($item2) + $lengthItem3;

		//$lengthItem1 + strlen($item2) + $lengthItem3 must be equeal to $caractLinea

		$string = str_pad($item1, $this->caractLinea - $lengthItem1, " ", STR_PAD_RIGHT);
		$string .= $item2;
		$string .= str_pad($item3, $space2, " ", STR_PAD_LEFT);

		return substr($string, 0, $this->caractLinea);
	}

	public function posDosItemsExtremos ($item1, $item2, $fill = ' ') {

		$item1 = eliminar_acentos($item1);
		$item2 = eliminar_acentos($item2);

		$fillBetween = $this->caractLinea - strlen($item2);

		$string = str_pad($item1, $fillBetween, $fill, STR_PAD_RIGHT);
		$string .= $item2;

		return $this->limitAndCleanStringLinea($string);
	}

	public function multiItemsFromArray($data){

		$stringLine = '';
		$totalChars = 0;
        $string = '';
		foreach ($data as $item) {
			$totalChars += $item[1];
		}

		if ($totalChars > $this->caractLinea) {
			return 'Error: Caracteres sobrepasan el tamaño de la linea '.json_encode($data);
		}

		// string, size, fill, side-fill
		foreach ($data as $item) {

			$totalChars += intval($item[1]);

			if ($item[3] < 0) {

				$side = STR_PAD_LEFT;

			} elseif ($item[3] == 0) {

				$side = STR_PAD_BOTH;

			} else {

				$side = STR_PAD_RIGHT;

			}

			if ($item[1] == 0) {
				$item[1] = $this->caractLinea - $totalChars;
			}

			$string = $this->limitAndCleanStringLinea($item[0], $item[1]);

			$stringLine .= str_pad($string, $item[1], $item[2], $side);

		}


		return $stringLine;

	}

    public function printLogoT80($printer){
        $empresa = GenEmpresa::find(1);
        if ($empresa->print_logo_pos) {
            $img = EscposImage::load("../public/images/logo1.png");
            return $printer->graphics($img);
        }
    }

    public static function divString($str, $num_partes) {
		$slong = strlen($str);
		if(!intval($num_partes) <= 0){
			$long_partes = intval($slong/$num_partes);
		} else {
			$long_partes = 1;
			$num_partes = 1;
		}
		$sobrante = $slong % $num_partes;
		$i = 0;
		$start = 0;
		$arr2 = array();
		while($i < $num_partes) {
			if($i < $slong) {
				$offset = ($sobrante > 0) ? $long_partes+1 : $long_partes;
				$arr2[] = substr($str, $start, $offset);
				$start += $offset;
				$sobrante--;
			} else {
				$arr2[] = '';
			}
			$i++;
		}
		return $arr2;
	}


}
