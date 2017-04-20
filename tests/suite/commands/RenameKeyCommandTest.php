<?php

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Tests\TestCase;

class RenameKeyCommandTest extends TestCase
{
    /** @test */
    function it_can_rename_key()
    {
        // This command also refreshes the cached translation files so make sure to clear the temporary target
        $this->setTemporaryCacheDir();

        $line = Line::make('frezzie.title');

        $this->artisan('squanto:rename',['oldKey' => 'frezzie.title', 'newKey' => 'freggle.title', '--files' => true]);

        $this->assertEquals($line->id, Line::findByKey('freggle.title')->id);
    }

    private function setTemporaryCacheDir()
    {
        config()->set('squanto.cache_path', $this->getFixtureDirectory('cachedchanged'));

        app()->bind(CachedTranslationFile::class, function ($app) {
            return new CachedTranslationFile(
                new Filesystem(new Local($this->getFixtureDirectory('cachedchanged')))
            );
        });
    }
}
