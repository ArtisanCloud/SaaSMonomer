<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Console\Commands\Tenant;

use App\Services\UserService\UserService;
use ArtisanCloud\SaaSMonomer\Services\OrgService\Models\Org;
use ArtisanCloud\SaaSMonomer\Services\OrgService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\OrgService\OrgService;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class CreateOrg extends Command
{

    protected Org $org;
    protected OrgService $orgService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'org:create {userUuid} {orgName} {orgShortName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'org:create';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->orgService = resolve(OrgService::class);

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
//        dd($this->arguments());
//        dd($this->options());

        $user = UserService::GetBy([
            'uuid' => $this->argument('userUuid'),
        ]);

        // check if $user is valid
        if (is_null($user)) {
            $message = 'user is not valid';
            $this->error($message);
            Log::error($message);
            return -1;
        }

        $orgName = $this->argument('orgName');
        if (!$orgName) {
            $message = 'org name  is required';
            $this->error($message);
            Log::error($message);
            return -1;
        }

        $shortName = $this->argument('orgShortName');
        if (!$shortName) {
            $message = 'org short name required';
            $this->error($message);
            Log::error($message);
            return -1;
        }


        OrgService::dispatchCreateOrgBy($user, $orgName, $shortName);

        return 1;
    }


}
