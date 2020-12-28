<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use App\Services\UserService\UserService;

use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\TenantModel;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;
use ArtisanCloud\UBT\Facades\UBT;
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

    public bool $isStandalone;
    public Tenant $tenant;
    protected TenantService $tenantService;

    /**
     * Create a new job instance.
     *
     * @param Tenant $tenant
     *
     * @return void
     */
    public function __construct(Tenant $tenant, bool $isStandalone = false)
    {
        //init tenant
        $this->tenant = $tenant;

        // load tenant service
        $this->isStandalone = $isStandalone;
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
        UBT::info(" Job migrate Tenant table:{$this->tenant->uuid}", ['orgName' => $this->tenant->org->name]);

        $tenant = \DB::connection(TenantModel::getConnectionNameStatic())->transaction(function () {

            try {
                if ($this->tenantService->isDatabaseSchemaCreated()) {

                    // seed tenant demo
                    $bResult = $this->tenantService->migrateTenant($this->tenant);
                    if ($bResult) {
                        UBT::info("Job succeed to migrate database", ['orgName' => $this->tenant->org->name]);

                        // save tenant status
                        $this->tenant->status = Tenant::STATUS_MIGRATED_DATABASE;
                        $bResult = $this->tenant->save();

                    } else {
                        throw new \Exception($this->tenant->org->name . ": failed to migrate table, please email amdin");
                    }

                } else {
                    UBT::warning("Job User tenant schema not created", ['orgName' => $this->tenant->org->name]);
                }

            } catch (Throwable $e) {
//                dd($e);
                UBT::alert("Job " . $e->getMessage(), ['orgName' => $this->tenant->org->name]);
                $bResult = false;
                report($e);
            }
        });


        if (!$this->isStandalone && $bResult) {
            TenantService::dispatchSeedTenantDemo($this->tenant);
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
        UBT::sendError($exception);
    }

}
