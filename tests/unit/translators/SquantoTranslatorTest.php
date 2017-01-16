<?php

namespace Thinktomorrow\Squanto\Tests;

use Mockery;
use Mockery\Mock;

class SquantoTranslatorTest extends TestCase
{
    private $translator;

    public function setUp()
    {
        parent::setUp();

        $this->translator = app('translator');
    }

    /** @test */
    public function it_can_get_a_translation()
    {
        $this->assertEquals('bazz',$this->translator->get('foo.bar'));
    }

    /** @test */
    public function it_can_get_a_translation_with_placeholders()
    {
        $this->assertEquals('hello Ben, welcome back',$this->translator->get('foo.hello',['name' => 'Ben']));
    }

    /** @test */
    public function it_can_get_a_translation_for_specific_locale()
    {
        $this->assertEquals('bash',$this->translator->get('foo.bar',[],'fr'));
    }

    /** @test */
    public function it_returns_null_if_no_translation_found_instead_of_key()
    {
        app('translator')->setKeyAsDefault(false);

        $this->assertEquals(null,$this->translator->get('unknown.key'));
    }

    /** @test */
    public function it_can_get_a_fallback_translation()
    {
        $this->assertEquals('bazzz',$this->translator->get('foo.bam',[],'fr',true));
        $this->assertEquals('bazzz',$this->translator->get('foo.bam',[],'fr')); // Default is true
        $this->assertEquals('foo.bam',$this->translator->get('foo.bam',[],'fr',false));
    }

    /** @test */
    public function it_uses_original_source_for_non_managed_lines()
    {
        config()->set('squanto.excluded_files', ['foo']);

        $this->assertEquals('bazz',$this->translator->get('foo.bar'));
    }

}
