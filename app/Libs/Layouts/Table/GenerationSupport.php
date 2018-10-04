<?php

namespace Laraview\Libs\Layouts\Table;

use Laraview\Console\Commands\LaraviewGenerateLayout;

class GenerationSupport extends \Laraview\Libs\Layouts\Tabs\GenerationSupport
{

    /**
     * @var LaraviewGenerateLayout
     */
    protected $console;

    /**
     * @var
     */
    protected $region;

    public function create()
    {
        $name = $this->askForNewTableLayoutName();
        $columnCount = $this->askForHowManyColumns();
        $columnNames = $this->askForAllColumnNames( $columnCount );
        $tableLayout = $this->createTempTableLayout( $name );
        $columns = $this->createTempTableColumns( $name, $columnNames );
        $contents = $this->getContents( $tableLayout, $columns );
        $this->createFolder( $tableLayout->folder );
        file_put_contents( $tableLayout->fileName, $contents );

        foreach( $columns as $column ) {
            $contents = $this->getColumnContents( $column );
            $this->createFolder( $column->folder );
            file_put_contents( $column->fileName, $contents );
        }

        $this->importLayoutToRegion( $tableLayout );
        return $tableLayout->fileName;

    }

    /**
     * @param $column
     * @return mixed
     */
    protected function getColumnContents( $column )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[HEADER]',
                '[ROW_KEY]',
                '[CLASS_NAME]',
            ],
            [
                $column->namespaceWithoutClassName,
                ucwords( $column->inputName ),
                str_slug( $column->inputName, '_' ),
                $column->className,
            ],
            file_get_contents( self::stubsDir( 'layouts/table/column.stub' ) )
        );
    }

    /**
     * @param $tableLayout
     * @param $columns
     * @return mixed
     */
    protected function getContents( $tableLayout, $columns )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[COLUMNS_IMPORT]',
                '[CLASS_NAME]',
                '[COLUMNS]',
            ],
            [
                $tableLayout->namespaceWithoutClassName,
                $this->tabDataSetToImportString( $columns ),
                $tableLayout->className,
                $this->tabDataSetToArrayDefinition( $columns )
            ],
            file_get_contents( self::stubsDir( 'layouts/table/table.stub' ) )
        );
    }

    /**
     * @param $inputName
     * @param $columnNames
     * @return array
     */
    public function createTempTableColumns( $inputName, $columnNames )
    {
        $columns = [];
        foreach( $columnNames as $columnName ) {
            $tableClassNameShort = self::nameToClassName( $inputName, '' );
            $columnClassName = self::nameToClassName( $columnName, 'Column' );
            if( $this->region->isLocal ) {
                $columns[] = $this->createLocalTempColumn( $columnName, $tableClassNameShort, $columnClassName );
            } else {
                $columns[] = $this->createExternalTempColumn( $columnName, $tableClassNameShort, $columnClassName );
            }
        }
        return $columns;
    }

    /**
     * @param $inputName
     * @param $tableClassNameShort
     * @param $columnClassName
     * @return object
     */
    protected function createLocalTempColumn( $inputName, $tableClassNameShort, $columnClassName )
    {
        $fileName = $this->region->folder . "{$this->region->nameToClassFormat}" . DIRECTORY_SEPARATOR . "Layouts" . DIRECTORY_SEPARATOR . "{$tableClassNameShort}" . DIRECTORY_SEPARATOR . "Columns" . DIRECTORY_SEPARATOR . "{$columnClassName}.php";
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts\\{$tableClassNameShort}\Columns";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$columnClassName}";
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $columnClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $inputName
     * @param $tableClassNameShort
     * @param $columnClassName
     * @return object
     */
    protected function createExternalTempColumn( $inputName, $tableClassNameShort, $columnClassName )
    {
        $namespaceWithoutClassName = $this->getGlobalRegionPrefix( $this->region->namespaceWithClassName ) . "\Layouts\\{$tableClassNameShort}\Columns";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$columnClassName}";
        $fileName = $this->localNamespaceToFileName( $namespaceWithClassName );
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $columnClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = true;

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $columnCount
     * @return array
     */
    public function askForAllColumnNames( $columnCount )
    {
        $tableNames = [];
        for( $x = 1; $x <= $columnCount; $x++ ) {
            $tableNames[] = $this->askForColumnName( $x );
        }
        return $tableNames;
    }

    /**
     * @param $inputName
     * @return object
     */
    protected function createTempTableLayout( $inputName )
    {
        $tableClassName = self::nameToClassName( $inputName, 'Table' );
        if( $this->region->isLocal ) {
            return $this->createLocalTempTable( $inputName, $tableClassName );
        }
        return $this->createExternalTempTable( $inputName, $tableClassName );
    }

    /**
     * @param $inputName
     * @param $tableClassName
     * @return object
     */
    protected function createLocalTempTable( $inputName, $tableClassName )
    {
        $fileName = $this->region->folder . "{$this->region->nameToClassFormat}" . DIRECTORY_SEPARATOR . "Layouts" . DIRECTORY_SEPARATOR . "{$tableClassName}.php";
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$tableClassName}";
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $tableClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $inputName
     * @param $tableClassName
     * @return object
     */
    protected function createExternalTempTable( $inputName, $tableClassName )
    {
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$tableClassName}";
        $fileName = $this->localNamespaceToFileName( $namespaceWithClassName );
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $tableClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @return mixed
     */
    protected function askForNewTableLayoutName()
    {
        $name = $this->console->ask( "What would you like this table layout to be called?" );
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
    protected function askForColumnName( $tabIndex )
    {
        $name = $this->console->ask( "What name would you like to give column {$tabIndex}" );
        if( strlen( $name ) < 2 ) {
            $this->console->error( "Please provide a name longer than 2 characters" );
            return $this->askForTabNames( $tabIndex );
        }
        if( is_numeric( $name[ 0 ] ) ) {
            $this->console->error( "Please provide a name that begins with a letter." );
            return $this->askForTabNames( $tabIndex );
        }
        return $name;
    }

    /**
     * @return int
     */
    protected function askForHowManyColumns() : int
    {
        return (int) $this->console->ask( "How many columns would you like to start with?" );
    }

}
