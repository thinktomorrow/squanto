<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\select;
use function Laravel\Prompts\text;

class SquantoCommand extends Command
{
    protected $signature = 'squanto';

    protected $description = 'Interactively manage Squanto translations.';

    public function handle(): int
    {
        intro('Squanto translations');

        $action = select(
            label: 'What would you like to do?',
            options: [
                'pull' => 'Pull database translations to language files',
                'push' => 'Push language file translations to database',
                'reset' => 'Reset database translations',
                'purge' => 'Purge obsolete database translations',
                'check' => 'Check translation sync status',
                'cache' => 'Refresh database translation cache',
            ],
            default: 'check',
            info: fn (string $value) => match ($value) {
                'pull' => 'Write database values back to language files. Optionally reset afterwards.',
                'push' => 'Create missing database records. Optionally force overwrite existing records.',
                'reset' => 'Remove database records for a target so files become source of truth.',
                'purge' => 'Remove only obsolete database records that no longer exist in files.',
                'check' => 'Preview missing and obsolete records.',
                'cache' => 'Regenerate cached database translation files.',
                default => null,
            }
        );

        return match ($action) {
            'pull' => $this->runPull(),
            'push' => $this->runPush(),
            'reset' => $this->runReset(),
            'purge' => $this->runPurge(),
            'cache' => $this->call('squanto:cache'),
            default => $this->call('squanto:check'),
        };
    }

    private function runPull(): int
    {
        $target = $this->askTarget();
        $reset = confirm('Reset pulled database records afterwards?', default: false);
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->call('squanto:pull', [
            'target' => $target,
            '--reset' => $reset,
            '--dry' => $dry,
            '--yes' => true,
        ]);
    }

    private function runPush(): int
    {
        $target = $this->askTarget();
        $force = confirm('Overwrite existing database records?', default: false);
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->call('squanto:push', [
            'target' => $target,
            '--force' => $force,
            '--dry' => $dry,
            '--yes' => true,
        ]);
    }

    private function runReset(): int
    {
        $target = $this->askTarget();
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->call('squanto:reset', [
            'target' => $target,
            '--dry' => $dry,
            '--yes' => true,
        ]);
    }

    private function runPurge(): int
    {
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->call('squanto:purge', [
            '--dry' => $dry,
            '--yes' => true,
        ]);
    }

    private function askTarget(): string
    {
        return text(
            label: 'Which target?',
            placeholder: 'all, app, products, products.title, vendor::group',
            default: 'all',
            required: true,
        );
    }
}
