<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Console\Commands\Tenant;

use App\Models\User;
use App\Services\UserService\UserService;
use ArtisanCloud\SaaSPolymer\Events\UserRegistered;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class Init extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:init {user} {--Q|queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tenant:init';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $args = $this->arguments();
//        dd($this->arguments());
//        dd($this->options());


        // input a valid uuid
        if (!Str::isUUID($args['user'])) {
            $this->error('please enter user\'s uuid');
            return -1;
        }

        // get user
        $user = UserService::GetBy([
            'uuid' => $args['user']
        ]);
        $userService = new UserService();

        if (is_null($user) || !$userService->isUserInit($user)
        ) {
            $this->error('please input a init user');
            return -1;
        }

        $this->info('command tenant init database');
        event(new UserRegistered($user));

        return 0;
    }


}
