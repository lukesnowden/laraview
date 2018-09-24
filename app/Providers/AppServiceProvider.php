<?php

namespace Laraview\Providers;

use Illuminate\Support\ServiceProvider;
use Laraview\Console\Commands\LaraviewCompiler;
use Laraview\Console\Commands\LaraviewGenerateElement;
use Laraview\Console\Commands\LaraviewGenerateRegion;
use Laraview\Console\Commands\LaraviewGenerateView;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Laraview\Libs\Register;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton( RegisterBlueprint::class, Register::class );
        $this->app[ RegisterBlueprint::class ]->viewComposer();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            LaraviewCompiler::class,
            LaraviewGenerateView::class,
            LaraviewGenerateRegion::class,
            LaraviewGenerateElement::class,
        ]);
    }
}
