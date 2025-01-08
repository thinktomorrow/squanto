<?php

namespace Thinktomorrow\SquantoTests\Application\Translator;

use Thinktomorrow\SquantoTests\TestCase;

class SquantoTranslatorTest extends TestCase
{
    public function test_it_can_get_a_translation_by_default_from_cache()
    {
        $this->assertEquals('titel cached', $this->translator->get('about.title'));
    }

    public function test_retrieving_translation_is_case_insensitive()
    {
        $this->assertEquals('titel cached', $this->translator->get('about.TITLE'));
    }

    public function test_retrieving_translation_can_contain_underscore()
    {
        $this->assertEquals('tel', $this->translator->get('about.phone_number'));
    }

    public function test_it_can_get_a_translation_collection()
    {
        $aboutContent = require __DIR__ . '/../../stubs/cached/nl/about.php';

        $this->assertEquals($aboutContent, $this->translator->get('about'));
    }

    public function test_it_can_get_a_translation_with_placeholders()
    {
        app()->setLocale('en');
        $this->assertEquals('hello Ben, welcome back', $this->translator->get('about.hello', ['name' => 'Ben']));
    }

    public function test_it_can_get_a_translation_for_specific_locale()
    {
        $this->assertEquals('titre cached', $this->translator->get('about.title', [], 'fr'));
    }

    public function test_it_returns_translation_key_if_no_translation_found_instead_of_key()
    {
        app('translator')->setKeyAsDefault(true);

        $this->assertEquals('unknown.key', $this->translator->get('unknown.key'));
    }

    public function test_it_returns_null_if_no_translation_found_instead_of_key()
    {
        app('translator')->setKeyAsDefault(false);

        $this->assertEquals(null, $this->translator->get('unknown.key'));
    }

    public function test_it_can_get_a_fallback_translation()
    {
        config()->set('app.fallback_locale', 'nl');
        $this->rebindTranslator();

        $this->assertEquals('nl heading', $this->translator->get('about.heading', [], 'fr', true));
        $this->assertEquals('nl heading', $this->translator->get('about.heading', [], 'fr')); // Default is true
        $this->assertEquals('about.heading', $this->translator->get('about.heading', [], 'fr', false));
    }
}
