<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Laraview\Libs\Traits\FilePropertyEditor;

class LaraviewGenerateView extends Command
{

    use FilePropertyEditor;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'laraview:view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a View class to register to Laraview';

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
        $name = $this->askForNameOfView();
        $path = $this->askForDotNotationPath();
        $view = $this->createTempView( $name );
        $this->generate( $view, $path );
    }

    /**
     * @param $inputName
     * @return object
     */
    protected function createTempView( $inputName )
    {
        $viewClassName = self::nameToClassName( $inputName, 'View' );
        $namespaceWithoutClassName = self::appNamespace( 'Laraview\\' . self::nameToClassName( $inputName ) );
        $namespaceWithClassName = "{$namespaceWithoutClassName}\\" . $viewClassName;
        $fileName = app_path( str_replace( '\\', DIRECTORY_SEPARATOR, preg_replace( '/^' . preg_quote( self::appNamespace(), '/' ) . '/', '', $namespaceWithClassName ) ) ) . '.php';
        return (object) [
            'inputName' => $inputName,
            'nameToClassFormat' => self::nameToClassName( $inputName, '' ),
            'namespaceWithClassName' => $namespaceWithClassName,
            'namespaceWithoutClassName' => $namespaceWithoutClassName,
            'className' => $viewClassName,
            'fileName' => $fileName,
            'folder' => dirname( $fileName ),
            'isLocal' => true,
        ];
    }

    /**
     * @param $view
     * @param $dotNotationPath
     */
    private function generate( $view, $dotNotationPath )
    {
        $contents = $this->getContent( $view, $dotNotationPath );
        $this->createRelatedFoldersFor( $view->nameToClassFormat );
        $this->createTemplate( $view->folder . DIRECTORY_SEPARATOR . 'template.blade.php' );
        file_put_contents( $view->fileName, $contents );
        $this->info( "{$view->fileName} created!" );
    }

    /**
     * @param $view
     * @param $dotNotationPath
     * @return mixed
     */
    protected function getContent( $view, $dotNotationPath )
    {
        $file = file_get_contents( __DIR__ . '/../../../stubs/view.stub' );
        return str_replace( [
            '[NAMESPACE]',
            '[CLASS_NAME]',
            '[DOT_NOTATION_PATH]',
        ], [
            $view->namespaceWithoutClassName,
            $view->className,
            $dotNotationPath
        ], $file );
    }

    /**
     * @param string $folderName
     */
    private function createRelatedFoldersFor( string $folderName )
    {
        $folders = [
            app_path( 'Laraview' ),
            app_path( "Laraview/{$folderName}" ),
            app_path( "Laraview/{$folderName}/Regions" )
        ];
        foreach( $folders as $folder ) {
            if( ! file_exists( $folder ) ) {
                mkdir( $folder, 0655 );
            }
        }
    }

    /**
     * @param $templateFileName
     */
    private function createTemplate( $templateFileName )
    {
        file_put_contents(
            $templateFileName,
            file_get_contents( __DIR__ . '/../../../stubs/template.stub' )
        );
    }

    /**
     * @return mixed
     */
    protected function askForDotNotationPath()
    {
        return $this->ask( 'What would you like the dot-notation path of your view to be when generated (pages.edit.view)?' );
    }

    /**
     * @return mixed
     */
    protected function askForNameOfView()
    {
        return $this->ask( 'What is the name of your view (singular)?' );
    }

}
