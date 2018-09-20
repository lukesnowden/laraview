<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //$name = $this->ask( 'What is the name of your region (singular)?' );
        //$viewClass = $this->ask( 'What is the full path class name of the View this region is for?' );

        $name =  'left hand column';
        $viewClass = 'Laraview\Laraview\ProductEdit\ProductEditView';

        if( ! class_exists( '\\' . $viewClass ) ) {
            $this->error( "{$viewClass} does not exist" );
            exit;
        }

        $this->generate( $name, $viewClass );

    }

    /**
     * @param $name
     * @param $viewClass
     * @throws \ReflectionException
     */
    private function generate( $name, $viewClass )
    {
        $path = $this->getPath( $viewClass );
        $className = ucfirst( camel_case( preg_replace( '/[^\d\w]/', '_', $name ) ) );
        $content = $this->getContent( $className, $viewClass );
        file_put_contents( $path . 'Regions' . DIRECTORY_SEPARATOR . "{$className}Region.php", $content );
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
    private function getContent( $className, $viewClass )
    {
        $file = file_get_contents( base_path( 'stubs/region.stub' ) );
        return str_replace( [
            '[NAMESPACE]',
            '[VIEW_NAME]',
            '[CLASS_NAME]',
            '[PLACEHOLDER]',
        ], [
            $this->getAppNamespace(),
            $this->getViewFolderName( $viewClass ),
            $className . 'Region'
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

}
