<?php

namespace Thinktomorrow\Squanto\Services;

class SuggestType
{
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const EDITOR = 'editor';

    private $value;

    public function __construct(Line $line)
    {
        $this->value = $line->getValue();
    }

    /**
     * Get suggestions for a type of value
     *
     * @return string
     */
    public function suggest()
    {
        if (strip_tags($this->value) != $this->value) {
            return self::EDITOR;
        }

        if (strlen($this->value) < 50) {
            return self::TEXT;
        }

        return self::TEXTAREA;
    }
}
