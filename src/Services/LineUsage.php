<?php

namespace Thinktomorrow\Squanto\Services;

use Symfony\Component\Finder\Finder;

class LineUsage
{
    /**
     * @var Finder
     */
    private $finder;
    private $paths;

    function __construct(Finder $finder, array $paths)
    {
        $this->finder = $finder;
        $this->paths = $paths;
    }

    /*
     * Based on the work of Barryvdh\TranslationManager by Barry vd. Heuvel <barryvdh@gmail.com>
     *
     * https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
     */
    public function getByKey($key)
    {
        $pattern = $this->getPattern($key);
        $usages = [];

        foreach (Finder::create()->files()->in($this->paths) as $file)
        {
            if (preg_match_all("/$pattern/siU", $file->getContents(), $matches)) {

                for($i=0; $i < count($matches[0]);$i++)
                {
                    $usages[] = [
                        'path' => $file->getRealPath(),
                        'function' => $matches[0][$i]
                    ];
                }
            }
        }

        return $usages;
    }

    /**
     * @param $key
     * @return string
     */
    private function getPattern($key): string
    {
        $functions = [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice'
        ];

        $pattern =
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]' . // Must not start with any alphanum or _
            '(?<!->)' . // Must not start with ->
            '(' . implode('|', $functions) . ')' .// Must start with one of the functions
            "\(" .// Match opening parentheses
            "[\'\"]" .// Match " or '
            '(' .// Start a new group to match:
            $key . // match the oldKey
            ')' .// Close group
            "[\'\"]" .// Closing quote
            "[\),]"  // Close parentheses or new parameter
        ;

        return $pattern;
    }
}