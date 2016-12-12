<?php

return [

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
     * Paragraphize
     *
     * By default the redactor editor uses <p> tags for each line. With this setting you can
     * remove this behaviour. This also means that linebreaks are interpreted as <br>
     * Note that is a specific line has <p> as an allowed html element, this rule will not apply
     */
    'paragraphize' => false,

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

];