<?php

namespace Thinktomorrow\Squanto\Import;

/**
 * Class ImportTranslation
 *
 * Translation during import from lang files to database
 * @package Thinktomorrow\Squanto\Domain
 */
class Entry
{
    private $locale;
    private $key;
    private $value;
    /**
     * @var null
     */
    private $original_value;

    public function __construct($locale, $key, $value, $original_value = null)
    {
        $this->locale = $locale;
        $this->key = $key;
        $this->value = $value;
        $this->original_value = $original_value;
    }

    public function __get($key)
    {
        return $this->$key;
    }
}