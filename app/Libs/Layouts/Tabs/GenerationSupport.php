<?php

namespace Laraview\Libs\Layouts\Tabs;

use Laraview\Console\Commands\LaraviewGenerateLayout;
use Illuminate\Container\Container;
use Laraview\Libs\Traits\FilePropertyEditor;

class GenerationSupport
{

    use FilePropertyEditor;

    /**
     * @var
     */
    protected $console;

    /**
     * @param LaraviewGenerateLayout $console
     */
    public function __construct( LaraviewGenerateLayout $console )
    {
        $this->console = $console;
        $name = $this->askForNewLayoutName();
        $tabCount = (int) $console->ask( "How many tabs would you like to initially create?" );
        $region = $console->choice( "What region would you like to attach this tabs layout to?", array_combine( $console->contract()->regions(), $console->contract()->regions() ) );

        $tabNames = $this->askForAllTabNames( $tabCount );
        $data = $this->generateFiles( $name, $region, $tabNames );

        $file = "{$data->base_path}Region.php";

        $this->addToClassesArray( $file, '$elements', function( &$rebuild ) use( $data ) {

            $rebuild[] = [ 319, "\\{$data->base_namespace}\Layouts\\{$data->class_name}", 21 ];
            $rebuild[] = [ 387, '::', 21 ];
            $rebuild[] = [ 361, 'class', 21 ];

        });

    }

    /**
     * @param $tabCount
     * @return array
     */
    public function askForAllTabNames( $tabCount )
    {
        $tabNames = [];
        for( $x = 1; $x <= $tabCount; $x++ ) {
            $tabNames[] = $this->askForTabNames( $x );
        }
        return $tabNames;
    }

    /**
     * @return mixed
     */
    protected function askForNewLayoutName()
    {
        $name = $this->console->ask( "What would you like this tabs layout to be called?" );
        if( strlen( $name ) < 5 ) {
            $this->console->error( "Please provide a name longer than 5 characters" );
            return $this->askForNewLayoutName();
        }
        if( is_numeric( $name[ 0 ] ) ) {
            $this->console->error( "Please provide a name that begins with a letter." );
            return $this->askForNewLayoutName();
        }
        return $name;
    }

