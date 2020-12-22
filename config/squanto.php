<?php

return [

    /**
     * The locales accessible for squanto. They are the translations that will be managed
     * via the database. These locales need to match the language folder names
     *
     * @var array
     */
    'locales' => ['nl', 'en'],

    /**
     * The language files not accessible for squanto. Here you can exclude translation
     * files from being managed by squanto. Another way of looking: these translation
     * files will only be accessed by the developer
     *
     * @var array
     */
    'excluded_files' => ['auth','pagination','passwords','validation','app', 'routes'],

    /**
     * Return null instead of the translation key when a translation isn't found.`
     * Out of the box, Laravel returns the translation key itself in case the value
     * could not be retrieved. Squanto allows to choose this behavior. Set this
     * option to false to return null instead of the key when a value isn't found.
     */
    'key_as_default' => true,

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
     * contain the info and settings for the squanto admin fields.
     */
    'metadata_path' => base_path('resources/squanto_metadata'),

    /**
     * Master template filepath.
     * This is relative to the default viewpath.
     * @var string
     */
    'template' => 'squanto::_layouts.master',

    /**
     * Paragraphize
     *
     * By default the redactor editor uses <p> tags for each line.
     * With this setting, squanto will interpret <p></p> as <br> on write changes.
     * Note that this can be overruled by the allowed html settings of a translation record
     */
    'paragraphize' => false,

];
