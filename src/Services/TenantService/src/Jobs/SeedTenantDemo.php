<?php

namespace ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs;

use App\Models\User;
use App\Services\UserService\UserService;

use ArtisanCloud\SaaSFramework\Notifications\ArtisanRegistered;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
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
        UBT::info("Seed Tenant Demo", [
            'orgName' => $this->tenant->org->name,
            'tenantUuid' => $this->tenant->uuid,
        ]);

        try {
            if ($this->tenantService->isDatabaseMigrated()) {

                // seed tenant demo
                $bResult = $this->tenantService->seedDemo($this->tenant);
                if (!$bResult) {
                    throw new \Exception('"Org Name: {$this->tenant->org->name}  failed to seed demo, please email amdin"');
                }
                UBT::info("succeed to seed demo", ['orgName' => $this->tenant->org->name]);

                // update user status
                $user = $this->tenant->org->creator;
                $user->status = User::STATUS_NORMAL;
                $user->save();

            } else {
                UBT::warning("Job User tenant is not migrated", ['orgName' => $this->tenant->org->name]);
            }

        } catch (Throwable $e) {
//                dd($e);
            UBT::alert($e->getMessage());
            report($e);
        }

        if ($user) {
            $notifictaion = (new ArtisanRegistered())
                ->onConnection('redis-mail')
                ->onQueue('mailer');
//        dd($notifictaion);
            $user->notify($notifictaion);

            UBT::info("user will received a email to login");
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
