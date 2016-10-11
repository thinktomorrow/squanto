<?php

namespace Thinktomorrow\Squanto\Import;

use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;

class ImportTranslationsCommand extends Command
{
    private $importer;

    public function __construct(ImportTranslations $importer)
    {
        parent::__construct();

        $this->importer = $importer;
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'squanto:import
                                {--dry : Run the import in dry mode without actual modifications to the database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import translation lines to your database.';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $locales = config('squanto.locales');

        $this->info(($this->option('dry') ? 'Simulating' : 'Starting') . ' squanto import for locales ['.implode(',',$locales).']' . ($this->option('dry') ? ' (dry mode)' : null).'.');

        foreach($locales as $locale)
        {
            $this->importer->dry($this->option('dry'))->import($locale);
        }

        $this->info('Import finished with following results:');
        $this->displayStats();

        $thanks = [
            'Squawesome. But there\'s more:',
            'Superb. Now start cracking:',
            'Squanto loves your style.',
        ];

        // Options to go further on the table stats
        $choice = $this->choice($thanks[array_rand($thanks)], [
            'y'     => 'Insert the on-hold translations one by one',
            'n'     => 'No thanks',
        ],'n');

        if('y' === $choice)
        {
            $i = 1;
            $total = count($this->importer->getStats()->getUpdatesOnHold());
            foreach($this->importer->getStats()->getUpdatesOnHold() as $key => $item)
            {
                $this->info("(".$i++ ."/".$total.")" . $item['locale'] . " translation for ".$item['key']." has changed. ");
                $this->comment("Original:");
                $this->line($item['original_value']);
                $this->comment("New:");
                $this->line($item['new_value']);
                if (!$this->confirm('Overwrite?', false)) {
                    //
                } else {
                    $this->importer->importSingleValue($item['locale'],$item['key'],$item['new_value']);
                }
            }
        }

        $this->info('Import finished.');

        // TODO: refresh cache translations from db to disk

        return;
        // Recache results
        app(SaveTranslationsToDisk::class)->clear()->handle();
        $this->info('Translation cache refreshed.');

        $this->output->writeln('');
    }

    private function displayStats()
    {
        $stats = $this->importer->getStats();

        $table = new Table($this->output);
        $table->setHeaders(['Item', '#']);

        $table->setRows([
            ['updates on hold', count($stats->getUpdatesOnHold())],
            ['inserts', count($stats->getInserts())],
            ['updates', count($stats->getUpdates())],
            ['remained the same', count($stats->getRemainedSame())],
        ]);

        $table->render();
    }
}
