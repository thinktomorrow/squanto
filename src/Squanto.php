<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto;

use Thinktomorrow\Squanto\Sources\SquantoRegistry;

final class Squanto
{
    public static function register(string $path, ?string $namespace = null, ?string $label = null): void
    {
        app(SquantoRegistry::class)->registerSource($path, $namespace, $label);
    }
}
