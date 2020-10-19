<?php
declare(strict_types=1);


return [
    'default' => env('DB_CONNECTION', 'pgsql'),

    'connections' => [
        'pgsql' => [
            'driver' => 'pgsql',
            'url' => '',
            'host' => '',
            'port' => '',
            'database' => '',
            'username' => '',
            'password' => '',
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => '',
            'sslmode' => 'prefer',
        ]
    ]
];
