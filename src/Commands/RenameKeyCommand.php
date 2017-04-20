<?php

namespace Thinktomorrow\Squanto\Commands;

use Exception;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;
use Thinktomorrow\Squanto\Application\Cache\CachedTranslationFile;
use Thinktomorrow\Squanto\Application\Rename\RenameKey;
use Thinktomorrow\Squanto\Application\Rename\RenameKeysInFiles;
use Thinktomorrow\Squanto\Domain\Line;

class RenameKeyCommand extends Command
{
    /**
     * @var RenameKey
     */
    private $renameKey;

    /**
     * @var RenameKeysInFiles
     */
    private $renameKeysInFiles;

    public function __construct(RenameKey $renameKey, RenameKeysInFiles $renameKeysInFiles)
    {
        parent::__construct();

        $this->renameKey = $renameKey;
        $this->renameKeysInFiles = $renameKeysInFiles;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squanto:rename {oldKey} {newKey} {--files : rename occurrences }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rename a squanto linekey';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Get line by oldKey and rename it
        // Display occurrences in viewfiles
        // if rename option is given, rename them instantly (only in local)
        // if rename option isn't given, give option to rename them now (only in local)

        $oldKey = $this->argument('oldKey');
        $newKey = $this->argument('newKey');

        if(!$line = Line::findByKey($oldKey))
        {
            throw new InvalidArgumentException('No translation line found by key: '.$oldKey);
        }

        // Rename key in database
        $this->renameKey->handle($line, $newKey);

        // Optionally rename occurrences in application
        if($this->option('files'))
        {
            $this->renameKeysInFiles->handle($oldKey, $newKey);
        }

        $this->info('Key renamed to '. $newKey);

        // Recache results
        app(CachedTranslationFile::class)->delete()->write();
        $this->info('Translation cache refreshed.');

        $this->output->writeln('');
    }

}
