<?php

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\Application\UpdateDatabaseLine;
use Thinktomorrow\Squanto\Database\Application\UpdateMetadata;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Disk\DiskLinesRepository;
use Thinktomorrow\Squanto\Disk\DiskMetadataRepository;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\TranslationTarget;

class PushToDatabaseCommand extends Command
{
    protected $signature = 'squanto:push
                                {target=all : Translation key target. Use all, app or a translation key prefix}
                                {--force : Overwrite existing database values with file translations}
                                {--dry : Run the push in dry mode without actual modifications to the database}
                                {--yes : Skip confirmation}';

    protected $description = 'Push all language lines to the database. Existing database values will remain untouched.';

    private DiskLinesRepository $diskLinesRepository;

    private DatabaseLinesRepository $databaseLinesRepository;

    private AddDatabaseLine $addDatabaseLine;

    private UpdateDatabaseLine $updateDatabaseLine;

    private UpdateMetadata $updateMetadata;

    private DiskMetadataRepository $diskMetadataRepository;

    private CacheDatabaseLines $cacheDatabaseLines;

    public function __construct(DiskLinesRepository $diskLinesRepository, DatabaseLinesRepository $databaseLinesRepository, AddDatabaseLine $addDatabaseLine, UpdateDatabaseLine $updateDatabaseLine, UpdateMetadata $updateMetadata, DiskMetadataRepository $diskMetadataRepository, CacheDatabaseLines $cacheDatabaseLines)
    {
        parent::__construct();

        $this->diskLinesRepository = $diskLinesRepository;
        $this->databaseLinesRepository = $databaseLinesRepository;
        $this->addDatabaseLine = $addDatabaseLine;
        $this->updateDatabaseLine = $updateDatabaseLine;
        $this->updateMetadata = $updateMetadata;
        $this->diskMetadataRepository = $diskMetadataRepository;
        $this->cacheDatabaseLines = $cacheDatabaseLines;
    }

    public function handle()
    {
        $newlyAddedRows = [];
        $updatedRows = [];
        $newLines = [];
        $existingLines = [];
        $target = TranslationTarget::fromString($this->argument('target'));

        // Get all language lines
        $diskLines = $target->filter($this->diskLinesRepository->all());
        $databaseLines = $this->databaseLinesRepository->all();

        // Metadata
        $metadataCollection = $this->diskMetadataRepository->all($diskLines);

        $diskLines->each(
            function (Line $line) use ($databaseLines, $metadataCollection, &$newlyAddedRows, &$updatedRows, &$newLines, &$existingLines) {
                $metadata = $metadataCollection->find($line->keyAsString());

                if ($databaseLines->exists($line->keyAsString())) {
                    $existingLines[] = [$line, $metadata];

                    if ($this->option('force')) {
                        $updatedRows[] = [
                            $line->keyAsString(),
                            $this->outputTranslations($line->values()),
                        ];
                    }
                } else {
                    $newLines[] = [$line, $metadata];

                    $newlyAddedRows[] = [
                        $line->keyAsString(),
                        $this->outputTranslations($line->values()),
                    ];
                }
            }
        );

        $changeCount = count($newlyAddedRows) + count($updatedRows);

        if ($changeCount > 0 && ! $this->confirmChanges('This will push '.$changeCount.' lines to the database for target ['.$target->value().'].')) {
            $this->info('Push cancelled.');

            return;
        }

        if (! $this->option('dry')) {
            foreach ($existingLines as [$line, $metadata]) {
                if ($metadata) {
                    $this->updateMetadata->handle(LineKey::fromString($line->keyAsString()), $metadata);
                }

                if ($this->option('force')) {
                    $this->updateDatabaseLine->handle($line);
                }
            }

            foreach ($newLines as [$line, $metadata]) {
                $this->addDatabaseLine->handle($line, $metadata);
            }
        }

        if (count($newlyAddedRows) > 0) {
            $this->info(count($newlyAddedRows).' new lines pushed to database.');
            $this->displayTable(['line', 'translations'], $newlyAddedRows);
        }

        if (count($updatedRows) > 0) {
            $this->info(count($updatedRows).' existing lines updated in database.');
            $this->displayTable(['line', 'translations'], $updatedRows);
        }

        if ($changeCount > 0) {
            if (! $this->option('dry')) {
                $this->cacheDatabaseLines->handle();
                $this->info('Cached translation files refreshed.');
            }

            $this->info($this->option('dry') ? 'Dry run finished. No database changes were made.' : 'Finished. Everything is back in sync!');
        } else {
            $this->info('No lines found to push to database. Any metadata has been updated. That\'s great!');
        }
    }
}
