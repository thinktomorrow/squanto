<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto;

use Thinktomorrow\Squanto\Sources\SquantoRegistry;

final class Squanto
{
    /**
     * Register a language source that Squanto should scan and manage.
     *
     * This only registers the source for Squanto. Package translations should
     * still be registered with Laravel via loadTranslationsFrom().
     */
    public static function register(string $path, ?string $namespace = null, ?string $label = null): void
    {
        app(SquantoRegistry::class)->registerSource($path, $namespace, $label);
    }

    /**
     * Register a package language source for both Squanto management and Laravel fallback translations.
     */
    public static function registerPlugin(string $path, string $namespace, ?string $label = null): void
    {
        self::register($path, $namespace, $label);

        app('translation.loader')->addNamespace($namespace, $path);
    }
}
