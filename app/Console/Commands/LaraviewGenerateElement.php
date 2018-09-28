<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\LayoutBlueprint;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Illuminate\Console\DetectsApplicationNamespace;
use Laraview\Libs\Blueprints\TabBlueprint;
use Laraview\Libs\Layouts\Tabs;
use Laraview\Libs\Traits\FilePropertyEditor;

class LaraviewGenerateElement extends Command
{

    use DetectsApplicationNamespace,
        FilePropertyEditor;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraview:element {--c|--compile}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates files for a Laraview element';

    /**
     * @var bool
     */
    protected $compile = false;

    /**
     * @var
     */
    protected $layouts;

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
    public function handle()
    {
        $this->compile = $this->option( 'compile' );

        $whatFor = $this->askIfForARegionOrLayout();

        if( $whatFor == 1 ) {
            $region = $this->askWhichRegionElementIsFor();
        } else {
            $layout = $this->askWhichLayoutElementIsFor();
            if( $this->layouts[ $layout ] instanceof Tabs ) {
                $tab = $this->askWhichTab( $this->layouts[ $layout ] );
                return $this->createForTab( $tab );
            }
        }

        return $this->createForRegion( $region );

    }

    /**
     * @param $tab
     */
    protected function createForTab( $tab )
    {
        try {
            $tab = self::getClassDetails( $tab );
        } catch( \Exception $e ) {
            $this->error( "{$tab->fileName} does not exist" );
            exit;
        }

        $element = $this->askWhatTypeOfElement();

        if( ! method_exists( $element, 'generate' ) ) {
            $this->error( "Element {$element} does not allow self generation, exiting..." );
            exit;
        }

        $fileName = $element::generate( $tab, $this );
        $this->info( "{$fileName} created!" );

        if( $this->compile ) {
            $this->call( "laraview:compile" );
        }
    }

    /**
     * @param $region
     */
    protected function createForRegion( $region )
    {
        try {
            $region = self::getClassDetails( $region );
        } catch( \Exception $e ) {
            $this->error( "{$region->fileName} does not exist" );
            exit;
        }

        $element = $this->askWhatTypeOfElement();

        if( ! method_exists( $element, 'generate' ) ) {
            $this->error( "Element {$element} does not allow self generation, exiting..." );
            exit;
        }

        $fileName = $element::generate( $region, $this );
        $this->info( "{$fileName} created!" );

        if( $this->compile ) {
            $this->call( "laraview:compile" );
        }
    }

    /**
     * @param bool $askChoice
     * @return mixed|string
     */
    protected function askWhichRegionElementIsFor( $askChoice = true )
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

    /**
     * @return array
     */
    protected function getElements()
    {
        $elements = [];
        foreach( app( RegisterBlueprint::class )->registeredElements() as $element ) {
            $elements[ $element ] = $element::humanReadableName();
        }
        return $elements;
    }

    /**
     * @return string
     */
    protected function askWhatTypeOfElement()
    {
        return $this->choice( "What kind of element would you like to create?", $this->getElements() );
    }

    /**
     * @return string
     */
    protected function askIfForARegionOrLayout()
    {
        return $this->choice( "Is this element for a Region or a Layout?", [ '1' => 'Region', '2' => 'Layout' ] );
    }

    /**
     * @return string
     */
    protected function askWhichLayoutElementIsFor()
    {
        $this->layouts =  $this->filterTabs( app( RegisterBlueprint::class )->regionElements() );
        return $this->choice( "What layout is this element for?", array_combine( array_keys( $this->layouts ), array_keys( $this->layouts ) ) );
    }

    /**
     * @param $elements
     * @return array
     */
    protected function filterTabs( $elements )
    {
        $tabs = [];
        foreach( $elements as $key => $element ) {
            if( $element instanceof LayoutBlueprint ) {
                $tabs[ $key ] = $element;
            }
        }
        return $tabs;
    }

    /**
     * @param $tabsLayout
     * @return string
     */
    protected function askWhichTab( $tabsLayout )
    {
        $tabs = $tabsLayout->tabs();
        return $this->choice( "What tab is this element for?", array_combine( array_keys( $tabs ), array_keys( $tabs ) ) );
    }

}
