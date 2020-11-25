<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
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

class ProcessTenantDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Tenant $tenant;
    protected TenantService $tenantService;
    protected OrgService $orgService;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Tenant $tenant)
    {
        //
        $this->tenant = $tenant;

        $this->tenantService = new TenantService();
        $this->tenantService->setModel($this->tenant);


        $this->orgService = new OrgService();
        $this->tenant->loadMissing('org');
        $org = $this->tenant->org;
//        dd($org);
        $this->$orgService->setModel($org);

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::info("Process User:{$this->userService->getModel()->name} Tenant database:{$this->tenant->uuid}" );

        try {
            if ($this->userService->isUserInit($this->user)) {

                // create user database
                $bResult = $this->tenantService->createDatabase($arrayDBInfo);
                if (!$bResult) {
                    Log::alert("User: {$this->user->mobile}  failed to create database, please email amdin");
                } else {
                    Log::info("User: {$this->user->mobile}  succeed to create database, user will received a email to login");
                }

            } else {
                Log::warning('User is not init or user has create a tenant database');
            }

        } catch (\Exception $e) {
//                dd($e);
            throw new BaseException(
                intval($e->getCode()),
                $e->getMessage()
            );
        }

        return $user;

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
        Log::error('process tenant database error: '.$exception->getMessage());
    }

}
