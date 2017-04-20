<?php

namespace Thinktomorrow\Squanto\Commands;

use Exception;
use Illuminate\Console\Command;
use Thinktomorrow\Squanto\Application\Cache\CachedTranslationFile;

class CacheTranslationsCommand extends Command
{
    private $cacher;
    private $locales = [];

    public function __construct(CachedTranslationFile $cacher)
    {
        parent::__construct();

        $this->cacher = $cacher;
        $this->locales = config('squanto.locales', []);
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squanto:cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear cache and rebuild it from the database translations';

    public function handle()
    {
        if (empty($this->locales)) {
            throw new Exception('No locales set for the cache rebuild. Make sure you set your locales in the squanto config file');
        }

        $this->info('Clear and rebuild squanto cache for locales ['.implode(',', $this->locales).'].');

        $this->cacher->delete()->write();

        $this->info('Translation cache refreshed.');
        $this->output->writeln('');
    }
}
