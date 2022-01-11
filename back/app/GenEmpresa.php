<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Tools;

class GenEmpresa extends Model
{

    private $caractLinea = 0;

    protected $table = 'gen_empresa';

    protected $fillable = [
    	'nombre',
    	'direccion',
    	'telefono',
    	'nit',
    	'razon_social',
    	'tipo_regimen',
    	'tercero_sucursal_pos_id',
    	'ruta_archivo_tiquetes_dibal',
    	'ruta_archivo_tx_dival',
    	'ruta_archivo_tiquetes_epelsa',
    	'ruta_archivo_precios_epelsa',
        'ruta_archivo_tiquetes_ishida',
        'ruta_archivo_txt_ishida',
    	'ruta_ip_marques',
    	'prod_lista_precios_id',
    	'gen_municipios_id',
    	'tipo_escaner',
    	'token_fac_elect',
    	'producto_bolsa_id',
    	'gen_iva_excluido_id',
    	'test_id_fe',
    	'licencia',
        'fact_grupo',
        'print_logo_pos',
        'email_backup_fact_elect',
        'cantidad_caracteres',
        'bloquear_tercero',
        'precio_bascula_marques'
    ];

    public function getDateFormat()
    {
        return Tools::dateTimeSql();
    }

    public function setCaractLinea($int){
        $this->caractLinea = $int;
    }

    public  function getCaractLinea($int){
        return $this->caractLinea;
    }

}
