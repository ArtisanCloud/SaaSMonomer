<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use App\Services\UserService\UserService;

use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\TenantModel;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Throwable;

class ProcessTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Tenant $tenant;
    protected TenantService $tenantService;

    /**
     * Create a new job instance.
     *
     * @param Tenant $tenant
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        //init tenant
        $this->tenant = $tenant;

        // load tenant service
        $this->tenantService = resolve(TenantService::class);
        $this->tenantService->setModel($this->tenant);

        // load tenant's org
        $this->tenant->loadMissing('org');

        // setup current tenant connection
        $this->tenantService->setConnection($tenant);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $bResult = false;
        Log::info("Job process Org:{$this->tenant->org->name} Tenant database:{$this->tenant->uuid}");

        $tenant = \DB::connection(TenantModel::getConnectionNameStatic())->transaction(function () {
            try {
                if ($this->tenantService->isDatabaseInit()) {

                    // create tenant database
                    $bResult = $this->tenantService->createDatabase($this->tenant);
                    if ($bResult) {
                        Log::info("Org Name: {$this->tenant->org->name}  succeed to create database");
                        $tenant->status = Tenant::STATUS_CREATED_DATABASE;
                    } else {
                        throw new \Exception('"Org Name: {$this->tenant->org->name}  failed to create database, please email amdin"');
                    }


                    // create tenant database account
                    $bResult = $this->tenantService->createDatabaseAccount($this->tenant);
                    if ($bResult) {
                        Log::info("Org Name: {$this->tenant->org->name}  succeed to create database account");
                        $tenant->status = Tenant::STATUS_CREATED_ACCOUNT;
                    } else {
                        throw new \Exception("Org Name: {$this->tenant->org->name}  failed to create database account, please email amdin");
                    }

                    // save tenant status
                    $bResult = $tenant->save();

                } else {
                    Log::warning('User is not init or user has create a tenant database');
                }

            } catch (Throwable $e) {
//                dd($e);
                Log::alert($e->getMessage());
                report($e);
            }
        });
        if ($bResult) {

        }

        Log::info("user will received a email to login");

        return $bResult;

    }

    /**
     * Handle a job failure.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        Log::error('process tenant database error: ' . $exception->getMessage());
    }

}
