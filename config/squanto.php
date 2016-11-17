<?php

return [

    /**
     * Enable specific translation source
     * By default the cached translations will be fetched. If not found there the database is hit for each translation value.
     * Here you can choose to force a specific source of translations without the default cascade
     *
     * possible values are: null (default), cache, database, lang
     */
    'source' => null,

    /**
     * Allowed locales to be managed
     * @var array
     */
    'locales' => ['nl'],

    /**
     * Exclude following lang groups from import
     * Here you list all translations that should be maintained by the developer
     * @var array
     */
    'excluded_files' => ['auth','pagination','passwords','validation','app'],

    /**
     * In case the translation key cannot be translated, this option
     * allows to display null instead of the key itself. This differs
     * from native Laravel behaviour where always the key is returned.
     */
    'key_as_default' => true,

    /**
     * Path where the laravel language files are stored
     * Default is the /resources/lang folder
     * @var string
     */
    'lang_path' => base_path('resources/lang'),

    /**
     * Path where the cached language files should be stored
     * @var string
     */
    'cache_path' => storage_path('app/lang'),

    /**
     * UI management settings
     *
     * It is advised to use custom routes to match your project settings
     * As a start you can copy/paste the routes found in /src/Manager/routes.php
     */
    'manager' => [
        'use_default_routes' => true,
        'master_layout' => 'admin._layouts.master',
    ],


];