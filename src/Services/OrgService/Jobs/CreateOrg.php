<?php

namespace ArtisanCloud\SaaSMonomer\Services\OrgService\Jobs;

use App\Models\User;
use ArtisanCloud\SaaSFramework\Exceptions\BaseException;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\CreateTenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Jobs\ProcessTenantDatabase;
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

class CreateOrg implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public User $user;
    public string $orgName;
    public string $shortName;
    protected OrgService $orgService;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param string $orgName
     *
     * @return void
     */
    public function __construct(User $user, string $orgName, string $shortName)
    {
        //
        $this->user = $user;
        $this->orgName = $orgName;
        $this->shortName = $shortName;
        $this->orgService = resolve(OrgService::class);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        Log::info('Job handle create org for user: ' . $this->user->mobile);
        
        $org = \DB::connection('pgsql')->transaction(function () {
            try {
                // create org for user
                $orgService =
                $org = $this->orgService->createBy([
                    'user_uuid' => $this->user->uuid,
                    'name' => $this->orgName,
                    'short_name' => $this->shortName,
                ]);
//                dd($org);

            } catch (Throwable $e) {
//                dd($e);
                report($e);
            }
            return $org;

        });

        if ($org) {
            // to create user org
            TenantService::dispatchCreateTenantBy($org);

        }


    }

    /**
     * Handle a job failure.
     *
     * @param  Throwable  $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
        Log::error('create org error: '.$exception->getMessage());
    }

}
