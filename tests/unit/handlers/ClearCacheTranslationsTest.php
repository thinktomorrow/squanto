<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Handlers\ClearCacheTranslations;

class ClearCacheTranslationsTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        // The filesystem bindings are not present in the container for some reason
        // Only after loading the translator, they seem to be loaded in...
        // TODO: should look into this further to find out bug behind this all
//        app('translator');

    }

    /** @test */
    public function it_removes_all_cached_language_files()
    {
        app(ClearCacheTranslations::class);
    }
}