<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Laraview\Libs\Traits\FilePropertyEditor;

class LaraviewGenerateRegion extends Command
{

    use DetectsApplicationNamespace,
        FilePropertyEditor;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraview:region';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates files for a Laraview region';

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
        $name = $this->askForNameOfRegion();
        $viewClass = $this->askWhichView();
        $placeholder = $this->generatePlaceholder( $name );

        try {
            $view = self::getClassDetails( $viewClass );
        } catch( \Exception $e ) {
            $this->error( "{$viewClass} does not exist" );
            exit;
        }

        $region = $this->createTempRegion( $name, $view );
        $this->generate( $region, $view, $placeholder );
        $this->info( "Use placeholder '{$placeholder}' in your template file!" );

    }

    /**
     * @param $inputName
     * @param $viewDetails
     * @return object
     */
    protected function createTempRegion( $inputName, $viewDetails )
    {
        $regionClassName = self::nameToClassName( $inputName, 'Region' );
        if( $viewDetails->isLocal ) {
            $fileName = $viewDetails->folder . "Regions" . DIRECTORY_SEPARATOR . "{$regionClassName}.php";
            $namespaceWithClassName = $viewDetails->namespaceWithoutClassName . "\Regions\\{$regionClassName}";
            return (object) [
                'inputName' => $inputName,
                'nameToClassFormat' => self::nameToClassName( $inputName, '' ),
                'namespaceWithoutClassName' => $viewDetails->namespaceWithoutClassName . "\Regions",
                'namespaceWithClassName' => $namespaceWithClassName,
                'className' => $regionClassName,
                'fileName' => $fileName,
                'folder' => dirname( $fileName ) . DIRECTORY_SEPARATOR,
                'isLocal' => self::isLocalNamespace( $namespaceWithClassName ),
            ];
        }
        die( "NEED TO DO THIS BIT, NOT LOCAL ONES!!!!" );
    }

    /**
     * @param $region
     * @param $view
     * @param $placeholder
     */
    private function generate( $region, $view, $placeholder )
    {
        $content = $this->getContent( $region, $placeholder );
        file_put_contents( $region->fileName, $content );

        $this->importRegionToView( $region, $view );
        $this->info( "{$region->fileName} created!" );
    }

    /**
     * @param $region
     * @param $view
     */
    protected function importRegionToView( $region, $view )
    {
        if( $view->isLocal ) {
            $this->addToClassesArray( $view->fileName, '$regions', function( &$rebuild ) use ( $region ) {
                $rebuild[] = [ 319, "\\{$region->namespaceWithClassName}", 21 ];
                $rebuild[] = [ 387, '::', 21 ];
                $rebuild[] = [ 361, 'class', 21 ];
            } );
        }
    }

    /**
     * @param $region
     * @param $placeholder
     * @return mixed
     */
    private function getContent( $region, $placeholder )
    {
        $file = file_get_contents( __DIR__ . '/../../../stubs/region.stub' );
        return str_replace( [
            '[NAMESPACE]',
            '[CLASS_NAME]',
            '[PLACEHOLDER]',
        ], [
            $region->namespaceWithoutClassName,
            $region->className,
            $placeholder
        ], $file );
    }

    /**
     * @return string
     */
    private function askWhichView()
    {
        $views = app( RegisterBlueprint::class )->views();
        $choices = array_combine( $views, $views );
        $choice = $this->choice( "What view is this region for?", $choices );
        if( ! in_array( $choice, $views ) ) {
            return $this->askWhichView();
        }
        return $choice;
    }

    /**
     * @param $name
     * @return string
     */
    private function generatePlaceholder( $name )
    {
        return '[' . strtoupper( str_slug( $name, '_' ) ) . ']';
    }

    /**
     * @return mixed
     */
    private function askForNameOfRegion()
    {
        return $this->ask( 'What is the name of your region?' );
    }

}
