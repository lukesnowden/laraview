<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use ReflectionClass;

class LaraviewGenerateRegion extends Command
{

    use DetectsApplicationNamespace;

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
     * @throws \ReflectionException
     */
    public function handle()
    {
        $name = $this->ask( 'What is the name of your region (singular)?' );
        $viewClass = $this->getView();
        $placeholder = $this->ask( 'What placeholder would you like to use for this region?' );

        if( ! class_exists( '\\' . $viewClass ) ) {
            $this->error( "{$viewClass} does not exist" );
            exit;
        }

        $this->generate( $name, $viewClass, $placeholder );

    }

    /**
     * @param $name
     * @param $viewClass
     * @throws \ReflectionException
     */
    private function generate( $name, $viewClass, $placeholder )
    {
        $path = $this->getPath( $viewClass );
        $className = ucfirst( camel_case( preg_replace( '/[^\d\w]/', '_', $name ) ) );
        $content = $this->getContent( $className, $viewClass, $placeholder );
        $filePath = $path . 'Regions' . DIRECTORY_SEPARATOR . "{$className}Region.php";
        file_put_contents( $filePath, $content );
        $this->info( "{$filePath} created!" );
    }

    /**
     * @param $viewClass
     * @return string
     */
    private function getPath( $viewClass )
    {
        $parts = explode( '\\', preg_replace( '/^' . preg_quote( $this->getAppNamespace() ) . '/', '', $viewClass ) );
        array_pop( $parts );
        return app_path( implode( DIRECTORY_SEPARATOR, $parts ) ) . DIRECTORY_SEPARATOR;
    }

    /**
     * @param $className
     * @param $viewClass
     * @return mixed
     * @throws \ReflectionException
     */
    private function getContent( $className, $viewClass, $placeholder )
    {
        $file = file_get_contents( __DIR__ . '/../../../stubs/region.stub' );
        return str_replace( [
            '[NAMESPACE]',
            '[VIEW_NAME]',
            '[CLASS_NAME]',
            '[PLACEHOLDER]',
        ], [
            $this->getAppNamespace(),
            $this->getViewFolderName( $viewClass ),
            $className . 'Region',
            $placeholder
        ], $file );
    }

    /**
     * @param $viewClass
     * @return mixed
     * @throws \ReflectionException
     */
    private function getViewFolderName( $viewClass )
    {
        return array_reverse( explode( '\\', ( new ReflectionClass( $viewClass ) )->getNamespaceName() ) )[ 0 ];
    }

    /**
     * @param bool $askChoice
     * @return mixed|string
     */
    private function getView( $askChoice = true )
    {

        if( $askChoice ) {
            $choices = [ '-' => 'Enter Manually' ] + app( RegisterBlueprint::class )->views();
            $view = $this->choice( "What view is this region for?", $choices );
            if( $view !== '-' ) {
                return $choices[ $view ];
            }
        }

        $view = $this->ask( "What view is this region for (fully qualified class name)?" );
        if( ! class_exists( "\\" . $view ) ) {
            $this->error( "View {$view} does not exist" );
            return $this->getView( false );
        }
        return $view;
    }

}
