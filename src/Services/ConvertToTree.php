<?php


namespace Thinktomorrow\Squanto\Services;


use Illuminate\Support\Arr;

final class ConvertToTree
{
    /**
     * convert flat array of key-value pairs to multidimensional array e.g. foo.bar => 'translation of foo' to [foo => [ bar => 'translation of foo' ]]
     *
     * @param array $lines
     * @param bool $includePage
     * @return array
     */
    public static function fromFlattened(array $lines = [], $includePage = true)
    {
        $translations = [];

        foreach ($lines as $key => $value) {
            Arr::set($translations, $key, $value);
        }

        return $includePage ? $translations : reset($translations);
    }
}