<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src;

use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Contracts\TenantServiceContract;
use Illuminate\Support\Facades\DB;

/**
 * Class TenantService
 * @package ArtisanCloud\SaaSMonomer\Services\TenantService\src
 */
class TenantService implements TenantServiceContract
{
    //

    /**
     * Creates a new database.
     * @param string $databaseName
     * @return bool
     */
    public function createDatabase($databaseName)
    {
        dd(DB::connection('tenant'));
        return DB::connection('tenant')->statement('CREATE DATABASE :database', array('database' => $databaseName));
    }

    /**
     * Creates a new schema.
     * @param string $schemaName
     * @return bool
     */
    public function createSchema($schemaName)
    {
        return DB::connection()->statement('CREATE SCHEMA :schema', array('schema' => $schemaName));
    }

    /**
     * Creates a new schema.
     * @param string $schemaName
     * @return bool
     */
    public function migrateTenant(string $databaseConnection, string $path = 'app/database/migrations/tenants')
    {
        Artisan::call('migrate', array('database' => $databaseConnection, 'path' => $path));
        Artisan::call('db:seed', array('database' => $databaseConnection, 'path' => $path));
    }


}
