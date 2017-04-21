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

class UsageCommand extends Command
{
    /**
     * @var LineUsage
     */
    private $usage;

    public function __construct(LineUsage $usage)
    {
        parent::__construct();

        $this->usage = $usage;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squanto:usage {key}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'view usages of a translation key';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $key = $this->argument('key');

        if(!$line = Line::findByKey($key))
        {
            throw new InvalidArgumentException('No translation line found by key: '.$key);
        }

        $usages = $this->usage->getByKey($key);
        $usageCount = count($usages);
        if($usageCount < 1)
        {
            $this->info('No usages found of the ['.$key.'] translation key.');
        }
        else
        {
            $this->displayUsages($key, $usages);
        }

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