    /**
     * @param $tabIndex
     * @return mixed
     */
    protected function askForTabNames( $tabIndex )
    {
        $name = $this->console->ask( "What name would you like to give tab {$tabIndex}" );
        if( strlen( $name ) < 5 ) {
            $this->console->error( "Please provide a name longer than 5 characters" );
            return $this->askForTabNames( $tabIndex );
        }
        if( is_numeric( $name[ 0 ] ) ) {
            $this->console->error( "Please provide a name that begins with a letter." );
            return $this->askForTabNames( $tabIndex );
        }
        return $name;
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function createMainTabsFile( $data )
    {
        $template = str_replace(
            [
                '[NAMESPACE]',
                '[TABS_IMPORT]',
                '[CLASS_NAME]',
                '[TABS]',
            ],
            [
                "{$data->base_namespace}\Layouts",
                self::genTabsImport( $data ),
                $data->class_name,
                self::genTabsDefinition( $data )
            ],
            file_get_contents( self::stubsDir( 'layouts/tabs/tabs.stub' ) )
        );

        file_put_contents( $data->class_file, $template );
        return $data->class_file;
    }

    /**
     * @param $data
     * @param $className
     * @param $filePath
     * @return mixed
     */
    protected function createTabFile( $data, $className, $filePath )
    {
        $template = str_replace(
            [
                '[NAMESPACE]',
                '[NAME]',
                '[CLASS_NAME]',
            ],
            [
                "{$data->base_namespace}\Layouts\\{$data->class_name}\Tabs",
                $data->tabs_names[ $className ],
                $className,
            ],
            file_get_contents( self::stubsDir( 'layouts/tabs/tab.stub' ) )
        );
        file_put_contents( $filePath, $template );
        return $filePath;
    }

    /**
     * @param $name
     * @param $region
     * @param $tabNames
     * @return object
     */
    protected function generateFiles( $name, $region, $tabNames )
    {
        $data = $this->getDetailsForCreation( $name, $region, $tabNames );

        $this->console->info(
            $this->createMainTabsFile( $data ) . ' created...'
        );

        foreach( $data->tabs as $className => $filePath ) {
            $this->console->info(
                $this->createTabFile( $data, $className, $filePath ) . ' created...'
            );
        }

        return $data;
    }

    /**
     * @param $data
     * @return string
     */
    public static function genTabsImport( $data )
    {
        $str = '';
        foreach( $data->tabs as $className => $filePath ) {
            $str .= "use {$data->base_namespace}\Layouts\\{$data->class_name}\Tabs\\{$className};\n";
        }
        return rtrim( $str );
    }

    /**
     * @param $data
     * @return string
     */
    public static function genTabsDefinition( $data )
    {
        $str = '';
        foreach( $data->tabs as $className => $filePath ) {
            $str .= "{$className}::class,\n\t\t";
        }
        return rtrim( $str );
    }

    /**
     * @param $name
     * @param $className
     * @param $tabNames
     * @param string $trim
     * @param int $depth
     * @return object
     */
    protected function getDetailsForCreation( $name, $className, $tabNames, $trim = 'Region', $depth = 3 )
    {
        $trimmed = preg_replace( "/{$trim}$/", '', $className );
        $correctDepth = array_slice( explode( '\\', $trimmed ), ( 0 - $depth ), $depth );
        $layoutPath = self::createFolders( array_merge( $correctDepth, [ 'Layouts', self::convertNameToClassName( $name ) ] ) );
        $tabsPath = self::createFolders( array_merge( $correctDepth, [ 'Layouts', self::convertNameToClassName( $name ), 'Tabs' ] ) );

        $data = [
            'base_namespace' => self::appNamespace() . implode( '\\', array_merge( [ 'Laraview' ], $correctDepth ) ),
            'base_path' => self::createFolders( $correctDepth ),
            'layout_path' => $layoutPath,
            'tabs' => [],
            'tabs_names' => [],
            'class_file' => $layoutPath . '.php',
            'class_name' => self::convertNameToClassName( $name )
        ];

        foreach( $tabNames as $tabName ) {
            $data[ 'tabs' ][ self::convertNameToClassName( $tabName, 'Tab' ) ] = $tabsPath . '/' . self::convertNameToClassName( $tabName, 'Tab.php' );
            $data[ 'tabs_names' ][ self::convertNameToClassName( $tabName, 'Tab' ) ] = $tabName;
        }

        return (object) $data;

    }

    /**
     * @param $array
     * @return string
     */
    public static function createFolders( $array )
    {
        if( reset( $array ) !== 'Laraview' ) {
            array_unshift( $array, 'Laraview' );
        }
        $path = app_path();
        while( $array ) {
            $path .= '/' . array_shift( $array );
            if( ! file_exists( $path ) ) {
                mkdir( $path, 0655 );
            }
        }
        return $path;
    }

    /**
     * @param $name
     * @param string $append
     * @return string
     */
    public static function convertNameToClassName( $name, $append = 'Tabs' )
    {
        return ucfirst( camel_case( str_slug( $name, '_' ) ) ) . $append;
    }

    /**
     * @param string $append
     * @return string
     */
    public static function stubsDir( $append = '' )
    {
        return __DIR__ . '/../../../../stubs/' . ltrim( $append, '/' );
    }

    /**
     * @param string $append
     * @return string
     */
    public static function appNamespace( $append = '' )
    {
        /** @noinspection PhpUndefinedMethodInspection */
        return Container::getInstance()->getNamespace() . ltrim( $append, '\\' );
    }

}
