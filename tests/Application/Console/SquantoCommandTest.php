<?php

namespace Thinktomorrow\SquantoTests\Application\Console;

use Thinktomorrow\SquantoTests\TestCase;

class SquantoCommandTest extends TestCase
{
    public function test_it_can_run_the_interactive_squanto_command(): void
    {
        $this->artisan('squanto')
            ->expectsChoice('What would you like to do?', 'check', [
                'Check translation sync status',
                'Pull database translations to language files',
                'Purge obsolete database translations',
                'Push language file translations to database',
                'Refresh database translation cache',
                'Reset database translations',
                'cache',
                'check',
                'pull',
                'purge',
                'push',
                'reset',
            ])
            ->assertExitCode(0);
    }
}
