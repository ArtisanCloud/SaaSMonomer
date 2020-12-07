<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Console\Commands\Tenant;

use App\Models\User;
use ArtisanCloud\SaaSFramework\Notifications\ArtisanRegistered;
use App\Services\UserService\UserService;
use ArtisanCloud\SaaSPolymer\Events\UserRegistered;

use ArtisanCloud\UBT\Facades\UBT;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;


class Init extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:init {user} {orgName} {shortName}';

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

//        UBT::info('test from ll', ['user' => '123']);
//        $ubt = new UBT();
//        $ubt->info('test from ll', ['user' => '123']);
        UBT::info('test from ll', ['user' => '123']);
        $this->info('sent');
        return 1;

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

        $orgName = $args['orgName'];
        if (!$orgName) {
            $message = 'org name  is required';
            $this->error($message);
            return -1;
        }

        $shortName = $args['shortName'];
        if (!$shortName) {
            $message = 'org short name required';
            $this->error($message);
            return -1;
        }

        $this->info('command tenant init database');
        $eventUserRegistered = new UserRegistered(
            $user,
            $args['orgName'],
            $args['shortName'],
        );
        event($eventUserRegistered);

        return 0;
    }


}
