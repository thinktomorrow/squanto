<?php

namespace Thinktomorrow\Squanto\Import;

use Thinktomorrow\Squanto\Domain\LineKey;

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
    private $original_value;
    private $pagekey;

    public function __construct($locale, $key, $value, $original_value = null)
    {
        $this->locale = $locale;
        $this->key = $key;
        $this->value = $value;
        $this->original_value = $original_value;
        $this->pagekey = (new LineKey($key))->getPageKey();
    }

    public function __get($key)
    {
        return $this->$key;
    }
}