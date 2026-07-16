<?php

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\Application\RemoveDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Disk\DiskLinesRepository;
use Thinktomorrow\Squanto\Domain\Line;

class PurgeDatabaseCommand extends Command
{
    protected $signature = 'squanto:purge
                                {--dry : Run the purge in dry mode without actually removing from the database}
                                {--yes : Skip confirmation}';

    protected $description = 'Remove database lines that are no longer present in the language files.';

    private DiskLinesRepository $diskLinesRepository;

    private DatabaseLinesRepository $databaseLinesRepository;

    private RemoveDatabaseLine $removeDatabaseLine;

    private CacheDatabaseLines $cacheDatabaseLines;

    public function __construct(DiskLinesRepository $diskLinesRepository, DatabaseLinesRepository $databaseLinesRepository, RemoveDatabaseLine $removeDatabaseLine, CacheDatabaseLines $cacheDatabaseLines)
    {
        parent::__construct();

        $this->diskLinesRepository = $diskLinesRepository;
        $this->databaseLinesRepository = $databaseLinesRepository;
        $this->removeDatabaseLine = $removeDatabaseLine;
        $this->cacheDatabaseLines = $cacheDatabaseLines;
    }

    public function handle()
    {
        $purgedRows = [];
        $purgedLines = [];

        $databaseLines = $this->databaseLinesRepository->all();
        $diskLines = $this->diskLinesRepository->all();

        $databaseLines->each(
            function (Line $line) use ($diskLines, &$purgedRows, &$purgedLines) {
                if (! $diskLines->exists($line->keyAsString())) {
                    $purgedLines[] = $line;

                    $purgedRows[] = [
                        $line->keyAsString(),
                        $this->outputTranslations($line->values()),
                    ];
                }
            }
        );

        if (count($purgedRows) > 0) {
            if ($this->option('dry')) {
                $this->displaySummary('Dry run: purge obsolete database translations', [
                    ['Obsolete database records', (string) count($purgedRows)],
                    ['Action', 'Remove records that no longer exist in language files'],
                ]);
            }

            if (! $this->confirmChanges('This will purge '.count($purgedRows).' obsolete database lines.')) {
                $this->info('Purge cancelled.');

                return;
            }

            $this->info(count($purgedRows).' obsolete lines purged from database.');
            $this->displayTable(['line', 'translations'], $purgedRows);

            if (! $this->option('dry')) {
                foreach ($purgedLines as $line) {
                    $this->removeDatabaseLine->handle($line);
                }

                $this->cacheDatabaseLines->handle();
                $this->info('Cached translation files refreshed.');
            }

            $this->info($this->option('dry') ? 'Dry run finished. No database changes were made.' : 'Finished. Everything is back in sync!');
        } else {
            $this->info('No obsolete lines found in database. All clean!');
        }
    }
}
