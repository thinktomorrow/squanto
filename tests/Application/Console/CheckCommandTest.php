<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\SquantoTests\TestCase;

class CheckCommandTest extends TestCase
{
    public function test_it_can_check_database_lines(): void
    {
        config()->set('squanto.locales', ['nl']);

        $this->artisan('squanto:check')
            ->assertExitCode(0);
    }

    public function test_it_can_check_obsolete_database_lines(): void
    {
        app(AddDatabaseLine::class)->handle(Line::fromRaw('foo.xxx', ['nl' => 'obsolete value']));

        $this->artisan('squanto:check')
            ->assertExitCode(0);
    }
}
