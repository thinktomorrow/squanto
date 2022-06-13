<?php

namespace Thinktomorrow\SquantoTests\Application\Translator;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class SquantoTranslatorCascadeTest extends TestCase
{
    /** @test */
    public function when_cache_does_not_contain_key_the_database_is_used()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.subtitle', [
            'nl' => 'subtitel database',
        ]));

        $this->assertEquals('subtitel database', $this->translator->get('about.subtitle'));
    }

    /** @test */
    public function when_cache_and_database_values_dont_exist_the_original_language_file_is_used()
    {
        $this->assertEquals('description', $this->translator->get('about.content', [], 'fr')); // Default is true
    }

    /** @test */
    public function when_cache_contains_null_the_database_is_used()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.content', [
            'en' => 'content database',
        ]));

        $aboutContent = require __DIR__ . '/../../stubs/cached/en/about.php';
        $this->assertNull($aboutContent['content']);

        app()->setLocale('en');
        $this->assertEquals('content database', $this->translator->get('about.content'));
    }

    /** @test */
    public function when_database_contains_null_the_translation_file_is_used()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.hello', [
            'en' => null,
        ]));

        $this->assertEquals('hello :name, welcome back', $this->translator->get('about.hello', [], 'en'));
    }

    /** @test */
    public function when_database_contains_an_intentional_empty_string_the_database_is_still_used()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.hello', [
            'en' => '',
        ]));

        $this->assertEquals('', $this->translator->get('about.hello', [], 'en'));
    }

    /** @test */
    public function it_uses_original_source_for_excluded_files()
    {
        config()->set('squanto.excluded_files', ['about']);
        $this->rebindTranslator();

        $this->assertEquals('titel', $this->translator->get('about.title'));
    }
}
