<?php

namespace Thinktomorrow\Squanto\Console;

use Thinktomorrow\Squanto\Database\Application\CacheDatabaseLines;

class CacheDatabaseCommand extends Command
{
    protected $signature = 'squanto:cache';

    protected $description = 'Cache the database line.';

    /**
     * @var CacheDatabaseLines 
     */
    private CacheDatabaseLines $cacheDatabaseLines;

    public function __construct(CacheDatabaseLines $cacheDatabaseLines)
    {
        parent::__construct();

        $this->cacheDatabaseLines = $cacheDatabaseLines;
    }

    public function handle()
    {
            $this->cacheDatabaseLines->handle();

            $this->info('Database lines are cached.');
    }
}
