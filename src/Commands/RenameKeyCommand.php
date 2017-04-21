<?php

namespace Thinktomorrow\Squanto\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Helper\Table;
use Thinktomorrow\Squanto\Application\Cache\CachedTranslationFile;
use Thinktomorrow\Squanto\Application\Rename\RenameKey;
use Thinktomorrow\Squanto\Application\Rename\RenameKeysInFiles;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Services\LineUsage;

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

    /**
     * @var LineUsage
     */
    private $usage;

    public function __construct(RenameKey $renameKey, RenameKeysInFiles $renameKeysInFiles, LineUsage $usage)
    {
        parent::__construct();

        $this->renameKey = $renameKey;
        $this->renameKeysInFiles = $renameKeysInFiles;
        $this->usage = $usage;
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

        $usages = $this->usage->getByKey($oldKey);
        $usageCount = count($usages);
        if($usageCount < 1)
        {
            $this->info('No usages found of the ['.$oldKey.'] translation key.');
        }
        else{
            $this->displayUsages($oldKey, $usages);

            // Optionally rename occurrences in application
            if($this->option('files'))
            {
                $this->renameKeysInFiles->handle($oldKey, $newKey);
            }
        }

        $this->info('Translation Key renamed to '. $newKey.'.' .($usageCount > 0) ? ' Also changed the '.$usageCount.' usage'.$usageCount<2?'':'s'.' found in the application files.' : '');

        // Recache results
        app(CachedTranslationFile::class)->delete()->write();
        $this->info('Translation cache refreshed.');

        $this->output->writeln('');
    }

    private function displayUsages($key, $usages)
    {
        $rows = [];
        foreach($usages as $usage)
        {
            $rows[] = [$usage['path'], $usage['function']];
        }

        $this->info('Found '. count($usages) . ' usages of the ['.$key.'] translation key.');
        $table = new Table($this->output);
        $table->setHeaders(['File', 'function']);
        $table->setHeaders(['File', 'function']);
        $table->setRows($rows);
        $table->render();
    }

}
