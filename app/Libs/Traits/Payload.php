<?php

namespace Laraview\Libs\Traits;

use Laraview\Libs\Blueprints\RegisterBlueprint;
use ReflectionException;
use ReflectionClass;

trait Payload
{

    /**
     * @param $model
     * @param $request
     * @param $view
     * @param $event
     */
    protected function dispatchPayload( $model, $request, $view, $event )
    {
        // send the data to all the elements
        app( RegisterBlueprint::class )->dispatchPayload( $view, $model, $request );

        // notify application of saving
        event( $this->getEventName( $event ), [ compact( 'model', 'request' ) ] );

        // save the new customer to the database
        $model->save();

        // notify application of saved
        event( $this->getEventName( $event, 'after' ), [ compact( 'model', 'request' ) ] );
    }

    /**
     * @param $eventName
     * @param string $position
     * @return string
     */
    protected function getEventName( $eventName, $position = 'before' )
    {
        return self::getEventNameFromClass( $this, $eventName, $position );
    }

    /**
     * @param $class
     * @param $eventName
     * @param string $position
     * @return string
     */
    public static function getEventNameFromClass( $class, $eventName, $position = 'before' )
    {
        try {
            $reflection = new ReflectionClass( $class );

            $classSimplified = str_slug( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $reflection->getShortName() ) );

            $methodSimplified = str_slug( preg_replace( '/([a-z])([A-Z])/', '$1-$2', $eventName ) );
        }
        catch( ReflectionException $e ) {
            dd( $e->getMessage() );
        }
        return "{$classSimplified}.{$methodSimplified}.{$position}";
    }

}
