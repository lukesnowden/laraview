<?php

namespace Laraview\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;

class LaraviewGenerateView extends Command
{

    use DetectsApplicationNamespace;

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
        $name = $this->ask( 'What is the name of your view (singular)?' );
        $path = $this->ask( 'What would you like the dot-notation path of your view to be when generated (pages.edit.view)?' );
        $this->createFolders();
        $this->generate( $name, $path );
    }

    /**
     * @param $name
     * @param $path
     */
    private function generate( $name, $path )
    {
        $folderName = ucfirst( camel_case( preg_replace( '/[^\d\w]/', '_', $name ) ) );
        $file = str_replace( [
            '[NAMESPACE]',
            '[VIEW_CLASS_NAME]',
            '[PATH]',
            '[BASE_VIEW_PATH]',
            '[FOLDER_NAME]'
        ], [
            $this->getAppNamespace(),
            $folderName . 'View',
            $path,
            'template.blade.php',
            $folderName
        ], file_get_contents( base_path( 'stubs/view.stub' ) ) );
        $this->createRelatedFoldersFor( $folderName );
        $this->createTemplate( $folderName );
        $filePath = app_path( "Laraview/{$folderName}/{$folderName}View.php" );
        file_put_contents( $filePath, $file );
        $this->info( "{$filePath} created!" );
    }

    /**
     * @return void
     */
    private function createFolders()
    {
        if( ! file_exists( app_path( 'Laraview' ) ) ) {
            mkdir( app_path( 'Laraview' ), 0655 );
        }
    }

    /**
     * @param string $folderName
     */
    private function createRelatedFoldersFor( string $folderName )
    {
        $folders = [
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
     * @param $folderName
     */
    private function createTemplate( $folderName )
    {
        file_put_contents(
            app_path( "Laraview/{$folderName}/template.blade.php" ),
            file_get_contents( base_path( 'stubs/template.stub' ) )
        );
    }
}
