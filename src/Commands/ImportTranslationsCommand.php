<?php

namespace Thinktomorrow\Squanto\Commands;

use Symfony\Component\Console\Helper\Table;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Trans;
use Thinktomorrow\Squanto\Handlers\ImportLaravelTranslations;
use Thinktomorrow\Squanto\Handlers\LaravelTranslationsReader;
use Thinktomorrow\Squanto\Domain\Page;
use Thinktomorrow\Squanto\Handlers\SaveTranslationsToDisk;
use Illuminate\Console\Command;

class ImportTranslationsCommand extends Command
{
    private $importer;

    public function __construct(ImportLaravelTranslations $importer)
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

return;
        foreach ($locales as $locale) {
        // Get all our translations files
            $lines = app(LaravelTranslationsReader::class)->readLoosely($locale, $groups);

            foreach ($groups as $slug) {
                if (!$group = Page::findBySlug($slug)) {
                    $group = Page::make($slug);
                }

                if (!isset($lines[$slug])) {
                    continue;
                }

                foreach ($lines[$slug] as $key => $value) {
                    $key = $group->slug.'.'.$key;

                    if (!$transline = Trans::findByKey($key)) {
                        $transline = Trans::make($key, $group->id, null, null, Trans::suggestType($value));
                    }

                    $currentline = $transline->getTranslation($locale, false);

                    if (!$currentline) {
                        $transline->saveTranslation($locale, 'value', $value);
                    } elseif ($currentline->value !== $value) {
                    // Notify a possible change
                        $this->info($key. ' '.strtoupper($locale) .' translation has been changed.');
                        $this->comment("Original value:");
                        $this->line($currentline->value);
                        $this->comment("New value:");
                        $this->line($value);
                        if (!$this->confirm('Overwrite?', false)) {
                        //
                        } else {
                            $transline->saveTranslation($locale, 'value', $value);
                        }
                    }
                }
            }
        }

        $this->info('Import finished.');

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
