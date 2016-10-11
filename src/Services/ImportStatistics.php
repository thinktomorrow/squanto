<?php


namespace Thinktomorrow\Squanto\Services;


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

    public function pushTranslation($item,array $value)
    {
        return $this->push($item,$value,false,$value['locale'].'.'.$value['key']);
    }

    public function pushSingle($key,$value)
    {
        return $this->push($key,$value,true);
    }

    private function push($item,$value,$single = false,$key = null)
    {
        if($single)
        {
            $this->attributes[$item] = $value;
            return $this;
        }

        if(!isset($this->attributes[$item])) $this->attributes[$item] = [];

        ($key) ? $this->attributes[$item][$key] = $value : $this->attributes[$item][] = $value;

        return $this;
    }

    public function merge(self $stats)
    {
        return new self( array_merge($this->attributes,$stats->get()) );
    }

    /**
     * Retrieve stat attributes by getter.
     * e.g. getInserts() will return the inserts array
     *
     * @param $method
     * @param $parameters
     * @return array
     */
    public function __call($method,$parameters)
    {
        $key = (0 === strpos($method,'get')) ? Str::snake(substr($method,3),'_') : $method;

        if(!isset($this->attributes[$key])) return [];

        return $this->attributes[$key];
    }
}