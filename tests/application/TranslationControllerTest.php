<?php

namespace Thinktomorrow\Squanto\Tests;

use Illuminate\Http\Request;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Manager\Http\Controllers\TranslationController;

class TranslationControllerTest extends TestCase
{
    public function setUp(): void
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


    /** @test */
    public function malicious_code_is_stripped()
    {
        $page = Page::make('foo');
        $line = Line::make('foo.fourth');
        $line->saveValue('nl','<div>bazz</div>');
        $line->saveSuggestedType();

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => 'bazz<img src="javascript:evil();" onload="evil();" />']]]);

        app(TranslationController::class)->update($request, $page->id);

        $this->assertSame('bazz', Line::findByKey('foo.fourth')->value);
    }

    /** @test */
    public function html_is_added()
    {
        $page = Page::make('foo');
        $line = Line::make('foo.fourth');
        $line->saveValue('nl','<b>bazz</b>');
        $line->saveSuggestedType();

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => '<div>Centered</div>']]]);

        app(TranslationController::class)->update($request, $page->id);

        $this->assertSame('<div>Centered</div>', Line::findByKey('foo.fourth')->value);
    }

    /** @test */
    public function translation_replace_parameter_can_be_passed_in_anchor_tag()
    {
        $page = Page::make('foo');
        $line = Line::make('foo.fourth');
        $line->saveValue('nl','<b>bazz</b>');
        $line->saveSuggestedType();

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => '<a href=":param">:param</a>']]]);

        app(TranslationController::class)->update($request, $page->id);

        $this->assertSame('<a href=":param">:param</a>', Line::findByKey('foo.fourth')->value);
    }


    /** @test */
    public function missing_end_tags_are_added()
    {
        $page = Page::make('foo');
        $line = Line::make('foo.fourth');
        $line->saveValue('nl','<div>bazz</div>');
        $line->saveSuggestedType();

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["trans" => [ "nl" => [$line->id => '<div>Centered']]]);

        app(TranslationController::class)->update($request, $page->id);

        $this->assertSame('<div>Centered</div>', Line::findByKey('foo.fourth')->value);
    }

}
