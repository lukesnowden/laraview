<?php

namespace Laraview\Libs\Layouts\Modal;

class GenerationSupport extends \Laraview\Libs\Layouts\Tabs\GenerationSupport
{

    /**
     * @return mixed
     */
    public function create()
    {
        $name = $this->askForNewModalLayoutName();
        $modalLayout = $this->createModalTableLayout( $name );
        $contents = $this->getContents( $modalLayout, [] );
        $this->createFolder( $modalLayout->folder );
        file_put_contents( $modalLayout->fileName, $contents );

        $this->importLayoutToRegion( $modalLayout );
        return $modalLayout->fileName;
    }

    /**
     * @param $modalLayout
     * @param $dummy
     * @return mixed
     */
    protected function getContents( $modalLayout, $dummy )
    {
        return str_replace(
            [
                '[NAMESPACE]',
                '[CLASS_NAME]',
                '[NAME]',
            ],
            [
                $modalLayout->namespaceWithoutClassName,
                $modalLayout->className,
                ucwords( $modalLayout->inputName )
            ],
            file_get_contents( self::stubsDir( 'layouts/modal/modal.stub' ) )
        );
    }

    /**
     * @return mixed
     */
    protected function askForNewModalLayoutName()
    {
        $name = $this->console->ask( "What would you like this modal layout to be called?" );
        if( strlen( $name ) < 3 ) {
            $this->console->error( "Please provide a name longer than 3 characters" );
            return $this->askForNewModalLayoutName();
        }
        if( is_numeric( $name[ 0 ] ) ) {
            $this->console->error( "Please provide a name that begins with a letter." );
            return $this->askForNewModalLayoutName();
        }
        return $name;
    }

    /**
     * @param $inputName
     * @return object
     */
    protected function createModalTableLayout( $inputName )
    {
        $modalClassName = self::nameToClassName( $inputName, 'Modal' );
        if( $this->region->isLocal ) {
            return $this->createLocalTempTable( $inputName, $modalClassName );
        }
        return $this->createExternalTempTable( $inputName, $modalClassName );
    }

    /**
     * @param $inputName
     * @param string $modalClassName
     * @return object
     */
    protected function createLocalTempTable( $inputName, string $modalClassName )
    {
        $fileName = $this->region->folder . "{$this->region->nameToClassFormat}" . DIRECTORY_SEPARATOR . "Layouts" . DIRECTORY_SEPARATOR . "{$modalClassName}.php";
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$modalClassName}";
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $modalClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

    /**
     * @param $inputName
     * @param string $modalClassName
     * @return object
     */
    protected function createExternalTempTable( $inputName, string $modalClassName )
    {
        $namespaceWithoutClassName = $this->region->namespaceWithoutClassName . "\\{$this->region->nameToClassFormat}\Layouts";
        $namespaceWithClassName = $namespaceWithoutClassName . "\\{$modalClassName}";
        $fileName = $this->localNamespaceToFileName( $namespaceWithClassName );
        $nameToClassFormat = self::nameToClassName( $inputName, '' );
        $className = $modalClassName;
        $folder = dirname( $fileName ) . DIRECTORY_SEPARATOR;
        $isLocal = self::isLocalNamespace( $namespaceWithClassName );

        return (object) compact( 'inputName', 'nameToClassFormat', 'namespaceWithoutClassName', 'namespaceWithClassName', 'className', 'fileName', 'folder', 'isLocal' );
    }

}
