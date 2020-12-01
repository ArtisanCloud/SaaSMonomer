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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Throwable;

class MigrateTenant implements ShouldQueue
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

        //
        $bResult = false;
        Log::info($this->tenant->org->name . ": Job migrate Tenant table:{$this->tenant->uuid}");

        try {
            if ($this->tenantService->isDatabaseSchemaCreated()) {

                // seed tenant demo
                $bResult = $this->tenantService->migrateTenant($this->tenant);
                if ($bResult) {
                    Log::info($this->tenant->org->name . ": Job succeed to migrate database");

                    // save tenant status
                    $this->tenant->status = Tenant::STATUS_MIGRATED_DATABASE;
                    $bResult = $this->tenant->save();

                } else {
                    throw new \Exception($this->tenant->org->name . ": failed to migrate table, please email amdin");
                }

            } else {
                Log::warning($this->tenant->org->name . ": Job User tenant schema not created");
            }

        } catch (Throwable $e) {
//                dd($e);
            Log::alert($this->tenant->org->name . ": Job " . $e->getMessage());
            $bResult = false;
            report($e);
        }

        if ($bResult) {

            Log::info($this->tenant->org->name . ": Job Ready to dispatch seed tenant demo");
            TenantService::dispatchSeedTenantDemo($this->tenant);
            Log::info($this->tenant->org->name . ": Job finish to dispatch seed tenant demo");
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
        Log::error($this->tenant->org->name . ": Job migrate tenant database error: " . $exception->getMessage());
    }

}
