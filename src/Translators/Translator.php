<?php

namespace Thinktomorrow\Squanto\Translators;

interface Translator
{
    public function get(string $key, array $replace = [], ?string $locale = null, bool $fallback = true): string|array|null;
}
