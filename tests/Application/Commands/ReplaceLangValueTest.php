<?php

namespace Thinktomorrow\Squanto\Tests\Application\Commands;

use Thinktomorrow\Squanto\Application\Commands\ReplaceLangValue;
use Thinktomorrow\Squanto\Domain\LangFile;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Tests\TestCase;

class ReplaceLangValueTest extends TestCase
{
    /** @test */
    public function it_can_replace_lang_values()
    {
        $langFile = LangFile::fromFilepath($this->langDirectory->path('nl/langfile.php'));
        $replacedLangfile = $langFile->replace(LineKey::fromString('langfile.first'),'new-first-value-nl');

        $this->assertEquals('new-first-value-nl', $replacedLangfile->values()['first']);
    }

    /** @test */
    public function it_only_replaces_when_key_is_found()
    {
        $langFile = LangFile::fromFilepath($this->langDirectory->path('nl/langfile.php'));
        $replacedLangfile = $langFile->replace(LineKey::fromString('langfile.fake'),'xxx');

        $this->assertCount(2, $replacedLangfile->values()->all());
    }

    /** @test */
    public function it_can_replace_a_value_in_lang_file()
    {
        $this->assertEquals('first-value-nl', $this->translator->get('langfile.first'));

        $langFile = LangFile::fromFilepath($this->langDirectory->path('nl/langfile.php'));
        $lineKey = LineKey::fromString('langfile.first');
        $value = 'new-first-value-nl';

        /** @var ReplaceLangValue */
        app(ReplaceLangValue::class)->handle($langFile, $lineKey, $value);

        $this->rebindTranslator();

        $this->assertEquals('new-first-value-nl', $this->translator->get('langfile.first'));
    }
}
