<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Squanto;
use Thinktomorrow\SquantoTests\TestCase;

class PullFromDatabaseCommandTest extends TestCase
{
    private DatabaseLinesRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    public function test_it_can_pull_database_lines_to_language_files(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.title', ['nl' => 'database titel']));

        $this->artisan('squanto:pull about.title --yes');

        $translations = require config('squanto.lang_path').'/nl/about.php';

        $this->assertEquals('database titel', $translations['title']);
        $this->assertEquals('omschrijving', $translations['content']);
        $this->assertTrue($this->repository->exists('about.title'));
    }

    public function test_it_can_pull_and_reset_database_lines(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.title', ['nl' => 'database titel']));

        $this->artisan('squanto:pull about.title --reset --yes');

        $translations = require config('squanto.lang_path').'/nl/about.php';

        $this->assertEquals('database titel', $translations['title']);
        $this->assertFalse($this->repository->exists('about.title'));
    }

    public function test_it_can_pull_namespaced_database_lines(): void
    {
        Squanto::register(dirname((string) config('squanto.lang_path')).'/plugin/lang', 'chief-form-plugin', 'Form plugin');
        app(AddDatabaseLine::class)->handle(Line::fromRaw('chief-form-plugin::general.title', ['nl' => 'Database plugin titel']));

        $this->artisan('squanto:pull chief-form-plugin::general.title --yes');

        $translations = require dirname((string) config('squanto.lang_path')).'/plugin/lang/nl/general.php';

        $this->assertEquals('Database plugin titel', $translations['title']);
    }

    public function test_dry_pull_does_not_change_language_files(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('about.title', ['nl' => 'database titel']));

        $this->artisan('squanto:pull about.title --dry')
            ->assertExitCode(0);

        $translations = require config('squanto.lang_path').'/nl/about.php';

        $this->assertEquals('titel', $translations['title']);
    }
}
