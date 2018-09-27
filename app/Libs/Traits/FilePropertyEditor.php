<?php

namespace Laraview\Libs\Traits;

use Exception;
use Illuminate\Container\Container;
use Laraview\Libs\Layouts\Tabs\GenerationSupport;
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
        if( preg_match( '/^' . preg_quote( GenerationSupport::appNamespace(), '/' ) . '/', $class ) ) {
            return true;
        }
        return false;
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
        return preg_match( '/^' . preg_quote( Container::getInstance()->getNamespace(), '/' ) . '/', $namespace );
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
        return GenerationSupport::appNamespace() . $append;
    }

}
