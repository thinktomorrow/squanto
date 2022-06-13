<?php

namespace Thinktomorrow\SquantoTests\Application\Translator;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Translators\DatabaseTranslator;
use Thinktomorrow\SquantoTests\TestCase;

class DatabaseTranslatorTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->translator = app(DatabaseTranslator::class);
    }

    /** @test */
    public function it_can_get_a_translation()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.bar', [
            'nl' => 'bazz',
        ]));

        $this->assertEquals('bazz', $this->translator->get('foo.bar'));
    }

    /** @test */
    public function it_can_get_a_translation_collection()
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.bar', ['nl' => 'bazz']));
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.bar2', ['nl' => 'bazzer']));

        $this->assertEquals(['bar' => 'bazz','bar2' => 'bazzer'], $this->translator->get('foo'));
    }

    /** @test */
    public function an_non_found_key_will_return_null()
    {
        $this->assertNull($this->translator->get('foo'));
    }

    /** @test */
    public function it_can_get_a_translation_with_placeholders()
    {
        $line = DatabaseLine::create(['key' => 'foo.bar', 'values' => ['value' => [
            'nl' => 'hello :name, welcome back',
        ]]]);

        $this->assertEquals('hello Ben, welcome back', $this->translator->get('foo.bar', ['name' => 'Ben']));
    }

    /** @test */
    public function it_can_get_a_translation_for_specific_locale()
    {
        $line = DatabaseLine::create(['key' => 'foo.bar', 'values' => ['value' => [
            'nl' => 'bazz',
            'fr' => 'bash',
        ]]]);

        $this->assertEquals('bash', $this->translator->get('foo.bar', [], 'fr'));
    }

    /** @test */
    public function it_can_get_a_fallback_translation()
    {
        $line = DatabaseLine::create(['key' => 'foo.bar', 'value' => [
            'en' => 'bazz',
        ]]);

        $this->assertEquals('bazz', $this->translator->get('foo.bar', [], 'fr', true));
        $this->assertEquals('bazz', $this->translator->get('foo.bar', [], 'fr')); // Default is true
        $this->assertEquals(null, $this->translator->get('foo.bar', [], 'fr', false));
    }
}
