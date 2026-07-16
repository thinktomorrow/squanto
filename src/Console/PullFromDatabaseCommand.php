<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\Application\RemoveDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Disk\WriteLanguageLines;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\TranslationTarget;

class PullFromDatabaseCommand extends Command
{
    protected $signature = 'squanto:pull
                                {target=all : Translation key target. Use all, app or a translation key prefix}
                                {--reset : Remove pulled database entries after writing them to language files}
                                {--dry : Run the pull in dry mode without actual modifications}
                                {--yes : Skip confirmation}';

    protected $description = 'Pull database translations into the language files.';

    public function __construct(
        private DatabaseLinesRepository $databaseLinesRepository,
        private WriteLanguageLines $writeLanguageLines,
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
        $pulledLines = [];

        $lines->each(function (Line $line) use (&$rows, &$pulledLines) {
            $pulledLines[] = $line;
            $rows[] = [
                $line->keyAsString(),
                $this->outputTranslations($line->values()),
            ];
        });

        if (count($rows) === 0) {
            $this->info('No database lines found for target ['.$target->value().'].');

            return;
        }

        if ($this->option('dry')) {
            $this->displaySummary('Dry run: pull database translations', [
                ['Target', $target->value()],
                ['Key match', $target->keyMatchDescription()],
                ['Matching files', $this->summarizeFiles($this->writeLanguageLines->pathsForLines($lines))],
                ['Matching database records', (string) count($rows)],
                ['Action', $this->option('reset') ? 'Write database values to language files, then remove database records' : 'Write database values to language files'],
            ]);
        }

        $summary = 'This will pull '.count($rows).' database lines to language files for target ['.$target->value().'].'.($this->option('reset') ? ' Pulled database lines will be removed afterwards.' : '');
        if (! $this->confirmChanges($summary)) {
            $this->info('Pull cancelled.');

            return;
        }

        $this->info(count($rows).' database lines pulled to language files.');
        $this->displayTable(['line', 'translations'], $rows);

        if (! $this->option('dry')) {
            $this->writeLanguageLines->handle($lines);

            if ($this->option('reset')) {
                foreach ($pulledLines as $line) {
                    $this->removeDatabaseLine->handle($line);
                }
            }

            $this->cacheDatabaseLines->handle();
            $this->info('Cached translation files refreshed.');
        }

        $this->info($this->option('dry') ? 'Dry run finished. No files or database records were changed.' : 'Finished. Everything is back in sync!');
    }
}
