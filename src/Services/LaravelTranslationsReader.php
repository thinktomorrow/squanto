<?php

namespace Thinktomorrow\Squanto\Services;

use Illuminate\Support\Arr;
use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Exceptions\EmptyTranslationFileException;

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
        $this->path = $this->getSquantoLangPath();
        $this->translations = collect();
    }

    /**
     * Read the lang files for a specific locale
     *
     * @param $locale
     * @param array $excluded filenames that should be excluded from the read
     * @return self
     */
    public function readAll($locale, array $excluded = [])
    {
        // Empty our translations for a new read
        $this->translations = collect();

        $files = $this->filesystem->listContents($locale);

        foreach ($files as $file) {
            $filename = substr($file['path'], strrpos($file['path'], '/'));
            $filename = ltrim($filename, '/');
            $filename = str_replace('.php', '', $filename);

            if (in_array($filename, $excluded)) {
                continue;
            }

            $this->translations[$filename] = require $this->path . DIRECTORY_SEPARATOR . $file['path'];
        }

        $this->validateTranslations();

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
        return $this->translations->map(function ($values, $groupkey) {
            return $this->prependGroupkey($values, $groupkey);
        })->collapse();
    }

    /**
     * @param $values
     * @param $groupkey
     * @return array
     */
    private function prependGroupkey($values, $groupkey)
    {
        $values = Arr::dot($values);
        $combined = [];

        foreach ($values as $key => $value) {
            // Empty arrays will be ignored in our flattening
            if (is_array($value) && empty($value)) {
                continue;
            }

            $combined[$groupkey . '.' . $key] = $value;
        }

        return $combined;
    }

    private function getSquantoLangPath()
    {
        $path = config('squanto.lang_path');
        return is_null($path) ? app('path.lang') : $path;
    }

    public function validateTranslations()
    {
        $this->translations->each(function($translation, $key){
            if(!is_array($translation))
            {
                throw new EmptyTranslationFileException('The file "' . $key . '.php" seems empty. Make sure every lang file returns an array.');
            }
        });
    }
}
