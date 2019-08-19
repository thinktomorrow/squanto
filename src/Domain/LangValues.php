<?php

namespace Thinktomorrow\Squanto\Domain;

use ArrayAccess;
use Illuminate\Support\Arr;

class LangValues implements ArrayAccess
{
    /** @var array */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function all(): array
    {
        return $this->values;
    }

    public function replace($key, $value)
    {
        $flattenedArray = $this->flatten();

        if(!array_key_exists($key, $flattenedArray)) return new static($this->values);

        return new static(
            static::inflateArray( array_merge($flattenedArray, [$key => $value]) )
        );
    }

    public function flatten()
    {
        return Arr::dot($this->values);
    }

    public function inflate()
    {
        return static::inflateArray($this->values);
    }

    /**
     * convert flat array of key-value pairs to multidimensional array
     * e.g. foo.bar => 'translation of foo' to [foo => [ bar => 'translation of foo' ]]
     *
     * @param array $values
     * @param bool $includePageKey
     * @return array
     */
    private static function inflateArray(array $values = [], $includePageKey = true)
    {
        $inflatedValues = [];

        foreach ($values as $key => $value) {
            Arr::set($inflatedValues, $key, $value);
        }

        return $includePageKey ? $inflatedValues : reset($inflatedValues);
    }

    public function offsetExists($offset)
    {
        return isset($this->values[$offset]);
    }

    public function offsetGet($offset)
    {
        if(!isset($this->values[$offset])) return null;

        return $this->values[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->values[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->values[$offset]);
    }
}