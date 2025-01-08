<?php

namespace Thinktomorrow\SquantoTests\Application\Manager;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Manager\Http\ManagerController;
use Thinktomorrow\SquantoTests\TestCase;

class TranslationControllerTest extends TestCase
{
    public function test_it_can_view_edit_when_pageslug_contains_a_dash()
    {
        DatabaseLine::create([
            'key' => 'foo_baz.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]],
        ]);

        /** @var View $response */
        $response = app(ManagerController::class)->edit('foo_baz');

        $this->assertCount(1, $response->getData()['lines']);
    }

    public function test_it_can_store_a_translation()
    {
        DatabaseLine::create([
            'key' => 'foo.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]],
        ]);

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["squanto" => ["foo.bar" => [ "nl" => 'bazz & foo']]]);

        app(ManagerController::class)->update($request, 'foo');

        $this->assertEquals('bazz & foo', DatabaseLine::findByKey(LineKey::fromString('foo.bar'))->dynamic('value', 'nl'));
        $this->assertSame('bazz & foo', app('translator')->get('foo.bar'));
    }

    public function test_an_empty_line_is_intentionally_kept_empty()
    {
        DatabaseLine::create([
            'key' => 'foo.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]],
        ]);

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["squanto" => ["foo.bar" => [ "nl" => '']]]);

        app(ManagerController::class)->update($request, 'foo');

        $this->assertSame('', DatabaseLine::findByKey(LineKey::fromString('foo.bar'))->dynamic('value', 'nl'));
        $this->assertSame('', app('translator')->get('foo.bar'));
    }
}
