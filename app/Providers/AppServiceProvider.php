<?php

namespace Laraview\Providers;

use Illuminate\Support\ServiceProvider;
use Laraview\Console\Commands\LaraviewCompiler;
use Laraview\Libs\Blueprints\RegisterBlueprint;
use Laraview\Libs\Demo\PagesEditView;
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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands([
            LaraviewCompiler::class
        ]);
    }
}
