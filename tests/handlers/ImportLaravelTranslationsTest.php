<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Handlers\ImportLaravelTranslations;

class ImportLaravelTranslationsTest extends TestCase
{
    /** @test */
    public function it_can_import()
    {
        app(ImportLaravelTranslations::class)->import('nl');

        dd(Line::all()->toArray());
    }
}