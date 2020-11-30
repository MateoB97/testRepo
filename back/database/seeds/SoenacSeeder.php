<?php

use Illuminate\Database\Seeder;
use  App\SoenacResposabilidad;


class SoenacSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $regimenes = [
        	[
	          'id'=> 1,
	          'name'=> 'Impuesto sobre las ventas – IVA',
	          'code'=> '48'
	        ],
	        [
	          'id'=> 2,
	          'name'=> 'No responsable de IVA',
	          'code'=> '49'
        	]
        ];

        foreach ($regimenes as $item) {
        	App\SoenacRegimen::create([
                'nombre' => $item['name'],
                'soenac_id'  => $item['id'],
                'codigo'  => $item['code']
            ]);
        }

        $responsabilidades = [
	        [
	          'id'=> 5,
	          'name'=> 'Gran contribuyente',
	          'code'=> 'O-13'
	        ],
	        [
	          'id'=> 7,
	          'name'=> 'Autorretenedor',
	          'code'=> 'O-15'
	        ],
	        [
	          'id'=> 12,
	          'name'=> 'Agente de retención IVA',
	          'code'=> 'O-23'
	        ],
	        [
	          'id'=> 20,
	          'name'=> 'Régimen simple de tributación',
	          'code'=> 'O-47'
	        ],
	        [
	          'id'=> 29,
	          'name'=> 'No responsable',
	          'code'=> 'R-99-PN'
	        ]
	    ];

	    foreach ($responsabilidades as $item) {
        	App\SoenacResponsabilidad::create([
                'nombre' => $item['name'],
                'soenac_id'  => $item['id'],
                'codigo'  => $item['code']
            ]);
        }

        $tiposDoc = [
	        [
	            "id"=> 1,
	            "name"=> "Registro civil",
	            "code"=> "11"
	        ],
	        [
	            "id"=> 2,
	            "name"=> "Tarjeta de identidad",
	            "code"=> "12"
	        ],
	        [
	            "id"=> 3,
	            "name"=> "Cédula de ciudadanía",
	            "code"=> "13"
	        ],
	        [
	            "id"=> 4,
	            "name"=> "Tarjeta de extranjería",
	            "code"=> "21"
	        ],
	        [
	            "id"=> 5,
	            "name"=> "Cédula de extranjería",
	            "code"=> "22"
	        ],
	        [
	            "id"=> 6,
	            "name"=> "NIT",
	            "code"=> "31"
	        ],
	        [
	            "id"=> 7,
	            "name"=> "Pasaporte",
	            "code"=> "41"
	        ],
	        [
	            "id"=> 8,
	            "name"=> "Documento de identificación extranjero",
	            "code"=> "42"
	        ],
	        [
	            "id"=> 9,
	            "name"=> "NIT de otro país",
	            "code"=> "50"
	        ],
	        [
	            "id"=> 10,
	            "name"=> "NUIP *",
	            "code"=> "91"
	        ]
	    ];

	    foreach ($tiposDoc as $item) {
        	App\SoenacTipoDocumento::create([
                'nombre' => $item['name'],
                'soenac_id'  => $item['id'],
                'codigo'  => $item['code']
            ]);
        }

        $tiposOrganizacion = [
	        [
	            "id"=> 1,
	            "name"=> "Persona Jurídica y asimiladas",
	            "code"=> "1"
	        ],
	        [
	            "id"=> 2,
	            "name"=> "Persona Natural y asimiladas",
	            "code"=> "2"
	        ]
	    ];

	    foreach ($tiposOrganizacion as $item) {
        	App\SoenacTipoOrg::create([
                'nombre' => $item['name'],
                'soenac_id'  => $item['id'],
                'codigo'  => $item['code']
            ]);
        }

    }
}
