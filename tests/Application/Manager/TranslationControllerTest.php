<?php

namespace Thinktomorrow\Squanto\Tests\Application;

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

    /** @test */
    public function an_empty_line_is_intentionally_kept_empty()
    {
        $page = Page::make('foo');
        $line = Line::make('foo.fourth');
        $line->saveValue('nl','bazz');

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => '']]]);

        app(TranslationController::class)->update($request, $page->id);

        $this->assertSame('', Line::findByKey('foo.fourth')->value);
        $this->assertSame('', app('translator')->get('foo.fourth'));
    }

}
