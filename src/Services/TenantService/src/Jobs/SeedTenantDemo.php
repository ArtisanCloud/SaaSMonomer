<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use App\Services\UserService\UserService;

use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

use Throwable;

class SeedTenantDemo implements ShouldQueue
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

        //
        $bResult = false;
        Log::info("Job seed Org:{$this->tenant->org->name} Tenant Demo:{$this->tenant->uuid}");

        try {
            if ($this->tenantService->isDatabaseAccountCreated()) {

                // seed tenant demo
                $bResult = $this->tenantService->seedDemo($this->tenant);
                if ($bResult) {
                    Log::info("Org Name: {$this->tenant->org->name}  succeed to create database");
                } else {
                    throw new \Exception('"Org Name: {$this->tenant->org->name}  failed to create database, please email amdin"');
                }

            } else {
                Log::warning('User is not init or user has create a tenant database');
            }

        } catch (Throwable $e) {
//                dd($e);
            Log::alert($e->getMessage());
            report($e);
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
        Log::error('seed tenant demo error: ' . $exception->getMessage());
    }

}
