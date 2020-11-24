<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src;

use ArtisanCloud\SaaSFramework\Services\ArtisanCloudService;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Contracts\TenantServiceContract;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use Illuminate\Support\Facades\DB;
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
        $this->m_model = $this->m_model->firstOrNew(
            [
                'tenantable_uuid' => $arrayData['tenantable_uuid'],
                'type' => $arrayData['type'],
            ],
            $arrayData
        );
        return $this->m_model;
    }


    /**
     * Get database ID by $uuid.
     * @param int $type
     * @param string $name
     * @param string $uuid
     *
     * @return null|array
     */
    public function generateDatabaseAccessInfoBy(int $type,string $name,string $uuid): ?array
    {
        if (!Str::isUuid($uuid)) {
            return null;
        }

        $arrayStr = Str::of($uuid)->explode('-');

        $arrayInfo['subdomain'] = "{$name}.".env('DOMAIN_TENANT', 'productman.com');

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
     * Creates a new database.
     * @param array $arrayInfo
     * @return bool
     */
    public function createDatabase(array $arrayInfo): bool
    {
//        dd(DB::connection('tenant'));
//        return DB::connection('tenant')->statement('CREATE DATABASE :database', ['database' => $databaseName]);
        //
        $bResult = DB::connection('tenant')->statement("CREATE DATABASE {$arrayInfo['database']}");

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


}
