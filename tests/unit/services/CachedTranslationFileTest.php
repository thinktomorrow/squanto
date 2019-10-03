<?php

namespace Thinktomorrow\Squanto\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Services\CachedTranslationFile;
use Thinktomorrow\Squanto\Domain\Line;

class CachedTranslationFileTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->setUpDatabase();
        
        // Reset language file because this language file is changed within these tests to reflect a change
        $this->setTemporaryCacheDir();
    }

    public function tearDown(): void
    {
        // Cleanup after each test (Ironically this is done with the class being tested)
        app(CachedTranslationFile::class)->delete();

        parent::tearDown();
    }

    /** @test */
    public function it_can_cache_all_translations()
    {
        $filename = 'foo_'.time();
        $filepath = config('squanto.cache_path').'/nl/'.$filename.'.php';
        Line::make($filename.'.bar')->saveValue('nl','foobar-nl');
        Line::make($filename.'.baz')->saveValue('nl','foobaz-nl');

        app(CachedTranslationFile::class)->write();

        $this->assertFileExists($filepath);

        $translations = require $filepath;
        $this->assertIsArray($translations);
        $this->assertCount(2,$translations);
        $this->assertEquals('foobar-nl',$translations['bar']);
        $this->assertEquals('foobaz-nl',$translations['baz']);
    }

    /** @test */
    public function it_can_delete_all_translations()
    {
        $filename = 'foo_'.time();
        $filepath = config('squanto.cache_path').'/nl/'.$filename.'.php';
        Line::make($filename.'.bar')->saveValue('nl','foobar-nl');
        Line::make($filename.'.baz')->saveValue('nl','foobaz-nl');

        app(CachedTranslationFile::class)->write();
        $this->assertFileExists($filepath);

        app(CachedTranslationFile::class)->delete();
        $this->assertFileNotExists($filepath);
    }

    /** @test */
    public function it_can_cache_nested_arrays()
    {
        $filename = 'foo_'.time();
        $filepath = config('squanto.cache_path').'/nl/'.$filename.'.php';
        Line::make($filename.'.bar')->saveValue('nl','foobar-nl');
        Line::make($filename.'.hello')->saveValue('nl','hallo :name, welkom terug');
        Line::make($filename.'.intro.title')->saveValue('nl','Dit is een introductie!');
        Line::make($filename.'.intro.header.first')->saveValue('nl','Thuis');
        Line::make($filename.'.intro.header.second')->saveValue('nl','Over');

        app(CachedTranslationFile::class)->write();

        $translations = require $filepath;
        $this->assertCount(3,$translations);
        $this->assertEquals('foobar-nl',$translations['bar']);
        $this->assertEquals('hallo :name, welkom terug',$translations['hello']);
        $this->assertEquals('Dit is een introductie!',$translations['intro']['title']);
        $this->assertEquals('Thuis',$translations['intro']['header']['first']);
        $this->assertEquals('Over',$translations['intro']['header']['second']);
    }

    private function setTemporaryCacheDir()
    {
        config()->set('squanto.cache_path', __DIR__ . '/../../stubs/cachedchanged');

        app()->bind(CachedTranslationFile::class, function ($app) {
            return new CachedTranslationFile(
                new Filesystem(new Local(__DIR__ . '/../../stubs/cachedchanged'))
            );
        });
    }
}