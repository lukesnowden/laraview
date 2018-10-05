<?php

namespace Laraview\Libs\Traits;

use Laraview\Libs\Blueprints\RegisterBlueprint;

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
        event( $event . '.before', [ compact( 'model', 'request' ) ] );

        // save the new customer to the database
        $model->save();

        // notify application of saved
        event( $event . '.after', [ compact( 'model', 'request' ) ] );

    }

}
