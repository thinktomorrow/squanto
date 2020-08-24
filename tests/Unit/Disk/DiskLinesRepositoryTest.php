<?php
declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Disk;

use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\SquantoTests\TestCase;
use Thinktomorrow\Squanto\Disk\Query\ReadLanguageFile;
use Thinktomorrow\Squanto\Disk\Query\ReadLanguageFolder;
use Thinktomorrow\Squanto\Disk\Query\DiskLinesRepository;
use Thinktomorrow\Squanto\Domain\Exceptions\InvalidLanguageFileReference;

final class DiskLinesRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_all_translations_from_disk()
    {
        config()->set('thinktomorrow.squanto.locales', ['en', 'nl']);

        $lines = app(DiskLinesRepository::class)->all();
        $this->assertInstanceOf(Lines::class, $lines);

        $this->assertCount(7, $this->getPrivateProperty($lines, 'items')); // 6 dutch translations

    }

    /** @test */
    public function it_can_retrieve_all_translations_for_a_locale_from_disk()
    {
        config()->set('thinktomorrow.squanto.locales', ['en']);

        $lines = app(DiskLinesRepository::class)->all();
        $this->assertInstanceOf(Lines::class, $lines);

        $this->assertCount(5, $this->getPrivateProperty($lines, 'items')); // 5 english translations
    }

    /** @test */
    public function it_can_retrieve_all_translations_of_a_folder()
    {
         $lines = app(ReadLanguageFolder::class)->read('en');

         $this->assertInstanceOf(Lines::class, $lines);

         $this->assertNotNull($lines->find('about.title'));

         // TODO: should be another test
         $this->assertNull($lines->find('about.content')); // Not available in en
     }

    /** @test */
    public function it_can_exclude_certain_files()
    {
        // Exclude all the en translation files
        config()->set('thinktomorrow.squanto.excluded_files', ['nested','about']);

        $lines = app(ReadLanguageFolder::class)->read('en');

        $lineItems = $this->getPrivateProperty($lines, 'items');

        $this->assertCount(0, $lineItems);
    }

    /** @test */
    public function it_can_retrieve_all_translations_of_any_file()
    {
        $lines = app(ReadLanguageFile::class)->read('nl', __DIR__ .'/langfile_stub.php');

        $this->assertInstanceOf(Lines::class, $lines);
        $this->assertNotNull($lines->find('langfile_stub.title'));
    }

    /** @test */
    public function it_fails_when_file_does_not_exist()
    {
        $this->expectException(InvalidLanguageFileReference::class);

        app(ReadLanguageFile::class)->read('nl', 'xxx');
    }

    /** @test */
    public function it_fails_when_file_content_does_not_return_an_array()
    {
        $this->expectException(InvalidLanguageFileReference::class);

        app(ReadLanguageFile::class)->read('nl', __DIR__ .'/langfile_empty_stub.php');
    }
}
