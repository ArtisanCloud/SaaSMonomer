<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src;

use ArtisanCloud\SaaSFramework\Services\ArtisanCloudService;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Contracts\TenantServiceContract;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\CreateTenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\MigrateTenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\ProcessTenantDatabase;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\SeedTenantDemo;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\TenantModel;
use ArtisanCloud\UBT\Facades\UBT;
use Illuminate\Foundation\Bus\PendingDispatch;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class TenantService
 * @package ArtisanCloud\SaaSMonomer\Services\TenantService\src
 */
class TenantService extends ArtisanCloudService implements TenantServiceContract
{
    //

    //
    public function __construct()
    {
        parent::__construct();
        $this->m_model = new Tenant();
    }

    /**
     * make a model
     *
     * @param array $arrayData
     *
     * @return mixed
     */
    public function makeBy(array $arrayData)
    {
        $this->m_model = $this->m_model->make($arrayData);
        return $this->m_model;
    }

    /**
     * is database init ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseInit(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_INIT;
    }

    /**
     * Is database created ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseCreated(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_CREATED_DATABASE;
    }

    /**
     * Is database account created ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseAccountCreated(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_CREATED_ACCOUNT;
    }

    /**
     * Is database schema created ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseSchemaCreated(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_CREATED_SCHEMA;
    }

    /**
     * Is database migrated ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseMigrated(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_MIGRATED_DATABASE;
    }

    /**
     * Is database account seeded ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseDemoSeeded(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_SEEDED_DEMO;
    }

    /**
     * Is tenant normal ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isNormal(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_NORMAL;
    }


    /**
     * Get database ID by $uuid.
     *
     * @param string $name
     * @param string $uuid
     *
     * @return null|array
     */
    public function generateDatabaseAccessInfoBy(string $name, string $uuid): ?array
    {
        if (!Str::isUuid($uuid)) {
            return null;
        }

        $arrayStr = Str::of($uuid)->explode('-');

        $arrayInfo['subdomain'] = "{$name}." . env('DOMAIN_TENANT', 'productman.com');

        $arrayInfo['account'] = 'u' . $arrayStr[4];
        $arrayInfo['password'] = Str::random(64);
        $arrayInfo['host'] = config('database.connections.tenant.host');
        $arrayInfo['port'] = config('database.connections.tenant.port');
        $arrayInfo['database'] = Str::lower(
            'd'
            . Str::substr($arrayStr[1], 0, 2)
            . $arrayStr[0]
            . Str::substr($arrayStr[2], 0, 2)
        );
        $arrayInfo['schema'] = config('database.connections.tenant.schema');

        $arrayInfo['url'] = "postgresql://"
            . $arrayInfo['account'] . ":" . $arrayInfo['password']
            . "@" . $arrayInfo['host'] . ":" . $arrayInfo['port']
            . "/" . $arrayInfo['database'];

//        dd($arrayInfo);

        return $arrayInfo;
    }


    /**
     * Set tenant connection.
     *
     * @param Tenant $tenant
     *
     * @return void
     */
    public function setConnection(Tenant $tenant): void
    {
        Config::set("database.connections." . TenantModel::getConnectionNameStatic(), [
            'driver' => 'pgsql',
            'url' => $tenant->url,
            'host' => $tenant->host,
            'port' => $tenant->port,
            'database' => $tenant->database,
            'username' => $tenant->account,
            'password' => $tenant->password,
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
            'schema' => $tenant->schema,
            'search_path' => "tenant,public",
            'sslmode' => 'prefer',
        ]);
    }

    /**
     * Creates a new database role.
     * @param Tenant $tenant
     * @return bool
     */
    public function createDatabaseRole(Tenant $tenant): bool
    {
        $bResult = false;

        $conection = DB::connection(TenantModel::getConnectionNameStatic());
        $bResult = $conection->statement("CREATE ROLE {$tenant->account} WITH PASSWORD '{$tenant->password}' NOCREATEROLE NOCREATEUSER NOSUPERUSER NOCREATEDB;");

        return $bResult;
    }

