<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Disk\DiskLinesRepository;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\multiselect;
use function Laravel\Prompts\select;

class SquantoCommand extends Command
{
    protected $signature = 'squanto';

    protected $description = 'Interactively manage Squanto translations.';

    public function __construct(private DiskLinesRepository $diskLinesRepository)
    {
        parent::__construct();
    }

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
            scroll: 6,
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
        $targets = $this->askTargets();
        $reset = confirm('Reset pulled database records afterwards?', default: false);
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->callForTargets('squanto:pull', $targets, [
            '--reset' => $reset,
            '--dry' => $dry,
            '--yes' => true,
        ]);
    }

    private function runPush(): int
    {
        $targets = $this->askTargets();
        $force = confirm('Overwrite existing database records?', default: false);
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->callForTargets('squanto:push', $targets, [
            '--force' => $force,
            '--dry' => $dry,
            '--yes' => true,
        ]);
    }

    private function runReset(): int
    {
        $targets = $this->askTargets();
        $dry = confirm('Run as dry-run first?', default: true);

        return $this->callForTargets('squanto:reset', $targets, [
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

    private function askTargets(): array
    {
        $targets = multiselect(
            label: 'Which targets?',
            options: $this->targetOptions(),
            default: ['all'],
            scroll: 10,
            required: 'Select at least one target.',
            hint: 'Use space to toggle targets. Selecting all overrides other choices.',
        );

        return in_array('all', $targets, true) ? ['all'] : $targets;
    }

    private function targetOptions(): array
    {
        $targets = [];

        $this->diskLinesRepository->all()->each(function ($line) use (&$targets) {
            $target = $this->targetFromKey($line->keyAsString());

            if (! in_array($target, ['all', 'app'], true)) {
                $targets[] = $target;
            }
        });

        $targets = array_values(array_unique($targets));
        sort($targets);

        return array_merge([
            'all' => 'All translations',
            'app' => 'Application translations',
        ], array_combine($targets, $targets) ?: []);
    }

    private function targetFromKey(string $key): string
    {
        if (str_contains($key, '::')) {
            [$namespace, $lineKey] = explode('::', $key, 2);

            return $namespace.'::'.explode('.', $lineKey, 2)[0];
        }

        return explode('.', $key, 2)[0];
    }

    private function callForTargets(string $command, array $targets, array $arguments): int
    {
        $exitCode = 0;

        foreach ($targets as $target) {
            $exitCode = max($exitCode, $this->call($command, array_merge(['target' => $target], $arguments)));
        }

        return $exitCode;
    }
}
