<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tools extends Model
{
    public static function dateTimeSql(){
		// return 'Y-m-d H:i:s.v';
		return 'Y-d-m H:i:s.v';
	}

    public static function  http_get($url){

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

    public static function http_post($url, $body){

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
}
