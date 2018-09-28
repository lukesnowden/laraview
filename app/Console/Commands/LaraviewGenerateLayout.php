<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Laraview\Libs\Traits\FilePropertyEditor;

class LaraviewGenerateLayout extends Command
{

    use FilePropertyEditor;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraview:layout {--c|--compile}';

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
     * @var bool
     */
    protected $compile = false;

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
        $this->compile = $this->option( 'compile' );
        $this->contract = app( RegisterBlueprint::class );
        $region = $this->askWhichRegionElementIsFor();
        $layoutClassName = $this->askWhichLayoutToCreate();

        try {
            $region = self::getClassDetails( $region );
        } catch( \Exception $e ) {
            $this->error( "{$region->fileName} does not exist" );
            exit;
        }

        if( ! method_exists( $layoutClassName, 'generate' ) ) {
            $this->error( "Layout {$layoutClassName} does not provide generation support, exiting..." );
            exit;
        }

        $layoutClassName::generate( $region, $this );

        if( $this->compile ) {
            $this->call( "laraview:compile" );
        }
    }

    /**
     * @return string
     */
    protected function askWhichLayoutToCreate()
    {
        return $this->choice( "What layout would you like to create?", array_combine( $this->contract->registeredLayouts(), $this->contract->registeredLayouts() ) );
    }

    /**
     * @param bool $askChoice
     * @return mixed|string
     */
    private function askWhichRegionElementIsFor( $askChoice = true )
    {

        if( $askChoice ) {
            $choices = [ '-' => 'Enter Manually' ] + app( RegisterBlueprint::class )->regions();
            $region = $this->choice( "What region is this element for?", $choices );
            if( $region !== '-' ) {
                return $choices[ $region ];
            }
        }

        $region = $this->ask( "What region is this element for (fully qualified class name)?" );
        if( ! class_exists( "\\" . $region ) ) {
            $this->error( "Region {$region} does not exist" );
            return $this->askWhichRegionElementIsFor( false );
        }
        return $region;
    }
}
