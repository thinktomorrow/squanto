<?php

namespace Thinktomorrow\SquantoTests\Unit\Database;

use Illuminate\Support\Collection;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Exceptions\NotFoundDatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\SquantoTests\TestCase;

class DatabaseLineTest extends TestCase
{
    /** @var mixed */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app()->make(DatabaseLinesRepository::class);
    }

    public function test_a_database_line_holds_all_translations_in_a_dynamic_field()
    {
        DatabaseLine::create(['key' => 'foo.bar', 'values' => ['value' => [
            'nl' => 'nl value',
            'en' => 'en value',
        ]]]);

        $databaseLine = DatabaseLine::findByKey(LineKey::fromString('foo.bar'));

        $this->assertEquals('nl value', $databaseLine->dynamic('value', 'nl'));
        $this->assertEquals('en value', $databaseLine->dynamic('value', 'en'));

        // Default localized value is based on current locale
        app()->setLocale('en');
        $this->assertEquals('en value', $databaseLine->value);
    }

    public function test_it_can_check_if_database_line_exists()
    {
        DatabaseLine::create(['key' => 'foo.bar']);

        $this->assertTrue($this->repository->exists('foo.bar'));
        $this->assertFalse($this->repository->exists('foo.xxx'));
    }

    public function test_a_non_found_key_throws_exception()
    {
        $this->expectException(NotFoundDatabaseLine::class);

        $this->repository->find('foo.xxx');
    }

    public function test_it_can_fetch_all_lines_from_database()
    {
        DatabaseLine::create(['key' => 'foo.bar']);
        DatabaseLine::create(['key' => 'baz.boo']);

        $lines = $this->repository->all();

        $this->assertEquals(new Lines([
            Line::fromRaw('foo.bar', []),
            Line::fromRaw('baz.boo', []),
        ]), $lines);
    }

    public function test_it_can_fetch_all_lines_as_eloquent_models_from_database()
    {
        DatabaseLine::create(['key' => 'foo.bar']);
        DatabaseLine::create(['key' => 'baz.boo']);

        $lines = $this->repository->modelsStartingWith('foo');

        $this->assertInstanceOf(Collection::class, $lines);
        $this->assertCount(1, $lines);
        $this->assertEquals('foo.bar', $lines->first()->key);
    }
}
