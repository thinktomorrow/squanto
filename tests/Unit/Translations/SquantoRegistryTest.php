<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Translations;

use Thinktomorrow\Squanto\Sources\SquantoRegistry;
use Thinktomorrow\Squanto\Sources\SquantoSource;
use Thinktomorrow\Squanto\Squanto;
use Thinktomorrow\SquantoTests\TestCase;

final class SquantoRegistryTest extends TestCase
{
    public function test_it_registers_the_default_app_source_from_config(): void
    {
        $registry = app(SquantoRegistry::class);
        $sources = $registry->all();

        $this->assertCount(1, $sources);
        $this->assertInstanceOf(SquantoSource::class, $sources[0]);
        $this->assertSame(config('squanto.lang_path'), $sources[0]->path());
        $this->assertNull($sources[0]->namespace());
        $this->assertSame('App', $sources[0]->label());
        $this->assertFalse($sources[0]->isNamespaced());
        $this->assertSame($sources[0], $registry->defaultSource());
    }

    public function test_it_can_register_a_plugin_source_via_public_api(): void
    {
        Squanto::register(
            __DIR__.'/../../stubs/plugin/lang',
            'chief-form-plugin',
            'Form plugin'
        );

        $sources = app(SquantoRegistry::class)->all();

        $this->assertCount(2, $sources);
        $this->assertSame(__DIR__.'/../../stubs/plugin/lang', $sources[1]->path());
        $this->assertSame('chief-form-plugin', $sources[1]->namespace());
        $this->assertSame('Form plugin', $sources[1]->label());
        $this->assertTrue($sources[1]->isNamespaced());
    }

    public function test_it_deduplicates_a_source_by_namespace_and_path(): void
    {
        Squanto::register(__DIR__.'/../../stubs/plugin/lang', 'chief-form-plugin', 'Form plugin');
        Squanto::register(__DIR__.'/../../stubs/plugin/lang/', 'chief-form-plugin', 'Another label');

        $sources = app(SquantoRegistry::class)->all();

        $this->assertCount(2, $sources);
        $this->assertSame('Another label', $sources[1]->label());
    }
}
