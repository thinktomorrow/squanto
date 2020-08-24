<?php

namespace Thinktomorrow\SquantoTests\Application\Manager;

use Illuminate\Http\Request;
use Thinktomorrow\SquantoTests\TestCase;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Manager\Http\ManagerController;

class TranslationControllerTest extends TestCase
{
    /** @test */
    public function it_can_store_a_translation()
    {
        DatabaseLine::create([
            'key' => 'foo.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]]
        ]);

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["squanto" => ["foo.bar" => [ "nl" => 'bazz & foo']]]);

        app(ManagerController::class)->update($request, 'foo');

        $this->assertEquals('bazz & foo', DatabaseLine::findByKey(LineKey::fromString('foo.bar'))->dynamic('value','nl'));
    }

    /** @test */
    public function an_empty_line_is_intentionally_kept_empty()
    {
        DatabaseLine::create([
            'key' => 'foo.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]]
        ]);

        //mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(["squanto" => ["foo.bar" => [ "nl" => '']]]);

        app(ManagerController::class)->update($request, 'foo');

        $this->assertSame('', DatabaseLine::findByKey(LineKey::fromString('foo.bar'))->dynamic('value','nl'));
        $this->assertSame('', app('translator')->get('foo.bar'));
    }

}
