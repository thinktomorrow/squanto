<?php

namespace Thinktomorrow\Squanto\Tests;

use Illuminate\Support\Collection;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Import\ImportTranslations;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;

class ImportLaravelTranslationsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // Reset language file because this language file is changed within these tests to reflect a change
        $this->resetLanguageChange();
    }

    /** @test */
    public function it_can_import_new_translations()
    {
        $importer = app(ImportTranslations::class)->import('nl');

        $this->assertInstanceOf(Collection::class,Line::all());
        $this->assertCount(7,Line::all());
        $this->assertCount(7,$importer->getStats()->getInserts());
        $this->assertCount(0,$importer->getStats()->getUpdates());
        $this->assertCount(0,$importer->getStats()->getUpdatesOnHold());
        $this->assertTrue($importer->getStats()->getOverwriteProtection());

    }

    /** @test */
    public function with_overwrite_protection_it_only_imports_new_translations()
    {
        // Import all
        app(ImportTranslations::class)->disableOverwriteProtection()->import('nl');

        $this->reflectLanguageChange();

        $importer = app(ImportTranslations::class)->enableOverwriteProtection()->import('nl');

        $this->assertCount(7,Line::all());
        $this->assertCount(0,$importer->getStats()->getInserts());
        $this->assertCount(0,$importer->getStats()->getUpdates());
        $this->assertCount(2,$importer->getStats()->getUpdatesOnHold());
        $this->assertCount(5,$importer->getStats()->getRemainedSame());
        $this->assertTrue($importer->getStats()->getOverwriteProtection());

        $this->resetLanguageChange();
    }

    /** @test */
    public function without_overwrite_protection_it_updates_existing_translations()
    {
        // Import all
        app(ImportTranslations::class)->import('nl');

        $this->reflectLanguageChange();

        $importer = app(ImportTranslations::class)->disableOverwriteProtection()->import('nl');

        $this->assertCount(7,Line::all());
        $this->assertCount(0,$importer->getStats()->getInserts());
        $this->assertCount(2,$importer->getStats()->getUpdates());
        $this->assertCount(0,$importer->getStats()->getUpdatesOnHold());
        $this->assertCount(5,$importer->getStats()->getRemainedSame());
        $this->assertFalse($importer->getStats()->getOverwriteProtection());

        $this->resetLanguageChange();
    }

    /** @test */
    public function it_can_import_multiple_locales()
    {
        app(ImportTranslations::class)->import('nl');
        app(ImportTranslations::class)->disableOverwriteProtection()->import('en');
    }

    /**
    * @test
    */
    public function it_throws_a_useful_error_if_a_lang_file_is_empty()
    {
        $this->expectExceptionMessage('The file "empty.php" seems empty. Make sure every lang file returns an array.');

        app(ImportTranslations::class)->import('de');
    }

    private function resetLanguageChange()
    {
        config()->set('squanto.lang_path', __DIR__ . '/../../stubs/lang');

        app()->bind(LaravelTranslationsReader::class, function ($app) {
            return new LaravelTranslationsReader(
                new Filesystem(new Local(__DIR__ . '/../../stubs/lang'))
            );
        });
    }

    private function reflectLanguageChange()
    {
        config()->set('squanto.lang_path', __DIR__ . '/../../stubs/langchanged');

        app()->bind(LaravelTranslationsReader::class, function ($app) {
            return new LaravelTranslationsReader(
                new Filesystem(new Local(__DIR__ . '/../../stubs/langchanged'))
            );
        });
    }
}