<?php

declare(strict_types=1);

return [
    'default' => env('ELASTIC_CONNECTION', 'default'),
    'connections' => [
        'default' => [
            'host' => env('ELASTIC_HOST', 'localhost:9200'),
            'basicAuthentication' => [
                env('ELASTIC_USERNAME'),
                env('ELASTIC_PASSWORD'),
            ],
            // configure HTTP client (Guzzle by default)
            'httpClientOptions' => [
                'timeout' => 2,
            ],
        ],
    ],
];
