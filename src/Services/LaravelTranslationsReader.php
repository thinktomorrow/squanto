<?php

namespace Thinktomorrow\Squanto\Services;

use Illuminate\Support\Arr;
use League\Flysystem\Filesystem;

class LaravelTranslationsReader
{
    /**
     * @var \Illuminate\Support\Collection
     */
    private $translations;

    /**
     * Local filesystem. Already contains the path to our translation files
     * e.g. storage/app/trans
     *
     * @var Filesystem
     */
    private $filesystem;
    private $path;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->path = app('squanto.lang_path');
        $this->translations = collect();
    }

    /**
     * Read the lang files for a specific locale
     *
     * @param $locale
     * @param array $excluded filenames that should be excluded from the read
     * @return \Illuminate\Support\Collection
     */
    public function read($locale, array $excluded = [])
    {
        // Empty our translations for a new read
        $this->translations = collect();

        $files = $this->filesystem->listContents($locale);

        foreach($files as $file)
        {
            if(in_array($file['filename'],$excluded)) continue;

            $this->translations[$file['filename']] = require $this->path . DIRECTORY_SEPARATOR . $file['path'];
        }

        return $this;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function get()
    {
        return $this->translations;
    }

    /**
     * Flatten per file a multi-dimensional associative array with dots.

     * @return \Illuminate\Support\Collection
     */
    public function flattenPerFile()
    {
        return $this->translations->map(function ($values) {
            return Arr::dot($values);
        });
    }

    /**
     * Flatten all files which also flattens the filenames (groups) with dots.
     * Note: This will remove any empty language files
     *
     * @return \Illuminate\Support\Collection
     */
    public function flatten()
    {
        return $this->translations->map(function($values,$groupkey){
            return $this->prependGroupkey($values,$groupkey);
        })->collapse();
    }

    /**
     * @param $values
     * @param $groupkey
     * @return array
     */
    private function prependGroupkey($values,$groupkey)
    {
        $values = Arr::dot($values);
        $combined = [];

        foreach ($values as $key => $value) {

            // Empty arrays will be ignored in our flattening
            if(is_array($value) && empty($value)) continue;

            $combined[$groupkey . '.' . $key] = $value;
        }

        return $combined;
    }
}
