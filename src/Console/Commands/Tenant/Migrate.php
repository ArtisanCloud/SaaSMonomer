<?php
declare(strict_types=1);

namespace ArtisanCloud\SaaSMonomer\Console\Commands\Tenant;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;



class Migrate extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'tenant:migrate';


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
//        dd($this->arguments());
//        dd($this->options());

//        Artisan::call('passport:install');
        $this->info('command tenant migrate database');

        return 0;
    }



}
