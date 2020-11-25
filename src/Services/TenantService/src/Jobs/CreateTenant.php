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

class CreateTenant implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Org $org;

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
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $tenant = \DB::connection('pgsql')->transaction(function () {
            try {
                // create a tenant for org
                $arrayDBInfo = $tenantService->generateDatabaseAccessInfoBy(Tenant::TYPE_USER, $this->org->shorName, $this->org->uuid);
                $arrayDBInfo['org_uuid'] = $org->uuid;
                $tenant = $tenantService->createBy($arrayDBInfo);


            } catch (\Throwable $e) {
//                dd($e);
                report($e);
            }

            return $tenant;

        });


        if($tenant){
            TenantService::dispatchProcessTenantDatabase();
        }
    }

    /**
     * Handle a job failure.
     *
     * @param  \Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        Log::error('create tenant error: '.$exception->getMessage());
    }

}
