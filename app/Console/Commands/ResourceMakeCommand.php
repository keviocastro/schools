<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ResourceMakeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:resource {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return mixed
     */
    public function handle()
    {
        $name = $this->argument('name');

        Artisan::call('make:test', [
                'name' => 'Http/Controllers/'.ucfirst(camel_case($name)).'ControllerTest'
            ]);

        Artisan::call('make:controller', [
                'name' => ucfirst(camel_case($name))
            ]);
        
        Artisan::call('make:model', [
                'name' => ucfirst(camel_case($name))
            ]);

    }
}
