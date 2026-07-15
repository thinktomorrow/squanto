<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\Application\RemoveDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\TranslationTarget;

class ResetDatabaseCommand extends Command
{
    protected $signature = 'squanto:reset
                                {target=all : Translation key target. Use all, app or a translation key prefix}
                                {--dry : Run the reset in dry mode without actually removing from the database}
                                {--yes : Skip confirmation}';

    protected $description = 'Remove database translations for a target so language files become the source of truth again.';

    public function __construct(
        private DatabaseLinesRepository $databaseLinesRepository,
        private RemoveDatabaseLine $removeDatabaseLine,
        private CacheDatabaseLines $cacheDatabaseLines
    ) {
        parent::__construct();
    }

    public function handle()
    {
        $target = TranslationTarget::fromString($this->argument('target'));
        $lines = $target->filter($this->databaseLinesRepository->all());
        $rows = [];
        $resetLines = [];

        $lines->each(function (Line $line) use (&$rows, &$resetLines) {
            $resetLines[] = $line;
            $rows[] = [
                $line->keyAsString(),
                $this->outputTranslations($line->values()),
            ];
        });

        if (count($rows) === 0) {
            $this->info('No database lines found for target ['.$target->value().'].');

            return;
        }

        if (! $this->confirmChanges('This will reset '.count($rows).' database lines for target ['.$target->value().'].')) {
            $this->info('Reset cancelled.');

            return;
        }

        $this->info(count($rows).' database lines reset.');
        $this->displayTable(['line', 'translations'], $rows);

        if (! $this->option('dry')) {
            foreach ($resetLines as $line) {
                $this->removeDatabaseLine->handle($line);
            }

            $this->cacheDatabaseLines->handle();
            $this->info('Cached translation files refreshed.');
        }

        $this->info($this->option('dry') ? 'Dry run finished. No database changes were made.' : 'Finished. Everything is back in sync!');
    }
}
