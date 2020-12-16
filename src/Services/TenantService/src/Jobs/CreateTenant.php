<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;
use ArtisanCloud\UBT\Facades\UBT;
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

    public bool $isStandalone;
    public Org $org;
    public TenantService $tenantService;

    /**
     * Create a new job instance.
     *
     * @param Org $org
     *
     * @return void
     */
    public function __construct(Org $org, bool $isStandalone = false)
    {
        //
        $this->org = $org;
        $this->isStandalone = $isStandalone;
        $this->org->loadMissing('creator');

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
        UBT::info('Job handle create tenant for org: ', [
            'mobile' => $this->org->creator->mobile,
            'orgName' => $this->org->name,
        ]);

        $this->org->load('tenant');
        if (!is_null($this->org->tenant)) {
            UBT::info(
                'Org had already created tenant, which tenant status is ' . $this->org->tenant->status,
                ['orgName' => $this->org->name]
            );
            return;
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

        if (!$this->isStandalone && $tenant) {
            TenantService::dispatchProcessTenantDatabase($tenant);
        }

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
