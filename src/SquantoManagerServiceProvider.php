<?php

namespace Thinktomorrow\Squanto;

use Illuminate\Support\ServiceProvider;

class SquantoManagerServiceProvider extends ServiceProvider
{
    protected $defer = false;

    /**
     * @return array
     */
    public function provides()
    {
        return [];
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (! $this->app->routesAreCached()) {
            require __DIR__ . '/Manager/routes/web.php';
        }

        // Register squanto viewfiles under squanto:: namespace
        // allow to override them by making a view file under the resources/views/squanto location
        $this->loadViewsFrom(realpath(__DIR__ . '/Manager/views'), 'squanto');

        $this->publishes([
            realpath(__DIR__ . '/Manager/views') => base_path('resources/views/vendor/squanto'),
        ],'views');

    }

    /**
     * Register our translator
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
