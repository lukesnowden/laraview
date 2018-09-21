<?php

namespace Laraview\Libs;

use Laraview\Libs\Blueprints\ViewBlueprint;
use Laraview\Libs\Blueprints\RegisterBlueprint;

class Register implements RegisterBlueprint
{

    /**
     * @var array
     */
    protected $views = [];

    /**
     * @var null
     */
    protected $console = null;

    /**
     * @param $object
     * @return $this
     */
    public function console( $object )
    {
        $this->console = $object;
        return $this;
    }

    /**
     * @param ViewBlueprint $view
     * @return $this
     */
    public function attachView( ViewBlueprint $view ) : self
    {
        $this->views[ get_class( $view ) ] = $view;
        return $this;
    }

    /**
     * @param $view
     * @return ViewBlueprint
     */
    public function getView( $view ) : ViewBlueprint
    {
        return $this->views[ $view ];
    }

    /**
     * @return void
     */
    public function generate()
    {
        foreach( $this->views as $view ) {
            $path = $this->createFileIfNotExists( $view->path() );
            $this->saveToFile( $view->render(), $path );
            $this->console->info( "{$path} generated..." );
        }
        $message = count( $this->views ) ? ( count( $this->views ) > 1 ? count( $this->views ) . ' views generated@' : '1 view generated!' ) : 'no views generated.';
        $this->console->info( $message );
    }

    /**
     * @param $dotNotationPath
     * @return string
     */
    protected function createFileIfNotExists( $dotNotationPath )
    {
        $segments = explode( '.', $dotNotationPath );
        $path = resource_path( "views" );
        foreach( $segments as $key => $segment ) {
            $path .= DIRECTORY_SEPARATOR . $segment;
            if( ! isset( $segments[ $key + 1 ] ) ) {
                $path .= '.blade.php';
                touch( $path );
            } else {
                if( ! file_exists( $path ) ) {
                    mkdir( $path, 0655 );
                }
            }
        }
        return $path;
    }

    /**
     * @param $contents
     * @param $path
     */
    protected function saveToFile( $contents, $path )
    {
        $beautifier = new BeautifyHtml( [
            'indent_inner_html' => false,
            'indent_char' => "\t",
            'indent_size' => 1,
            'wrap_line_length' => 32786,
            'unformatted' => ['code', 'pre'],
            'preserve_newlines' => false,
            'max_preserve_newlines' => 32786,
            'indent_scripts'	=> 'normal'
        ] );

        file_put_contents( $path, str_replace( [ '@extends(', '@section(', '@endsection' ], [ "\n@extends(", "\n@section(", "\n@endsection" ], $beautifier->beautify( $contents ) ) );

    }

    /**
     * @return array
     */
    public function regions()
    {
        $regions = [];
        foreach( $this->views as $view ) {
            $regions = $regions + array_keys( $view->regions() );
        }
        return $regions;
    }

    /**
     * @param $region
     * @return mixed
     */
    public function getViewForRegion( $region )
    {
        foreach( $this->views as $view ) {
            foreach( $view->regions() as $regionName => $regionObject ) {
                if( $regionName === $region ) {
                    return $view;
                }
            }
        }
    }

}
