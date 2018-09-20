<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\RegisterBlueprint;

class LaraviewCompiler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraview:compile';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compiles attached views, regions and elements into view blade files.';

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
        app( RegisterBlueprint::class )
            ->console( $this )->generate();
    }
}
