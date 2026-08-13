<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Disk;

use Thinktomorrow\Squanto\Disk\DiskLinesRepository;
use Thinktomorrow\Squanto\Disk\ReadLanguageFile;
use Thinktomorrow\Squanto\Disk\ReadLanguageFolder;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLanguageFileReference;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;
use Thinktomorrow\Squanto\Squanto;
use Thinktomorrow\SquantoTests\TestCase;

final class DiskLinesRepositoryTest extends TestCase
{
    public function test_it_can_retrieve_all_translations_from_disk()
    {
        config()->set('squanto.locales', ['en', 'nl']);

        $lines = app(DiskLinesRepository::class)->all();
        $this->assertInstanceOf(Lines::class, $lines);

        $this->assertCount(7, $this->getPrivateProperty($lines, 'items')); // 6 dutch translations
    }

    public function test_it_can_retrieve_all_translations_for_a_locale_from_disk()
    {
        config()->set('squanto.locales', ['en']);

        $lines = app(DiskLinesRepository::class)->all();
        $this->assertInstanceOf(Lines::class, $lines);

        $this->assertCount(5, $this->getPrivateProperty($lines, 'items')); // 5 english translations
    }

    public function test_it_can_retrieve_all_translations_of_a_folder()
    {
        $lines = app(ReadLanguageFolder::class)->read('en');

        $this->assertInstanceOf(Lines::class, $lines);

        $this->assertNotNull($lines->find('about.title'));

        // TODO: should be another test
        $this->assertNull($lines->find('about.content')); // Not available in en
    }

    public function test_it_can_exclude_certain_files()
    {
        // Exclude all the en translation files
        config()->set('squanto.excluded_files', ['nested', 'about']);

        $lines = app(ReadLanguageFolder::class)->read('en');

        $lineItems = $this->getPrivateProperty($lines, 'items');

        $this->assertCount(0, $lineItems);
    }

    public function test_it_can_retrieve_all_translations_of_any_file()
    {
        $lines = app(ReadLanguageFile::class)->read('nl', __DIR__.'/langfile_stub.php');

        $this->assertInstanceOf(Lines::class, $lines);
        $this->assertNotNull($lines->find('langfile_stub.title'));
    }

    public function test_it_can_retrieve_translations_with_numeric_keys_from_any_file()
    {
        $lines = app(ReadLanguageFile::class)->read('nl', __DIR__.'/langfile_numeric_stub.php');

        $this->assertInstanceOf(Lines::class, $lines);
        $this->assertNotNull($lines->find('langfile_numeric_stub.0'));
        $this->assertNotNull($lines->find('langfile_numeric_stub.1'));
    }

    public function test_it_fails_when_file_does_not_exist()
    {
        $this->expectException(InvalidLanguageFileReference::class);

        app(ReadLanguageFile::class)->read('nl', 'xxx');
    }

    public function test_it_fails_when_file_content_does_not_return_an_array()
    {
        $this->expectException(InvalidLanguageFileReference::class);

        app(ReadLanguageFile::class)->read('nl', __DIR__.'/langfile_empty_stub.php');
    }

    public function test_it_can_retrieve_all_translations_from_registered_namespaced_sources(): void
    {
        Squanto::register($this->pluginLangPath(), 'chief-form-plugin', 'Form plugin');
        config()->set('squanto.locales', ['en', 'nl']);

        $lines = app(DiskLinesRepository::class)->all();

        $this->assertInstanceOf(Lines::class, $lines);
        $this->assertNotNull($lines->find('chief-form-plugin::general.title'));
        $this->assertNotNull($lines->find('chief-form-plugin::nested.hero.title'));
    }

    public function test_it_can_retrieve_all_translations_of_a_namespaced_folder(): void
    {
        Squanto::register($this->pluginLangPath(), 'chief-form-plugin', 'Form plugin');

        $source = app(SquantoRegistry::class)->all()[1];
        $lines = app(ReadLanguageFolder::class)->read('en', $source);

        $this->assertInstanceOf(Lines::class, $lines);
        $this->assertNotNull($lines->find('chief-form-plugin::general.title'));
        $this->assertNotNull($lines->find('chief-form-plugin::nested.hero.title'));
    }

    public function test_excluded_files_are_not_applied_to_namespaced_sources(): void
    {
        Squanto::register($this->pluginLangPath(), 'chief-form-plugin', 'Form plugin');
        config()->set('squanto.excluded_files', ['nested']);

        $source = app(SquantoRegistry::class)->all()[1];
        $lines = app(ReadLanguageFolder::class)->read('en', $source);

        $this->assertNotNull($lines->find('chief-form-plugin::nested.hero.title'));
    }

    private function pluginLangPath(): string
    {
        return dirname((string) config('squanto.lang_path')).'/plugin/lang';
    }
}
