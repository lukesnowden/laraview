<?php

namespace Laraview\Libs\Elements\Generate;

use Exception;
use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Libs\Traits\FilePropertyEditor;

class InputGeneration
{

    use FilePropertyEditor;

    /**
     * @var
     */
    protected $region;

    /**
     * @var LaraviewGenerateElement
     */
    protected $console;

    /**
     * @var string
     */
    protected $stubPath = '';

    /**
     * InputGeneration constructor.
     * @param $region
     * @param LaraviewGenerateElement $console
     */
    public function __construct( $region, LaraviewGenerateElement $console )
    {
        $this->region = $region;
        $this->console = $console;
        $this->region->nameToClassFormat = preg_replace( '/Region$/', '', $this->region->className );
        return $this;
    }

    /**
     * @param $path
     */
    public function setStubPath( $path )
    {
        $this->stubPath = $path;
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $name = $this->askForNameOfElement();
        $label = $this->askForElementsLabelText();
        $attributes = $this->askForElementsAttributes();
        $element = $this->createTempElement( $name );
        $contents = $this->getContents( $element, $label, $attributes );
        $this->createFolder( $element->folder );

        file_put_contents( $element->fileName, $contents );
        $this->importElementToRegion( $element );

        return $element->fileName;
    }

    /**
     * @param $element
     */
    protected function importElementToRegion( $element )
    {
        if( $this->region->isLocal ) {
            $this->addToClassesArray( $this->region->fileName, '$elements', function( &$rebuild ) use ( $element ) {
                $rebuild[] = [ 319, "\\{$element->namespaceWithClassName}", 21 ];
                $rebuild[] = [ 387, '::', 21 ];
                $rebuild[] = [ 361, 'class', 21 ];
            } );
        }
    }

    /**
     * @param $path
     * @param null $orig
     * @return mixed
     */
    protected function createFolder( $path, $orig = null )
    {
        $path = rtrim( $path, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        if( ! is_null( $orig ) ) {
            $orig = rtrim( $orig, DIRECTORY_SEPARATOR ) . DIRECTORY_SEPARATOR;
        }

        try {
            if( ! file_exists( $path ) ) {
                mkdir( $path, 0655 );
                if( ! is_null( $orig ) && $path !== $orig ) {
                    return $this->createFolder( $orig, null );
                }
            }
        } catch( Exception $e ) {
            if( $e->getMessage() === 'mkdir(): No such file or directory' ) {
                if( ! preg_match( '/^' . preg_quote( app_path(), '/' ) . '/', $path ) ) {
                    die( "This has gone too far!!" );
                }
                return $this->createFolder( dirname( $path ), $path );
            }
        }
    }

    /**
     * @param $inputName
     * @return object
     */
    protected function createTempElement( $inputName )
    {
        $elementClassName = self::nameToClassName( $inputName, 'Element' );
        if( $this->region->isLocal ) {
            $fileName = $this->region->folder . "{$this->region->nameToClassFormat}" . DIRECTORY_SEPARATOR . "Elements" . DIRECTORY_SEPARATOR . "{$elementClassName}.php";
            $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Elements";
            $namespaceWithClassName = $namespaceWithoutClassName . "\\{$elementClassName}";
            return (object) [
                'inputName' => $inputName,
                'nameToClassFormat' => self::nameToClassName( $inputName, '' ),
                'namespaceWithoutClassName' => $namespaceWithoutClassName,
                'namespaceWithClassName' => $namespaceWithClassName,
                'className' => $elementClassName,
                'fileName' => $fileName,
                'folder' => dirname( $fileName ) . DIRECTORY_SEPARATOR,
                'isLocal' => self::isLocalNamespace( $namespaceWithClassName ),
            ];
        }
    }

    /**
     * @param $element
     * @param $label
     * @param array $attributes
     * @return mixed
     */
    public function getContents( $element, $label, $attributes = [] )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[CLASS_NAME]',
                '[NAME]',
                '[LABEL]',
                '[ATTRIBUTES]',
            ],
            [
                $element->namespaceWithoutClassName,
                $element->className,
                $element->inputName,
                $label,
                $this->stringifyAsArray( $attributes ),
            ],
            file_get_contents( $this->stubPath )
        );
    }

    /**
     * @param array $array
     * @return string
     */
    protected function stringifyAsArray( array $array )
    {
        $html = '';
        foreach( $array as $name => $value ) {
            $html .= "'{$name}' => '{$value}',\n\t\t";
        }
        return trim( $html );
    }

    /**
     * @return string
     */
    protected function askForNameOfElement()
    {
        return str_slug( $this->console->ask( "What is the name of this element?" ), '_' );
    }

    /**
     * @return mixed
     */
    protected function askForElementsLabelText()
    {
        return $this->console->ask( "What label would you like to use for this element?" );
    }

    /**
     * @return array|mixed
     */
    protected function askForElementsAttributes()
    {
        if( ! $this->console->confirm( "Would you like to add attributes to the input element?", true ) ) {
            return [];
        }
        return $this->keyPair(
            $this->console->ask( "Please enter attributes in key:value format separated with a comma (a:Apple,b:Pear)" )
        );
    }

    /**
     * @param $attributes
     * @return array
     */
    protected function keyPair( $attributes )
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

}