<?php

namespace Thinktomorrow\Squanto\Application\Rename;

use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;

final class RenameKeysInFiles
{
    /**
     * @var Filesystem
     */
    private $disk;
    private $paths;

    function __construct(Filesystem $disk)
    {
        $this->disk = $disk;
        $this->paths = array_merge(config('view.paths'), [app('path')]);
    }

    public function handle($oldKey, $newKey)
    {
        $occurrences = $this->getFilesContainingOldKey($oldKey);

        foreach($occurrences as $occurrence)
        {
            $this->renameKeysInFile($occurrence['path'], $occurrence['function'], $oldKey, $newKey);
        }
    }

    /*
     * Based on the work of Barryvdh\TranslationManager by Barry vd. Heuvel <barryvdh@gmail.com>
     *
     * https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
     */
    private function getFilesContainingOldKey($oldKey)
    {
        $functions = ['trans', 'trans_choice', 'Lang::get', 'Lang::choice', 'Lang::trans', 'Lang::transChoice', '@lang', '@choice'];

        $pattern =
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '('.implode('|', $functions).')'.// Must start with one of the functions
            "\(".// Match opening parentheses
            "[\'\"]".// Match " or '
            '('.// Start a new group to match:
            $oldKey. // match the oldKey
            ')'.// Close group
            "[\'\"]".// Closing quote
            "[\),]"  // Close parentheses or new parameter
        ;

        $occurrences = [];

        foreach($this->paths as $path)
        {
            foreach (Finder::create()->files()->in($path) as $file)
            {
                if (preg_match_all("/$pattern/siU", $file->getContents(), $matches)) {

                    for($i=0; $i < count($matches[0]);$i++)
                    {
                        $occurrences[] = [
                            'path' => $file->getRealPath(),
                            'function' => $matches[0][$i]
                        ];
                    }


                }
            }
        }


        return $occurrences;
    }

    private function renameKeysInFile($path, $function, $oldKey, $newKey)
    {
        $replace = str_replace($oldKey,$newKey,$function);

        $content = file_get_contents($path);
        $replacedContent = preg_replace("/$function/s", $replace, $content);

        file_put_contents($path,$replacedContent);
    }
}