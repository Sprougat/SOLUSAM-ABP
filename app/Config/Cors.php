<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Cors extends BaseConfig
{
    

    public array $default = [
        

        'allowedOrigins' => ['*'],

        

        'allowedOriginsPatterns' => [],

        

        'supportsCredentials' => false,

        

        'allowedHeaders' => ['*'],

        

        'exposedHeaders' => [],

        

        'allowedMethods' => [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'OPTIONS',
        ],

        

        'maxAge' => 7200,
    ];
}
