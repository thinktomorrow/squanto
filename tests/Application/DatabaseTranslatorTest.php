<?php

namespace Thinktomorrow\Squanto\Tests\Application;

use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Tests\TestCase;
use Thinktomorrow\Squanto\Translators\DatabaseTranslator;

class DatabaseTranslatorTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();

        $this->translator = new DatabaseTranslator;
    }

    /** @test */
    public function it_can_get_a_translation()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('nl','bazz');

        $this->assertEquals('bazz',$this->translator->get('foo.bar'));
    }

    /** @test */
    public function it_can_get_a_translation_collection()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('nl','bazz');
        $line = DatabaseLine::createFromKey('foo.bar2');
        $line->saveValue('nl','bazzz');

        $this->assertEquals(['bar' => 'bazz','bar2' => 'bazzz'],$this->translator->get('foo'));

        // Change locale
        app()->setLocale('en');
        $line = DatabaseLine::createFromKey('foo.bav');
        $line->saveValue('en','bazz');

        $this->assertEquals(['bav' => 'bazz'],$this->translator->get('foo'));
    }

    /** @test */
    public function it_can_get_a_translation_with_placeholders()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('nl','hello :name, welcome back');

        $this->assertEquals('hello Ben, welcome back',$this->translator->get('foo.bar',['name' => 'Ben']));
    }

    /** @test */
    public function it_can_get_a_translation_for_specific_locale()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('nl','bazz');
        $line->saveValue('fr','bash');

        $this->assertEquals('bash',$this->translator->get('foo.bar',[],'fr'));
    }

    /** @test */
    public function it_can_get_a_fallback_translation()
    {
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('en','bazz');

        $this->assertEquals('bazz',$this->translator->get('foo.bar',[],'fr',true));
        $this->assertEquals('bazz',$this->translator->get('foo.bar',[],'fr')); // Default is true
        $this->assertEquals(null,$this->translator->get('foo.bar',[],'fr',false));
    }

}
