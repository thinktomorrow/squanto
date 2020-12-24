<?php

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Disk\DiskLinesRepository;
use Thinktomorrow\Squanto\Disk\DiskMetadataRepository;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;

class CheckCommand extends Command
{
    protected $signature = 'squanto:check';

    protected $description = 'Check if your database lines are up to date.';

    /**
     * @var DiskLinesRepository
     */
    private DiskLinesRepository $diskLinesRepository;

    /**
     * @var DatabaseLinesRepository
     */
    private DatabaseLinesRepository $databaseLinesRepository;

    /**
     * @var DiskMetadataRepository
     */
    private DiskMetadataRepository $diskMetadataRepository;

    public function __construct(DiskLinesRepository $diskLinesRepository, DatabaseLinesRepository $databaseLinesRepository, DiskMetadataRepository $diskMetadataRepository)
    {
        parent::__construct();

        $this->diskLinesRepository = $diskLinesRepository;
        $this->databaseLinesRepository = $databaseLinesRepository;
        $this->diskMetadataRepository = $diskMetadataRepository;
    }

    public function handle()
    {
        $diskLines = $this->diskLinesRepository->all();
        $databaseLines = $this->databaseLinesRepository->all();

        $pushableRows = [];
        $purgableRows = [];

        $diskLines->each(
            function (Line $line) use ($databaseLines, &$pushableRows) {
                if(!$databaseLines->exists($line->keyAsString())) {
                    $pushableRows[] = [$line->keyAsString()];
                }
            }
        );

        $databaseLines->each(
            function (Line $line) use ($diskLines, &$purgableRows) {
                if(!$diskLines->exists($line->keyAsString())) {
                    $purgableRows[] = [$line->keyAsString()];
                }
            }
        );

        if(count($pushableRows) > 0) {
            $this->displayTable([ count($pushableRows) . ' new lines'], $pushableRows);
        }
        if(count($purgableRows) > 0) {
            $this->displayTable([count($purgableRows) . ' obsolete lines'], $purgableRows);
        }

        if(count($pushableRows) > 0) {
            $this->info('Run squanto:push to push the new lines to the database.');
        } else {
            $this->info('No new lines found.');
        }

        if(count($purgableRows) > 0) {
            $this->info('Run squanto:purge to remove the obsolete lines from the database.');
        } else {
            $this->info('No obsolete lines found in database.');
        }

        if(count($pushableRows + $purgableRows) == 0) {
            $this->line('Your database is up to date!');
        }
    }
}
