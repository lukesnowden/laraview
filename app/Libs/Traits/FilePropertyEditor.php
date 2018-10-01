<?php

namespace Laraview\Libs\Traits;

use Exception;
use Illuminate\Container\Container;
use ReflectionClass;
use ReflectionException;

trait FilePropertyEditor
{

    /**
     * @param string $class
     * @return bool
     */
    public function ableToEdit( string $class )
    {
        if( preg_match( '/^' . preg_quote( self::appNamespace(), '/' ) . '/', $class ) ) {
            return true;
        }
        return false;
    }

    /**
     * @param $namespace
     * @return string
     */
    protected function localNamespaceToFileName( $namespace )
    {
        return app_path( str_replace( '\\', DIRECTORY_SEPARATOR, preg_replace( '/^' . preg_quote( self::appNamespace(), '\\' ) . '/', '', $namespace ) ) ) . '.php';
    }

    /**
     * @param $namespace
     * @return null|string|string[]
     */
    protected function getGlobalRegionPrefix( $namespace )
    {
        $segments = explode( '\\', $namespace );
        $segments[0] = rtrim( self::appNamespace(), '\\' );
        $string = implode( '\\', $segments );
        foreach( [ 'Region', 'View', 'Element', 'Tab', 'Layout' ] as $append ) {
            $string = preg_replace( '/' . preg_quote( $append ) . '$/', '', $string );
        }
        return $string;
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
                mkdir( $path, 0755, 'R' );
                return $this->createFolder( $orig, null );
            }
        } catch( Exception $e ) {
            if( $e->getMessage() === 'mkdir(): No such file or directory' ) {
                if( ! preg_match( '/^' . preg_quote( app_path(), '/' ) . '/', $path ) ) {
                    die( "This has gone too far!!" );
                }
                return $this->createFolder( dirname( $path ), is_null( $orig ) ? $path : $orig );
            }
        }
    }

    /**
     * @param $file
     * @param $variable
     * @param $callback
     * @return null
     */
    protected function addToClassesArray( $file, $variable, $callback )
    {
        if( ! file_exists( $file ) ) {
            return null;
        }
        $tokens = token_get_all( file_get_contents( $file ) );
        $protectedCheck = false;
        $nameCheck = false;
        $isEmptyArray = true;

        $rebuild = [];

        foreach( $tokens as $position => $token ) {
            if( is_array( $token ) ) {
                if( token_name( $token[ 0 ] ) === 'T_PROTECTED' ) {
                    $protectedCheck = true;
                }
                if( token_name( $token[ 0 ] ) === 'T_VARIABLE' && $token[ 1 ] === $variable && $protectedCheck === true ) {
                    $nameCheck = true;
                }
                if( token_name( $token[ 0 ] ) === 'T_DOUBLE_COLON' && $protectedCheck === true && $nameCheck === true ) {
                    $isEmptyArray = false;
                }
            } else {
                if( $token === ']' && $protectedCheck === true && $nameCheck === true ) {
                    $rebuild = $this->trimBack( $rebuild );
                    if( ! $isEmptyArray ) {
                        $rebuild[] = ',';
                    }
                    $rebuild[] = [ 382, "\n", 21 ];
                    $rebuild[] = [ 382, "\t", 21 ];
                    $rebuild[] = [ 382, "\t", 21 ];

                    $callback( $rebuild );

                    $rebuild[] = [ 382, "\n", 21 ];
                    $rebuild[] = [ 382, "\t", 21 ];
                }
            }
            $rebuild[] = $token;
        }

        $source = $this->reBuildBackToSource( $rebuild );
        file_put_contents( $file, $source );

    }

    /**
     * @param $tokens
     * @return string
     */
    public function reBuildBackToSource( $tokens )
    {
        $source = '';
        foreach( $tokens as $token ) {
            if( is_array( $token ) ) {
                $source .= $token[ 1 ];
            } else {
                $source .= $token;
            }
        }
        return $source;
    }

    /**
     * @param array $rebuild
     * @return array
     */
    private function trimBack( array $rebuild )
    {
        $rebuild = array_reverse( $rebuild );
        foreach( $rebuild as $key => $token ) {
            if( is_array( $token ) ) {
                if( $token[ 0 ] === 382 ) {
                    unset( $rebuild[ $key ] );
                    continue;
                }
                return array_reverse( $rebuild );
            } else {
                if( $token === ',' ) {
                    unset( $rebuild[ $key ] );
                    continue;
                }
            }
            return array_reverse( $rebuild );
        }
    }

    /**
     * @param $namespace
     * @return null|string
     */
    public static function getFilenameFromNamespace( $namespace )
    {
        try {
            return ( new ReflectionClass( $namespace ) )->getFileName();
        } catch( \ReflectionException $e ) {
            return null;
        }
    }

    /**
     * @param $file
     * @return null
     */
    public static function getNamespaceFromFilename( $file )
    {
        if( $data = file_get_contents( $file ) ) {
            if ( preg_match( '#^namespace\s+(.+?);$#sm', $data, $match ) ) {
                return $match[ 1 ];
            }
            return null;
        }
        return null;
    }

    /**
     * @param $namespace
     * @return false|int
     */
    public static function isLocalNamespace( $namespace )
    {
        return preg_match( '/^' . preg_quote( self::appNamespace(), '/' ) . '/', $namespace );
    }

    /**
     * @param $namespace
     * @return object
     * @throws \Exception
     */
    public static function getClassDetails( $namespace )
    {
        try {
            $reflection = new ReflectionClass( $namespace );
            return (object)[
                'namespaceWithClassName' => $namespace,
                'namespaceWithoutClassName' => $reflection->getNamespaceName(),
                'className' => $reflection->getShortName(),
                'fileName' => $reflection->getFileName(),
                'folder' => dirname( $reflection->getFileName() ) . DIRECTORY_SEPARATOR,
                'isLocal' => self::isLocalNamespace( $namespace ),
            ];
        } catch( ReflectionException $e ) {
            throw new Exception( "Unable to reflect on class {$namespace}" );
        }
    }

    /**
     * @param $name
     * @param string $append
     * @return string
     */
    public static function nameToClassName( $name, $append = '' )
    {
        return ucfirst( camel_case( str_slug( $name, '_' ) ) ) . $append;
    }

    /**
     * @param string $append
     * @return string
     */
    public static function appNamespace( $append = '' )
    {
        return Container::getInstance()->getNamespace() . $append;
    }

}
