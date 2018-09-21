<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Illuminate\Console\DetectsApplicationNamespace;
use ReflectionClass;

class LaraviewGenerateElement extends Command
{

    use DetectsApplicationNamespace;

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

    protected $elements = [
        'Text',
        'Select',
        'Checkbox',
        'Radio'
    ];

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
        $region = $this->getRegion();
        $elementType = $this->choice( "What kind of element would you like to create?", $this->elements );

        switch( $elementType ) {
            case  0 :
                $this->textElement( $region );
            break;
            case 1 :
                $this->selectElement( $region );
            break;
            case 2 :
                $this->checkboxElement( $region );
            break;
            case 3 :
                $this->radioElement( $region );
            break;
        }
    }

    private function textElement( $region )
    {
        $path = $this->generateFile(
            __DIR__ . '/../../../stubs/elements/text.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'text'
        );
    }

    private function selectElement( $region )
    {
        $name = $this->getNameOfElement();
        $label = $this->getElementsLabel();
    }

    private function checkboxElement( $region )
    {
        $name = $this->getNameOfElement();
        $label = $this->getElementsLabel();
    }

    private function radioElement( $region )
    {
        $name = $this->getNameOfElement();
        $label = $this->getElementsLabel();
    }

    /**
     * @return string
     */
    private function getNameOfElement()
    {
        return str_slug( $this->ask( "What is the name of this element?" ), '_' );
    }

    /**
     * @return mixed
     */
    private function getElementsLabel()
    {
        return $this->ask( "What label would you like to use for this element?" );
    }

    /**
     * @param bool $askChoice
     * @return mixed|string
     */
    private function getRegion( $askChoice = true )
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
            return $this->getRegion( false );
        }
        return $region;
    }

    /**
     * @return array|mixed
     */
    private function getAttributes()
    {
        if( ! $this->confirm( "Would you like to add attributes to the input element?", true ) ) {
            return [];
        }
        $attributes = $this->ask( "Please enter attributes in key:value format separated with a comma (a:Apple,b:Pear)" );
        $chunks = array_map( 'trim', explode( ',', $attributes ) );
        $attributes = [];
        foreach( $chunks as $choice ) {
            $pair = array_map( 'trim', explode( ':', $choice ) );
            if( ! isset( $pair[ 1 ] ) ) {
                $attributes[ $choice ] = $choice;
            } else {
                $attributes[ $pair[ 0 ] ] = $pair[ 1 ];
            }
        }
        return $attributes;
    }

    /**
     * @param string $path
     * @param string $region
     * @param string $name
     * @param $label
     * @param array $attributes
     * @param string $type
     * @throws \ReflectionException
     */
    private function generateFile( string $path, string $region, string $name, $label, array $attributes, string $type )
    {
        $view = $this->getView( $region );
        $viewShortClassName = ( new ReflectionClass( $view ) )->getShortName();
        $regionShortClassName = ( new ReflectionClass( $region ) )->getShortName();

        $classifiedName = ucfirst( camel_case( $name ) );
        $className = $classifiedName . ucfirst( $type ) . 'Element';

        $contents = str_replace(
            [
                '[NAMESPACE]',
                '[CLASS_NAME]',
                '[NAME]',
                '[LABEL]',
                '[ATTRIBUTES]',
                '[VIEW_NAME]',
                '[REGION_NAME]',
            ],
            [
                $this->getAppNamespace(),
                $className,
                $name,
                $label,
                $this->stringifyAttributes( $attributes ),
                $viewShortClassName,
                $regionShortClassName,
            ],
            file_get_contents( $path )
        );
        $path = $this->createFoldersReturnPathForNamespace( $this->getAppNamespace() . "Laraview\\{$viewShortClassName}\Regions\\{$regionShortClassName}\Elements" );
        file_put_contents( $path . $className . '.php', $contents );
    }

    /**
     * @param $region
     * @return mixed
     */
    private function getView( $region )
    {
        $view = app( RegisterBlueprint::class )->getViewForRegion( $region );
        if( is_null( $view ) ) {
            $this->error( "Region {$region} is not currently registered to a view. Please register and then create your elements." );
            exit;
        }
        return $view;
    }

    /**
     * @param array $attributes
     * @return string
     */
    private function stringifyAttributes( array $attributes )
    {
        $html = '';
        foreach( $attributes as $name => $value ) {
            $html .= "'{$name}' => '{$value}',\n\t\t";
        }
        return trim( $html );
    }

    /**
     * @param string $namespace
     * @return string
     */
    private function createFoldersReturnPathForNamespace( string $namespace )
    {
        $namespace = preg_replace( '/^' . preg_quote( $this->getAppNamespace(), '/' ) . '/', '', $namespace );
        $folders = explode( '\\', $namespace );
        $path = app_path();
        while( $folders ) {
            $path .= DIRECTORY_SEPARATOR . array_shift( $folders );
            if( ! file_exists( $path ) ) {
                mkdir( $path, 0655 );
            }
        }
        return $path . DIRECTORY_SEPARATOR;
    }

}
