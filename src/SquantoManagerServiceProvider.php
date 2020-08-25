<?php

namespace Thinktomorrow\Squanto;

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
        // Require helper files
        if(!function_exists('htmLawed')) {
            require_once dirname(__FILE__) . "/Manager/utils/vendors/htmlLawed.php";
        }

        require_once dirname(__FILE__) . "/Manager/helpers.php";

        if (! $this->app->routesAreCached()) {
            require __DIR__ . '/Manager/Http/routes.php';
        }

        // Register squanto viewfiles under squanto:: namespace
        // allow to override them by making a view file under the resources/views/vendor/squanto location
        $this->loadViewsFrom(realpath(__DIR__ . '/Manager/views'), 'squanto');
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
