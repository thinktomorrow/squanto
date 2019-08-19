<?php

namespace Thinktomorrow\Squanto\Tests\Application\Manager;

use Illuminate\Http\Request;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Manager\Http\Controllers\TranslationController;
use Thinktomorrow\Squanto\Tests\TestCase;

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
        $page = Page::createFromKey('foo');
        $line = DatabaseLine::createFromKey('foo.bar');
        $line->saveValue('nl','bazz');

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => 'bazz & foo']]]);

        app(TranslationController::class)->update($request, $page->id);
        
        $this->assertEquals('bazz & foo', DatabaseLine::findByKey('foo.bar')->value);
    }

    /** @test */
    public function an_empty_line_is_intentionally_kept_empty()
    {
        $page = Page::createFromKey('foo');
        $line = DatabaseLine::createFromKey('foo.fourth');
        $line->saveValue('nl','bazz');

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => '']]]);

        app(TranslationController::class)->update($request, $page->id);

        $this->assertSame('', DatabaseLine::findByKey('foo.fourth')->value);
        $this->assertSame('', app('translator')->get('foo.fourth'));
    }

}
