<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\SquantoTests\TestCase;

class PushToDatabaseCommandTest extends TestCase
{
    use DatabaseTransactions, DatabaseMigrations;

    /** @var mixed */
    private $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    /** @test */
    public function it_can_push_disk_lines_to_database()
    {
        config()->set('squanto.locales', ['en', 'nl']);

        $this->artisan('squanto:push');

        $databaseLines = $this->repository->all();

        $items = $this->getPrivateProperty($databaseLines, 'items');

        $this->assertCount(7, $items);
        $this->assertCount(7, $databaseLines->values('nl'));
        $this->assertCount(7, $databaseLines->values('en'));

        // Missing 'content' value for EN
        $this->assertNull($databaseLines->values('en')['about.content']);
    }

    /** @test */
    public function it_does_not_overwrite_existing_database_translations()
    {
        DatabaseLine::create([
            'key' => 'about.title',
            'values' => ['value' => [
                'nl' => 'custom titel',
            ]],
        ]);

        config()->set('squanto.locales', ['nl']);
        $this->artisan('squanto:push');

        $this->assertEquals('custom titel', $this->repository->find('about.title')->value('nl'));
    }

    /** @test */
    public function it_does_not_add_translations_when_the_database_entry_already_exists()
    {
        DatabaseLine::create([
            'key' => 'about.title',
            'values' => ['value' => [
                'nl' => 'custom titel',
            ]],
        ]);

        config()->set('squanto.locales', ['nl', 'en']);
        $this->artisan('squanto:push');

        $this->assertNull($this->repository->find('about.title')->value('en'));
    }

    /** @test */
    public function it_does_add_translations_when_the_database_entry_exists_as_soft_deleted_entry()
    {
        $record = DatabaseLine::create([
            'key' => 'about.title',
            'values' => ['value' => [
                'nl' => 'custom titel',
            ]],
        ]);

        $record->delete();

        config()->set('squanto.locales', ['nl', 'en']);
        $this->artisan('squanto:push');

        $this->assertEquals('titel', $this->repository->find('about.title')->value('nl'));
        $this->assertEquals('title', $this->repository->find('about.title')->value('en'));
    }
}
