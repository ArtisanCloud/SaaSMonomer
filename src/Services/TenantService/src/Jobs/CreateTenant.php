<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
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

class CreateTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Org $org;
    protected TenantService $tenantService;

    /**
     * Create a new job instance.
     *
     * @param Org $org
     *
     * @return void
     */
    public function __construct(Org $org)
    {
        //
        $this->org = $org;

        $this->tenantService = resolve(TenantService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::info('Job handle create tenant for org: ' . $this->org->name);

        $this->org->load('tenant');
        if (!is_null($this->org->tenant)) {
            Log::info(
                'Org had already created tenant for org: ' . $this->org->name
                . ', which tenant status is ' . $this->org->tenant->status
            );
            return false;
        }

        $tenant = \DB::connection()->transaction(function () {
            $tenant = null;
            try {
                // create a tenant for org
                $arrayDBInfo = $this->tenantService->generateDatabaseAccessInfoBy($this->org->short_name, $this->org->uuid);
                $arrayDBInfo['org_uuid'] = $this->org->uuid;
                $tenant = $this->tenantService->createBy($arrayDBInfo);

            } catch (Throwable $e) {
//                dd($e);
                report($e);
            }

            return $tenant;

        });
//        dd($tenant);

        if ($tenant) {
            TenantService::dispatchProcessTenantDatabase($tenant);
        }

        return true;
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
        Log::error('create tenant error: ' . $exception->getMessage());
    }

}
