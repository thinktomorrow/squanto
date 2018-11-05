<?php

namespace Thinktomorrow\Squanto\Tests;

use Illuminate\Http\Request;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Manager\Http\Controllers\TranslationController;

class TranslationControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpDatabase();
    }

    /** @test */
    public function it_can_store_a_translation()
    {
        $page = Page::make('foo');
        $line = Line::make('foo.bar');
        $line->saveValue('nl','bazz');

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => 'bazz & foo']]]);

        app(TranslationController::class)->update($request, $page->id);
        
        $this->assertEquals('bazz & foo', Line::findByKey('foo.bar')->value);           
    }

}
