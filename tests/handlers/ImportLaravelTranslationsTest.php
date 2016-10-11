<?php

namespace Thinktomorrow\Squanto\Tests;

use Illuminate\Support\Collection;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Handlers\ImportLaravelTranslations;

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
        $importer = app(ImportLaravelTranslations::class)->import('nl');

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
        app(ImportLaravelTranslations::class)->disableOverwriteProtection()->import('nl');

        $this->reflectLanguageChange();

        $importer = app(ImportLaravelTranslations::class)->enableOverwriteProtection()->import('nl');

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
        app(ImportLaravelTranslations::class)->import('nl');

        $this->reflectLanguageChange();

        $importer = app(ImportLaravelTranslations::class)->disableOverwriteProtection()->import('nl');

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
        app(ImportLaravelTranslations::class)->import('nl');
        app(ImportLaravelTranslations::class)->disableOverwriteProtection()->import('en');

    }

    private function resetLanguageChange()
    {
        // Reset language file
        file_put_contents(app('path.lang') . '/nl/about.php', "<?php return [
            'bar'   => 'bazz',
            'hello' => 'hello :name, welcome back',
        ];");
    }

    private function reflectLanguageChange()
    {
        // Fake change 2 entries
        file_put_contents(app('path.lang') . '/nl/about.php', "<?php return [
            'bar'   => 'bazz CHANGED',
            'hello' => 'hello :name, welcome back CHANGED',
        ];");
    }
}