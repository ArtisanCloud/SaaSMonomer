<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Console\Commands\Tenant;

use ArtisanCloud\SaaSMonomer\Services\TenantService\src\Models\Tenant;
use ArtisanCloud\SaaSMonomer\Services\TenantService\src\TenantService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;


class SeedDemo extends Command
{

    protected Tenant $tenant;
    protected TenantService $tenantService;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:seed {tenant} {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tenant:seed';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->tenantService = resolve(TenantService::class);

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

//        Artisan::call('passport:install');
        $this->info('command tenant seed database');

        $tenant = TenantService::GetBy([
            'uuid' => $this->argument('tenant'),
        ]);

        // check if tenant is valid
        if (is_null($tenant)) {
            $message = 'tenant is not valid';
            $this->error($message);
            Log::error($message);
            return -1;
        }


        // check if tenant is migrated or not
        if (!$this->tenantService->isDatabaseMigrated($tenant)) {
            $message = 'tenant status is not migrated';
            $this->error($message);
            Log::error($message);
            return -1;
        }

        $this->info($tenant->database);
        Log::info($tenant->database);

        $para = compact('tenant');
        $bResult = $this->call(\DemoSeeder::class, $para);

        return 1;
    }


}
