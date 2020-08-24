<?php

return [

    /**
     * Allowed locales to be managed.
     * It expects these locales to match the translation folder names
     *
     * @var array
     */
    'locales' => ['nl', 'en'],

    /**
     * Exclude following lang groups from import
     * Here you list all translations that should be maintained by the developer
     * @var array
     */
    'excluded_files' => ['auth','pagination','passwords','validation','app'],

    /**
     * Paragraphize
     *
     * By default the redactor editor uses <p> tags for each line.
     * With this setting, squanto will interpret <p></p> as <br> on write changes.
     * Note that this can be overruled by the allowed html settings of a translation record
     */
    'paragraphize' => false,

    /**
     * In case the translation key cannot be translated, this option
     * allows to display null instead of the key itself. This differs
     * from native Laravel behaviour where always the key is returned.
     */
    'key_as_default' => true,

    /**
     * Master template filepath.
     * This is relative to the default viewpath.
     * @var string
     */
    'template' => 'squanto::_layouts.master',

    /**
     * Path where the laravel language files are stored
     * Default is the /resources/lang folder
     * @var string
     */
    'lang_path' => base_path('resources/lang'),

    /**
     * Path where the cached language files should be stored. Note that this entire folder
     * will be built up from scratch on each cache refresh.
     * @var string
     */
    'cache_path' => storage_path('app/lang'),

    /**
     * The folder where the squanto metadata are located. These files
     * contain options for the admin presentation of the squanto fields.
     */
    'metadata_path' => base_path('resources/squanto_metadata'),

];
