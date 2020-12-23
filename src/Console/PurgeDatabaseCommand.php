<?php

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Disk\Query\DiskLinesRepository;
use Thinktomorrow\Squanto\Database\Query\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;
use Thinktomorrow\Squanto\Database\Application\RemoveDatabaseLine;

class PurgeDatabaseCommand extends Command
{
    protected $signature = 'squanto:purge
                                {--dry : Run the purge in dry mode without actually removing from the database}';

    protected $description = 'Remove database lines that are no longer present in the language files.';

    /**
     * @var DiskLinesRepository 
     */
    private DiskLinesRepository $diskLinesRepository;

    /**
     * @var DatabaseLinesRepository 
     */
    private DatabaseLinesRepository $databaseLinesRepository;

    /**
     * @var RemoveDatabaseLine 
     */
    private RemoveDatabaseLine $removeDatabaseLine;

    /**
     * @var CacheDatabaseLines 
     */
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

        $databaseLines = $this->databaseLinesRepository->all();
        $diskLines = $this->diskLinesRepository->all();

        $databaseLines->each(
            function (Line $line) use ($diskLines, &$purgedRows) {
                if(!$diskLines->exists($line->keyAsString())) {
                    $this->removeDatabaseLine->handle($line);

                    $purgedRows[] = [
                    $line->keyAsString(),
                    $this->outputTranslations($line->values())
                    ];
                }
            }
        );

        if(count($purgedRows) > 0) {
            $this->info(count($purgedRows) . ' obsolete lines purged from database.');
            $this->displayTable(['line', 'translations'], $purgedRows);
            $this->info('Finished. Everything is back in sync!');

            $this->cacheDatabaseLines->handle();
            $this->info('Cached translation files refreshed.');

        } else {
            $this->info('No obsolete lines found in database. All clean!');
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
