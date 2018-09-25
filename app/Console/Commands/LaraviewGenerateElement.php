<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Illuminate\Console\DetectsApplicationNamespace;
use Laraview\Libs\Elements\Checkbox;
use Laraview\Libs\Elements\Email;
use Laraview\Libs\Elements\Password;
use Laraview\Libs\Elements\Radio;
use Laraview\Libs\Elements\Select;
use Laraview\Libs\Elements\Text;
use Laraview\Libs\Elements\Textarea;
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
        $region = $this->getRegion();
        $elementType = $this->choice( "What kind of element would you like to create?", $this->getElements() );

        switch( $elementType ) {
            case  Text::class :
                $file = $this->textElement( $region );
            break;
            case Select::class :
                $file = $this->selectElement( $region );
            break;
            case Checkbox::class :
                $file = $this->checkboxElement( $region );
            break;
            case Radio::class :
                $file = $this->radioElement( $region );
            break;
            case Password::class :
                $file = $this->passwordElement( $region );
            break;
            case Email::class :
                $file = $this->emailElement( $region );
            break;
            case Textarea::class :
                $file = $this->textareaElement( $region );
            break;
            default :
                $elementType::generate( $region, $elementType );
            break;
        }

        $this->info( "{$file} element created!" );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function textareaElement( $region )
    {
        return $this->generateFile(
            __DIR__ . '/../../../stubs/elements/textarea.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'textarea'
        );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function emailElement( $region )
    {
        return $this->generateFile(
            __DIR__ . '/../../../stubs/elements/email.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'email'
        );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function passwordElement( $region )
    {
        return $this->generateFile(
            __DIR__ . '/../../../stubs/elements/password.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'password'
        );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function textElement( $region )
    {
        return $this->generateFile(
            __DIR__ . '/../../../stubs/elements/text.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'text'
        );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function selectElement( $region )
    {
        return $this->generateSelectFile(
            __DIR__ . '/../../../stubs/elements/select.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            $this->getSelectOptions()
        );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function checkboxElement( $region )
    {
        return $this->generateFile(
            __DIR__ . '/../../../stubs/elements/checkbox.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'checkbox'
        );
    }

    /**
     * @param $region
     * @return string
     * @throws \ReflectionException
     */
    private function radioElement( $region )
    {
        return $this->generateFile(
            __DIR__ . '/../../../stubs/elements/radio.stub',
            $region,
            $this->getNameOfElement(),
            $this->getElementsLabel(),
            $this->getAttributes(),
            'radio'
        );
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
    private function stringifyAsArray( array $attributes )
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

    /**
     * @return array|mixed
     */
    private function getSelectOptions()
    {
        return $this->keyPair(
            $this->ask( "Please enter options in key:value format separated with a comma (a:Apple,b:Pear)" )
        );
    }

    /**
     * @return array|mixed
     */
    private function getAttributes()
    {
        if( ! $this->confirm( "Would you like to add attributes to the input element?", true ) ) {
            return [];
        }
        return $this->keyPair(
            $this->ask( "Please enter attributes in key:value format separated with a comma (a:Apple,b:Pear)" )
        );
    }

    /**
     * @param $attributes
     * @return array
     */
    private function keyPair( $attributes )
    {
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
     * @return string
     * @throws \ReflectionException
     */
    private function generateFile( string $path, string $region, string $name, $label, array $attributes, string $type )
    {
        $view = $this->getView( $region );
        $viewShortClassName = preg_replace( '/View$/', '', ( new ReflectionClass( $view ) )->getShortName() );
        $regionShortClassName = preg_replace( '/Region$/', '', ( new ReflectionClass( $region ) )->getShortName() );
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
                $this->stringifyAsArray( $attributes ),
                $viewShortClassName,
                $regionShortClassName,
            ],
            file_get_contents( $path )
        );
        $path = $this->createFoldersReturnPathForNamespace( $this->getAppNamespace() . "Laraview\\{$viewShortClassName}\Regions\\{$regionShortClassName}\Elements" );
        file_put_contents( $path . $className . '.php', $contents );
        return $path . $className . '.php';
    }

    /**
     * @param string $path
     * @param string $region
     * @param string $name
     * @param $label
     * @param array $attributes
     * @param array $options
     * @return string
     * @throws \ReflectionException
     */
    private function generateSelectFile( string $path, string $region, string $name, $label, array $attributes, array $options )
    {
        $type = 'select';
        $view = $this->getView( $region );
        $viewShortClassName = preg_replace( '/View$/', '', ( new ReflectionClass( $view ) )->getShortName() );
        $regionShortClassName = preg_replace( '/Region$/', '', ( new ReflectionClass( $region ) )->getShortName() );

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
                '[OPTIONS]'
            ],
            [
                $this->getAppNamespace(),
                $className,
                $name,
                $label,
                $this->stringifyAsArray( $attributes ),
                $viewShortClassName,
                $regionShortClassName,
                $this->stringifyAsArray( $options ),
            ],
            file_get_contents( $path )
        );
        $path = $this->createFoldersReturnPathForNamespace( $this->getAppNamespace() . "Laraview\\{$viewShortClassName}\Regions\\{$regionShortClassName}\Elements" );
        file_put_contents( $path . $className . '.php', $contents );
        return $path . $className . '.php';
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

}
