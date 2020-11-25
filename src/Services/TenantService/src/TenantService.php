<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src;

use ArtisanCloud\SaaSFramework\Services\ArtisanCloudService;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Contracts\TenantServiceContract;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\CreateTenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\ProcessTenantDatabase;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use Illuminate\Foundation\Bus\PendingDispatch;
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
    protected $connection = null;

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
     * Is database processing ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseProcessing(Tenant $tenant = null)
    {
        $currentTenant = $tenant ?? $this->m_model;

        return $currentTenant->status == Tenant::STATUS_IN_PROCESS;
    }

    /**
     * Is database processed ?
     *
     * @param Tenant $tenant
     *
     * @return bool
     */
    public function isDatabaseProcessed(Tenant $tenant = null)
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

        $arrayInfo['account'] = $arrayStr[4];
        $arrayInfo['password'] = Str::random(64);
        $arrayInfo['host'] = config('database.connections.tenant.host');
        $arrayInfo['port'] = config('database.connections.tenant.port');
        $arrayInfo['database'] = 'd' . $arrayStr[0] . Str::random(6);
        $arrayInfo['schema'] = config('database.connections.tenant.schema');

        $arrayInfo['uri'] = "postgresql://"
            . $arrayInfo['account'] . ":" . $arrayInfo['password']
            . "@" . $arrayInfo['host'] . ":" . $arrayInfo['port']
            . "/" . $arrayInfo['database'];

//        dd($arrayInfo);

        return $arrayInfo;
    }

    /**
     * Set tenant connection.
     * @param Tenant $tenant
     * @return bool
     */
    public function setConnection(Tenant $tenant): bool
    {
        $bResult = false;

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
//        dd(DB::connection('tenant'));
//        return DB::connection('tenant')->statement('CREATE DATABASE :database', ['database' => $databaseName]);
        //
//        Log::info('create tenant info',$tenant->toArray());
//        $bResult = DB::connection('tenant')->statement("CREATE DATABASE {$arrayInfo['database']}");

        return $bResult;
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


    /**
     * Dispatch Job for create tenant by.
     *
     * @param Org $org
     *
     * @return null|\Illuminate\Foundation\Bus\PendingDispatch
     */
    public static function dispatchCreateTenantBy(Org $org): PendingDispatch
    {
        return CreateTenant::dispatch($org)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');
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
        return ProcessTenantDatabase::dispatch($tenant)
            ->onConnection('redis-tenant')
            ->onQueue('tenant-database');
    }


}
