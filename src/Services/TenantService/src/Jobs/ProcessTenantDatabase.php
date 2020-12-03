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
use Illuminate\Support\Facades\DB;
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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /**
         * setup current tenant connection with current session
         * cannot set connection in construction
         */
        $this->tenantService->setConnection($this->tenant);

        $bResult = false;
        Log::info($this->tenant->org->name . ": Job process Tenant database:{$this->tenant->uuid}");

        try {
            // create tenant database
            if ($this->tenantService->isDatabaseInit($this->tenant)) {

                $bResult = $this->tenantService->createDatabase($this->tenant);
                if ($bResult) {
                    Log::info($this->tenant->org->name . ": Job succeed to create database");

                    // save tenant status
                    $this->tenant->status = Tenant::STATUS_CREATED_DATABASE;
                    $bResult = $this->tenant->save();

                } else {
                    throw new \Exception($this->tenant->org->name . ": Job failed to create database, please email amdin");
                }

            } else {
                Log::warning($this->tenant->org->name . ": Job User is not init or user has create a tenant database");
            }

            $bResult = false;
            // create tenant database account
            if ($this->tenantService->isDatabaseCreated($this->tenant)) {
                $bResult = $this->tenantService->createDatabaseAccount($this->tenant);
                if ($bResult) {
                    Log::info($this->tenant->org->name . ": Job succeed to create database account");

                    // save tenant status
                    $this->tenant->status = Tenant::STATUS_CREATED_ACCOUNT;
                    $bResult = $this->tenant->save();

                } else {
                    throw new \Exception($this->tenant->org->name . ": Job failed to create database account, please email amdin");
                }
            } else {
                Log::warning($this->tenant->org->name . ": Job Tenant database is not created");
            }

            $bResult = false;
            // create tenant schema
            if ($this->tenantService->isDatabaseAccountCreated($this->tenant)) {

                $bResult = $this->tenantService->createSchema($this->tenant->schema);
                if ($bResult) {
                    Log::info($this->tenant->org->name . ": Job succeed to create schema");

                    // save tenant status
                    $this->tenant->status = Tenant::STATUS_CREATED_SCHEMA;
                    $bResult = $this->tenant->save();

                } else {
                    throw new \Exception($this->tenant->org->name . ": Job failed to create schema, please email amdin");
                }
            } else {
                Log::warning($this->tenant->org->name . ": Job Tenant account is not created");
            }


        } catch (Throwable $e) {
//                dd($e);
            Log::alert($e->getMessage());
            $bResult = false;
            report($e);
        }

        if ($bResult) {
            
            TenantService::dispatchMigrateTenant($this->tenant);
        }

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
        Log::error($this->tenant->org->name . ": Job process tenant database error: " . $exception->getMessage());
    }

}