    /**
     * Creates a new database.
     * @param Tenant $tenant
     * @return bool
     */
    public function createDatabase(Tenant $tenant): bool
    {
        $bResult = false;
//        dump(config('database.connections.tenant'));
//        dd(DB::connection(TenantModel::getConnectionNameStatic()));
        $conection = DB::connection('tenant-servers');
        $bResult = $conection->statement("CREATE DATABASE {$tenant->database};");

        return $bResult;
    }

    /**
     * Creates a new database.
     * @param Tenant $tenant
     * @return bool
     */
    public function createDatabaseAccount(Tenant $tenant): bool
    {
        $bResult = false;
//        dump(123, config('database.connections.tenant-servers'));
//        dd(321, DB::connection('tenant-servers'));
        $conection = DB::connection('tenant-servers');
        $bResult = $conection->statement("CREATE USER {$tenant->account} WITH PASSWORD '{$tenant->password}' NOCREATEROLE NOCREATEDB;");
        $bResult = $conection->statement("GRANT ALL PRIVILEGES ON DATABASE \"{$tenant->database}\" to {$tenant->account};");

        return $bResult;
    }

    /**
     * Creates a new schema.
     * @param string $schemaName
     * @return bool
     */
    public function createSchema(string $schemaName)
    {
        $bResult = false;
//        dump(config('database.connections.tenant'));
//        dd(DB::connection(TenantModel::getConnectionNameStatic()));
        $conection = DB::connection(TenantModel::getConnectionNameStatic());
        $bResult = $conection->statement("CREATE SCHEMA {$schemaName}");

        return $bResult;

    }


    /**
     * Migrate tables.
     *
     * @param Tenant $tenant
     * @param string $path
     *
     * @return int
     */
    public function migrateTenant(Tenant $tenant, string $path = 'database/migrations/tenants'): int
    {
//        dd(DB::connection(TenantModel::getConnectionNameStatic())->getDatabaseName());
        $result = Artisan::call('migrate', array('--database' => TenantModel::getConnectionNameStatic(), '--path' => $path));
//        dd(Artisan::output());
        return 1;
    }

    /**
     * Seed demo.
     *
     * @param string $schemaName
     * @param string $path
     *
     * @return int
     */
    public function seedDemo(Tenant $tenant, string $path = 'database/seeds/demo'): int
    {
        $result = Artisan::call('tenant:seed ' . $tenant->uuid);
        return $result;
    }


    /**
     * Dispatch Job for create tenant by.
     *
     * @param Org $org
     *
     * @return null|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchCreateTenantBy(Org $org): PendingDispatch
    {
        UBT::info(': Job ready to dispatch create tenant', ['orgName' => $org->name]);

        $dispatch = CreateTenant::dispatch($org)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');

        UBT::info('Job finish to dispatch create tenant', ['orgName' => $org->name]);

        return $dispatch;
    }

    /**
     * Dispatch Job for create tenant by.
     *
     * @param Tenant $tenant
     *
     * @return null|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchProcessTenantDatabase(Tenant $tenant): PendingDispatch
    {
        UBT::info('Job ready to dispatch process tenant database', ['tenantSubDomain' => $tenant->subdomain]);

        $dispatch = ProcessTenantDatabase::dispatch($tenant)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');

        UBT::info(': Job finish to dispatch process tenant database', ['tenantSubDomain' => $tenant->subdomain]);

        return $dispatch;
    }

    /**
     * Dispatch Job for migrate tenant table by.
     *
     * @param Tenant $tenant
     *
     * @return null|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchMigrateTenant(Tenant $tenant): PendingDispatch
    {
        UBT::info('Job ready to dispatch migrate tenant ', ['subdomain' => $tenant->subdomain]);

        $dispatch = MigrateTenant::dispatch($tenant)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');

        Log::info(': Job finish to dispatch migrate tenant ', ['subdomain' => $tenant->subdomain]);

        return $dispatch;

    }

    /**
     * Dispatch Job for seed demo by.
     *
     * @param Tenant $tenant
     *
     * @return null|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchSeedTenantDemo(Tenant $tenant): PendingDispatch
    {
        UBT::info(": Job Ready to seed tenant demo", ['subdomain' => $tenant->subdomain]);

        $dispatch = SeedTenantDemo::dispatch($tenant)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');

        UBT::info(': Job finish to dispatch seed tenant demo', ['subdomain' => $tenant->subdomain]);

        return $dispatch;
    }


}
