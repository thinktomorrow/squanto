<?php

namespace Thinktomorrow\Squanto\Tests\Application;

use Thinktomorrow\Squanto\Domain\DatabaseLine;

class SquantoTranslatorTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->translator = app('translator');
    }

    /** @test */
    public function it_can_get_a_translation()
    {
        $this->assertEquals('bazz', $this->translator->get('foo.bar'));
    }

    /** @test */
    public function retrieving_translation_is_case_insensitive()
    {
        $this->assertEquals('bazz', $this->translator->get('foo.BAR'));
    }

    /** @test */
    public function it_can_get_a_translation_collection()
    {
        $foo = require __DIR__.'/../../stubs/cached/nl/foo.php';

        $this->assertEquals($foo, $this->translator->get('foo'));
    }

    /** @test */
    public function it_can_get_a_translation_with_placeholders()
    {
        $this->assertEquals('hello Ben, welcome back', $this->translator->get('foo.hello', ['name' => 'Ben']));
    }

    /** @test */
    public function it_can_get_a_translation_for_specific_locale()
    {
        $this->assertEquals('bash', $this->translator->get('foo.bar', [], 'fr'));
    }

    /** @test */
    public function it_returns_null_if_no_translation_found_instead_of_key()
    {
        app('translator')->setKeyAsDefault(false);

        $this->assertEquals(null, $this->translator->get('unknown.key'));
    }

    /** @test */
    public function it_can_get_a_fallback_translation()
    {
        $this->assertEquals('bazzz', $this->translator->get('foo.bam', [], 'fr', true));
        $this->assertEquals('bazzz', $this->translator->get('foo.bam', [], 'fr')); // Default is true
        $this->assertEquals('foo.bam', $this->translator->get('foo.bam', [], 'fr', false));
    }

    /** @test */
    public function it_uses_original_source_for_non_managed_lines()
    {
        config()->set('squanto.excluded_files', ['foo']);

        $this->assertEquals('bazz', $this->translator->get('foo.bar'));
    }

    /** @test */
    public function it_takes_file_source_if_database_line_is_null()
    {
        DatabaseLine::make('foo.fourth')->saveValue('nl',null);

        $this->assertEquals('fourth-lang', $this->translator->get('foo.fourth'));
    }

    /** @test */
    public function it_takes_database_source_if_database_line_is_intentional_empty_string()
    {
        DatabaseLine::make('foo.fourth')->saveValue('nl','');

        $this->assertSame('', $this->translator->get('foo.fourth'));
    }
}
