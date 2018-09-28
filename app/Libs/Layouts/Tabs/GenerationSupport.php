<?php

namespace Laraview\Libs\Layouts\Tabs;

use Laraview\Console\Commands\LaraviewGenerateLayout;
use Laraview\Libs\Traits\FilePropertyEditor;

class GenerationSupport
{

    use FilePropertyEditor;

    /**
     * @var
     */
    protected $console;

    /**
     * @var
     */
    protected $region;

    /**
     * GenerationSupport constructor.
     * @param $region
     * @param LaraviewGenerateLayout $console
     */
    public function __construct( $region, LaraviewGenerateLayout $console )
    {
        $this->console = $console;
        $this->region = $region;
        $this->region->nameToClassFormat = preg_replace( '/Region$/', '', $this->region->className );
    }

    /**
     * @return mixed
     */
    public function create()
    {
        $name = $this->askForNewTabsLayoutName();
        $tabCount = $this->askHowManyTabs();
        $tabNames = $this->askForAllTabNames( $tabCount );
        $tabLayout = $this->createTempTabsLayout( $name );
        $tabs = $this->createTempTabs( $name, $tabNames );
        $contents = $this->getContents( $tabLayout, $tabs );
        $this->createFolder( $tabLayout->folder );
        file_put_contents( $tabLayout->fileName, $contents );

        foreach( $tabs as $tab ) {
            $contents = $this->getTabContents( $tab );
            $this->createFolder( $tab->folder );
            file_put_contents( $tab->fileName, $contents );
        }

        $this->importLayoutToRegion( $tabLayout );
        return $tabLayout->fileName;
    }

    /**
     * @param $tabLayout
     */
    protected function importLayoutToRegion( $tabLayout )
    {
        if( $this->region->isLocal ) {
            $this->addToClassesArray( $this->region->fileName, '$elements', function( &$rebuild ) use ( $tabLayout ) {
                $rebuild[] = [ 319, "\\{$tabLayout->namespaceWithClassName}", 21 ];
                $rebuild[] = [ 387, '::', 21 ];
                $rebuild[] = [ 361, 'class', 21 ];
            } );
        }
    }

    /**
     * @param $tab
     * @return mixed
     */
    protected function getTabContents( $tab )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[NAME]',
                '[CLASS_NAME]',
            ],
            [
                $tab->namespaceWithoutClassName,
                $tab->inputName,
                $tab->className,
            ],
            file_get_contents( self::stubsDir( 'layouts/tabs/tab.stub' ) )
        );
    }

    /**
     * @param $tabLayout
     * @param $tabs
     * @return mixed
     */
    protected function getContents( $tabLayout, $tabs )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[TABS_IMPORT]',
                '[CLASS_NAME]',
                '[TABS]',
            ],
            [
                $tabLayout->namespaceWithoutClassName,
                $this->tabDataSetToImportString( $tabs ),
                $tabLayout->className,
                $this->tabDataSetToArrayDefinition( $tabs )
            ],
            file_get_contents( self::stubsDir( 'layouts/tabs/tabs.stub' ) )
        );
    }

    /**
     * @param $tabs
     * @return string
     */
    protected function tabDataSetToArrayDefinition( $tabs )
    {
        $string = '';
        foreach( $tabs as $tab ) {
            $string .= "{$tab->className}::class,\n\t\t";
        }
        return rtrim( $string );
    }

    /**
     * @param $tabs
     * @return string
     */
    protected function tabDataSetToImportString( $tabs )
    {
        $string = '';
        foreach( $tabs as $tab ) {
            $string .= "use {$tab->namespaceWithClassName};\n";
        }
        return rtrim( $string );
    }

    /**
     * @param $inputName
     * @return object
     */
    protected function createTempTabsLayout( $inputName )
    {
        $tabsClassName = self::nameToClassName( $inputName, 'Tabs' );
        if( $this->region->isLocal ) {
            return $this->createLocalTempTabs( $inputName, $tabsClassName );
        }
        return $this->createExternalTempTabs( $inputName, $tabsClassName );
    }

    /**
     * @param $inputName
     * @param $tabNames
     * @return array
     */
    protected function createTempTabs( $inputName, $tabNames )
    {
        $tabs = [];
        foreach( $tabNames as $tabName ) {
            $tabsClassNameShort = self::nameToClassName( $inputName, '' );
            $tabClassName = self::nameToClassName( $tabName, 'Tab' );
            if( $this->region->isLocal ) {
                $tabs[] = $this->createLocalTempTab( $tabName, $tabsClassNameShort, $tabClassName );
            } else {
                $tabs[] = $this->createExternalTempTab( $tabName, $tabsClassNameShort, $tabClassName );
            }
        }
        return $tabs;
    }

    /**
     * @param $inputName
     * @param $tabsClassNameShort
     * @param $tabClassName
     * @return object
     */
    protected function createLocalTempTab( $inputName, $tabsClassNameShort, $tabClassName )
    {
        $fileName = $this->region->folder . "{$this->region->nameToClassFormat}" . DIRECTORY_SEPARATOR . "Layouts" . DIRECTORY_SEPARATOR . "{$tabsClassNameShort}" . DIRECTORY_SEPARATOR . "Tabs" . DIRECTORY_SEPARATOR . "{$tabClassName}.php";
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts\\{$tabsClassNameShort}\Tabs";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$tabClassName}";
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $tabClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $inputName
     * @param $tabsClassNameShort
     * @param $tabClassName
     * @return object
     */
    protected function createExternalTempTab( $inputName, $tabsClassNameShort, $tabClassName )
    {
        $namespaceWithoutClassName = $this->getGlobalRegionPrefix( $this->region->namespaceWithClassName ) . "\Layouts\\{$tabsClassNameShort}\Tabs";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$tabClassName}";
        $fileName = $this->localNamespaceToFileName( $namespaceWithClassName );
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $tabClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = true;

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $inputName
     * @param $tabsClassName
     * @return object
     */
    protected function createLocalTempTabs( $inputName, $tabsClassName )
    {
        $fileName = $this->region->folder . "{$this->region->nameToClassFormat}" . DIRECTORY_SEPARATOR . "Layouts" . DIRECTORY_SEPARATOR . "{$tabsClassName}.php";
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$tabsClassName}";
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $tabsClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $inputName
     * @param $tabsClassName
     * @return object
     */
    protected function createExternalTempTabs( $inputName, $tabsClassName )
    {
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$tabsClassName}";
        $fileName = $this->localNamespaceToFileName( $namespaceWithClassName );
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $tabsClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );

    }

    /**
     * @return int
     */
    protected function askHowManyTabs()
    {
        return (int) $this->console->ask( "How many tabs would you like to initially create?" );
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
    protected function askForNewTabsLayoutName()
    {
        $name = $this->console->ask( "What would you like this tabs layout to be called?" );
        if( strlen( $name ) < 5 ) {
            $this->console->error( "Please provide a name longer than 5 characters" );
            return $this->askForNewTabsLayoutName();
        }
        if( is_numeric( $name[ 0 ] ) ) {
            $this->console->error( "Please provide a name that begins with a letter." );
            return $this->askForNewTabsLayoutName();
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
     * @param string $append
     * @return string
     */
    public static function stubsDir( $append = '' )
    {
        return __DIR__ . '/../../../../stubs/' . ltrim( $append, '/' );
    }

}
