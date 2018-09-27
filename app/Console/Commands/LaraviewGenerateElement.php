<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Illuminate\Console\DetectsApplicationNamespace;
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
    protected $signature = 'laraview:element';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates files for a Laraview element';

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
     * @return void
     */
    public function handle()
    {
        $region = $this->askWhichRegionElementIsFor();
        $element = $this->askWhatTypeOfElement();

        try {
            $region = self::getClassDetails( $region );
        } catch( \Exception $e ) {
            $this->error( "{$region->fileName} does not exist" );
            exit;
        }

        if( ! method_exists( $element, 'generate' ) ) {
            $this->error( "Element {$element} does not allow self generation, exiting..." );
            exit;
        }

        $fileName = $element::generate( $region, $this );
        $this->info( "{$fileName} created!" );
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

    /**
     * @return array
     */
    private function getElements()
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
    private function askWhatTypeOfElement()
    {
        return $this->choice( "What kind of element would you like to create?", $this->getElements() );
    }

}
