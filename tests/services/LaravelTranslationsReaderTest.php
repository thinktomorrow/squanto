<?php

namespace Thinktomorrow\Squanto\Tests;

use Illuminate\Support\Collection;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;

class LaravelTranslationsReaderTest extends TestCase
{
    /** @test */
    public function it_reads_the_original_lang_files()
    {
        $reader = app(LaravelTranslationsReader::class);
        $translations = $reader->read('nl')->get();

        $this->assertInstanceOf(Collection::class,$translations);
        $this->assertCount(3,$translations['foo']); // foo lang file contains 3 entries
        $this->assertCount(2,$translations['foo']['intro']);
    }

    /** @test */
    public function it_can_flatten_each_file_entries_with_dotted_keys()
    {
        $reader = app(LaravelTranslationsReader::class);
        $translations = $reader->read('nl')->flattenPerFile();

        $this->assertInstanceOf(Collection::class,$translations);
        $this->assertCount(5,$translations['foo']); // foo lang file contains 5 entries
        $this->assertEquals(app('translator')->get('foo.intro.title'),$translations['foo']['intro.title']);
    }

    /** @test */
    public function it_can_flatten_all_lang_entries_with_dotted_keys()
    {
        $reader = app(LaravelTranslationsReader::class);
        $translations = $reader->read('nl')->flatten();

        $this->assertInstanceOf(Collection::class,$translations);
        $this->assertEquals(app('translator')->get('foo.intro.title'),$translations['foo.intro.title']);
    }

    /** @test */
    public function it_can_exclude_files_by_filename()
    {
        $reader = app(LaravelTranslationsReader::class);
        $translations = $reader->read('nl',['foo'])->flatten();

        $this->assertInstanceOf(Collection::class,$translations);
        $this->assertCount(2,$translations);
        $this->assertTrue(isset($translations['about.bar']));
        $this->assertFalse(isset($translations['foo.intro.title']));
    }

    /** @test */
    public function it_silently_ignores_nonexisting_locale()
    {
        $reader = app(LaravelTranslationsReader::class);
        $translations = $reader->read('locale')->flatten();

        $this->assertInstanceOf(Collection::class,$translations);
        $this->assertCount(0,$translations);
    }
}