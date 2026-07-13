<?php

namespace Thinktomorrow\SquantoTests\Application\Manager;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Manager\Http\ManagerController;
use Thinktomorrow\Squanto\Squanto;
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

    public function test_it_can_view_edit_for_a_namespaced_page_slug(): void
    {
        $this->registerPluginSource();

        DatabaseLine::create([
            'key' => 'general.title',
            'values' => ['value' => [
                'nl' => 'App title',
            ]],
        ]);

        DatabaseLine::create([
            'key' => 'chief-form-plugin::general.title',
            'values' => ['value' => [
                'nl' => 'Plugin title',
            ]],
        ]);

        /** @var View $response */
        $response = app(ManagerController::class)->edit('chief-form-plugin::general');

        $this->assertCount(1, $response->getData()['lines']);
        $this->assertSame('chief-form-plugin::general.title', $response->getData()['lines'][0]->key());
        $this->assertSame('Form plugin', $response->getData()['page']->sourceLabel());
    }

    public function test_it_can_generate_an_edit_route_for_a_namespaced_page_slug(): void
    {
        $this->assertStringContainsString(
            'translations/chief-form-plugin::general/edit',
            route('squanto.edit', 'chief-form-plugin::general')
        );
    }

    public function test_it_can_store_a_translation()
    {
        DatabaseLine::create([
            'key' => 'foo.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]],
        ]);

        // mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(['squanto' => ['foo.bar' => ['nl' => 'bazz & foo']]]);

        app(ManagerController::class)->update($request, 'foo');

        $this->assertEquals('bazz & foo', DatabaseLine::findByKey(LineKey::fromString('foo.bar'))->dynamic('value', 'nl'));
        $this->assertSame('bazz & foo', app('translator')->get('foo.bar'));
    }

    public function test_it_can_search_translations_by_key(): void
    {
        DatabaseLine::create([
            'key' => 'home.hero.title',
            'values' => ['value' => [
                'nl' => 'Welkom',
            ]],
        ]);

        DatabaseLine::create([
            'key' => 'about.hero.title',
            'values' => ['value' => [
                'nl' => 'Over ons',
            ]],
        ]);

        $request = Request::create('/admin/translations', 'GET', ['search' => 'home.hero']);

        /** @var View $response */
        $response = app(ManagerController::class)->index($request);

        $results = $response->getData()['results'];

        $this->assertCount(1, $results);
        $this->assertSame('home.hero.title', $results[0]->key());
    }

    public function test_it_can_search_translations_by_value(): void
    {
        DatabaseLine::create([
            'key' => 'home.hero.title',
            'values' => ['value' => [
                'nl' => 'Welkom',
            ]],
        ]);

        $request = Request::create('/admin/translations', 'GET', ['search' => 'welkom']);

        /** @var View $response */
        $response = app(ManagerController::class)->index($request);

        $results = $response->getData()['results'];

        $this->assertCount(1, $results);
        $this->assertSame('home.hero.title', $results[0]->key());
    }

    public function test_it_returns_default_page_overview_for_empty_search(): void
    {
        $request = Request::create('/admin/translations', 'GET', ['search' => '']);

        /** @var View $response */
        $response = app(ManagerController::class)->index($request);

        $this->assertSame('', $response->getData()['searchTerm']);
        $this->assertIsArray($response->getData()['pages']);
        $this->assertCount(0, $response->getData()['results']);
    }

    public function test_it_groups_the_page_overview_by_source_label(): void
    {
        $this->registerPluginSource();

        $request = Request::create('/admin/translations', 'GET', ['search' => '']);

        /** @var View $response */
        $response = app(ManagerController::class)->index($request);

        $pageGroups = $response->getData()['pageGroups'];

        $this->assertArrayHasKey('App', $pageGroups);
        $this->assertArrayHasKey('Form plugin', $pageGroups);
        $this->assertSame('chief-form-plugin::general', $pageGroups['Form plugin'][0]->slug());
    }

    public function test_an_empty_line_is_intentionally_kept_empty()
    {
        DatabaseLine::create([
            'key' => 'foo.bar',
            'values' => ['value' => [
                'nl' => 'bazz',
            ]],
        ]);

        // mocking a request + call since we have no full laravel application in this package
        $request = Request::capture()->replace(['squanto' => ['foo.bar' => ['nl' => '']]]);

        app(ManagerController::class)->update($request, 'foo');

        $this->assertSame('', DatabaseLine::findByKey(LineKey::fromString('foo.bar'))->dynamic('value', 'nl'));
        $this->assertSame('', app('translator')->get('foo.bar'));
    }

    private function registerPluginSource(): void
    {
        Squanto::register(dirname((string) config('squanto.lang_path')).'/plugin/lang', 'chief-form-plugin', 'Form plugin');
    }
}
