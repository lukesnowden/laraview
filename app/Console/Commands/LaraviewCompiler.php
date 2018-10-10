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
    protected $signature = 'laraview:compile {--o|--only=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Compiles attached views, regions and elements into view blade files.';

    public $only = [];

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
        $this->checkForSelectedViewGeneration();

        app( RegisterBlueprint::class )
            ->console( $this )
            ->generate();
    }

    /**
     * @return void
     */
    protected function checkForSelectedViewGeneration()
    {
        if( $only = $this->option( 'only' ) ) {
            foreach( array_filter( array_map( 'trim', explode( ',', $only ) ) ) as $class ) {
                if( ! class_exists( $class ) ) {
                    $this->error( "{$class} does not exist..." );
                } else {
                    $this->only[] = $class;
                }
            }
        }
    }

}
