<?php

namespace Thinktomorrow\Squanto;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SquantoManagerServiceProvider extends ServiceProvider
{
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
        include_once dirname(__FILE__) . "/Manager/helpers.php";

        if (! $this->app->routesAreCached()) {
            include __DIR__ . '/Manager/Http/routes.php';
        }

        // Register squanto viewfiles under squanto:: namespace
        // allow to override them by making a view file under the resources/views/vendor/squanto location
        $this->loadViewsFrom(realpath(__DIR__ . '/Manager/views'), 'squanto');

        Blade::componentNamespace('Thinktomorrow\\Squanto\\Manager\\Components', 'squanto');
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
