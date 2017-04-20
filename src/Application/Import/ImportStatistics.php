<?php


namespace Thinktomorrow\Squanto\Application\Import;

use Illuminate\Support\Str;

class ImportStatistics
{
    private $attributes = [];

    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function get()
    {
        return $this->attributes;
    }

    public function pushTranslation($action, Entry $translation)
    {
        return $this->push($action, $translation, false, $translation->locale.'.'.$translation->key);
    }

    public function removeTranslation($action, $locale, $key)
    {
        $k = $locale.'.'.$key;

        if (!isset($this->attributes[$action]) || !isset($this->attributes[$action][$k])) {
            return;
        }

        unset($this->attributes[$action][$k]);
    }

    public function pushSingle($key, $value)
    {
        return $this->push($key, $value, true);
    }

    private function push($action, $value, $single = false, $key = null)
    {
        if ($single) {
            $this->attributes[$action] = $value;
            return $this;
        }

        if (!isset($this->attributes[$action])) {
            $this->attributes[$action] = [];
        }

        ($key) ? $this->attributes[$action][$key] = $value : $this->attributes[$action][] = $value;

        return $this;
    }

    public function merge(self $stats)
    {
        return new self(array_merge($this->attributes, $stats->get()));
    }

    /**
     * Retrieve stat attributes by getter.
     * e.g. getInserts() will return the inserts array
     *
     * @param $method
     * @param $parameters
     * @return array
     */
    public function __call($method, $parameters)
    {
        $key = (0 === strpos($method, 'get')) ? Str::snake(substr($method, 3), '_') : $method;

        if (!isset($this->attributes[$key])) {
            return [];
        }

        return $this->attributes[$key];
    }
}
