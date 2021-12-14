<?php

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Disk\DiskLinesRepository;
use Thinktomorrow\Squanto\Disk\DiskMetadataRepository;
use Thinktomorrow\Squanto\Database\Application\UpdateMetadata;
use Thinktomorrow\Squanto\Database\Application\AddDatabaseLine;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;

class PushToDatabaseCommand extends Command
{
    protected $signature = 'squanto:push
                                {--dry : Run the push in dry mode without actual modifications to the database}';

    protected $description = 'Push all language lines to the database. Existing database values will remain untouched.';

    /**
     * @var DiskLinesRepository
     */
    private DiskLinesRepository $diskLinesRepository;

    /**
     * @var \Thinktomorrow\Squanto\Database\DatabaseLinesRepository
     */
    private DatabaseLinesRepository $databaseLinesRepository;

    /**
     * @var AddDatabaseLine
     */
    private AddDatabaseLine $addDatabaseLine;

    /**
     * @var UpdateMetadata
     */
    private UpdateMetadata $updateMetadata;

    /**
     * @var DiskMetadataRepository
     */
    private DiskMetadataRepository $diskMetadataRepository;

    /**
     * @var CacheDatabaseLines
     */
    private CacheDatabaseLines $cacheDatabaseLines;

    public function __construct(DiskLinesRepository $diskLinesRepository, DatabaseLinesRepository $databaseLinesRepository, AddDatabaseLine $addDatabaseLine, UpdateMetadata $updateMetadata, DiskMetadataRepository $diskMetadataRepository, CacheDatabaseLines $cacheDatabaseLines)
    {
        parent::__construct();

        $this->diskLinesRepository = $diskLinesRepository;
        $this->databaseLinesRepository = $databaseLinesRepository;
        $this->addDatabaseLine = $addDatabaseLine;
        $this->updateMetadata = $updateMetadata;
        $this->diskMetadataRepository = $diskMetadataRepository;
        $this->cacheDatabaseLines = $cacheDatabaseLines;
    }

    public function handle()
    {
        $newlyAddedRows = [];

        // Get all language lines
        $diskLines = $this->diskLinesRepository->all();
        $databaseLines = $this->databaseLinesRepository->all();

        // Metadata
        $metadataCollection = $this->diskMetadataRepository->all($diskLines);

        $diskLines->each(
            function (Line $line) use ($databaseLines, $metadataCollection, &$newlyAddedRows) {
                if($databaseLines->exists($line->keyAsString())) {
                    if($metadata = $metadataCollection->find($line->keyAsString())) {
                        $this->updateMetadata->handle(LineKey::fromString($line->keyAsString()), $metadata);
                    }
                } else {

                    $metadata = $metadataCollection->find($line->keyAsString());

                    // Create new database entry + insert default values
                    $this->addDatabaseLine->handle($line, $metadata);

                    $newlyAddedRows[] = [
                       $line->keyAsString(),
                       $this->outputTranslations($line->values())
                    ];
                }
            }
        );

        if(count($newlyAddedRows) > 0) {
            $this->info(count($newlyAddedRows) . ' new lines pushed to database.');
            $this->displayTable(['line', 'translations'], $newlyAddedRows);
            $this->info('Finished. Everything is back in sync!');

            $this->cacheDatabaseLines->handle();
            $this->info('Cached translation files refreshed.');

        } else {
            $this->info('No new lines found to push to database. Any metadata has been updated. That\'s great!');
        }
    }

    private function outputTranslations(array $values): string
    {
        $output = [];
        foreach($values as $locale => $value){
            $output[] = $locale.': '.$value;
        }

        return implode("\n", $output);
    }
}
