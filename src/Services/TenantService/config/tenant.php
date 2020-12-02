<?php
declare(strict_types=1);

const TENANT_CLIENT_ID = "9148a614-cd54-4de3-9964-2c032a5f16fd";
const TENANT_CLIENT_SECRET = "xowgjUJ059ebcC6aTIn3in67KqQ9mSUgD2WKHVwp";

const TENANT_CLIENT_PASSWORD_ID = "916efb77-d549-4b2a-9fd7-b345933ae473";
const TENANT_CLIENT_PASSWORD_SECRET = "tgJWfBUDJuEr9o0O0cyJRLUugpu2bCUgXLQrm7ZM";


const API_ERR_CODE_TENANT_NOT_EXIST = 44300002;


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
    ],
    'migrations' => [
        'path' => 'app/database/migrations/tenants'
    ]
];
