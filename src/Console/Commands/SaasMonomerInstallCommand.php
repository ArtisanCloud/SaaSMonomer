<?php

namespace ArtisanCloud\SaaSMonomer\Console\Commands;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Command;



class SaasMonomerInstallCommand extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'saasmonomer:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'saasmonomer:install';


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
        $this->info('install saas monomer');

        return 0;
    }



}
