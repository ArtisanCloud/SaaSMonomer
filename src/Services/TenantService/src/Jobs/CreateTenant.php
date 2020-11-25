<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
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

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
                $arrayDBInfo = $tenantService->generateDatabaseAccessInfoBy(Tenant::TYPE_USER, $artisan->short_name, $org->uuid);
                $arrayDBInfo['org_uuid'] = $org->uuid;
                $tenant = $tenantService->createBy($arrayDBInfo);


            } catch (\Exception $e) {
//                dd($e);
                throw new BaseException(
                    intval($e->getCode()),
                    $e->getMessage()
                );
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
