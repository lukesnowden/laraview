<?php

namespace Laraview\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\RegisterBlueprint;

class LaraviewGenerateLayout extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraview:layout';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates files for a Laraview layout.';

    /**
     * @var
     */
    protected $contract;

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
     * @return mixed
     */
    public function contract()
    {
        return $this->contract;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->contract = app( RegisterBlueprint::class );
        $layoutClassName = $this->choice( "What layout would you like to create?", array_combine( $this->contract->registeredLayouts(), $this->contract->registeredLayouts() ) );

        if( ! method_exists( $layoutClassName, 'generate' ) ) {
            $this->error( "Layout {$layoutClassName} does not provide generation support, exiting..." );
            exit;
        }

        $layoutClassName::generate( $this );

    }
}
