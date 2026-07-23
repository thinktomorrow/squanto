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

    public function test_it_can_choose_all_targets_from_a_checklist(): void
    {
        $this->artisan('squanto')
            ->expectsChoice('What would you like to do?', 'reset', [
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
            ->expectsChoice('Which targets?', ['all'], [
                'All translations',
                'Application translations',
                'about',
                'about',
                'all',
                'app',
                'nested',
                'nested',
            ])
            ->expectsConfirmation('Run as dry-run first?', 'yes')
            ->assertExitCode(0);
    }
}
