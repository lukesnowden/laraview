<?php

namespace Laraview\Providers;

use Illuminate\Support\ServiceProvider;
use Laraview\Laraview\CustomerEdit\CustomerEditView;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Laraview\Libs\Demo\Event\SurnameTextElement;
use Laraview\Libs\Demo\GeneralTabRegion;

class DemoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted( function( $app ) {
            $app[ RegisterBlueprint::class ]->attachView( new CustomerEditView );
        });

        //app( 'events' )->listen( GeneralTabRegion::class . '.attached', function( $region ) {
        //    $region->insertElement( SurnameTextElement::class );
        //});
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
