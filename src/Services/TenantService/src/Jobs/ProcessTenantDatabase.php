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

class ProcessTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Tenant $tenant;
    protected TenantService $tenantService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        //
        $this->tenant = $tenant;

        $this->tenantService = resolve(TenantService::class);
        $this->tenantService->setModel($this->tenant);

        $this->tenant->loadMissing('org');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::info("Job process Org:{$this->tenant->org->name} Tenant database:{$this->tenant->uuid}");

        try {
            if ($this->tenantService->isDatabaseInit()) {

                // create user database
                $bResult = $this->tenantService->createDatabase($this->tenant);
                if (!$bResult) {
                    Log::alert("Org Name: {$this->tenant->org->name}  failed to create database, please email amdin");
                } else {
                    Log::info("Org Name: {$this->tenant->org->name}  succeed to create database, user will received a email to login");
                }

            } else {
                Log::warning('User is not init or user has create a tenant database');
            }

        } catch (Throwable $e) {
//                dd($e);
            report($e);
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
        Log::error('process tenant database error: ' . $exception->getMessage());
    }

}
