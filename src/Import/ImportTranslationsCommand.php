<?php

namespace Thinktomorrow\Squanto\Import;

use Exception;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

class ImportTranslationsCommand extends Command
{
    private $importer;
    private $locales = [];

    public function __construct(ImportTranslations $importer)
    {
        parent::__construct();

        $this->importer = $importer;
        $this->locales = config('squanto.locales', []);
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
        if (empty($this->locales)) {
            throw new Exception('No locales set for the import. Make sure you set your locales in the squanto config file');
        }

        $this->info(($this->option('dry') ? 'Simulating' : 'Starting') . ' squanto import for locales ['.implode(',', $this->locales).']' . ($this->option('dry') ? ' (dry mode)' : null).'.');

        // Collect the statistics without modifying the database
        foreach ($this->locales as $locale) {
            $this->importer->dry()->import($locale);
        }

        // Insert new translations modules
        while (false === $this->handleInserts()) {
        }

        // Update changed translations
        while (false === $this->handleUpdatesOnHold()) {
        }

        $this->info('Import finished with following results:');
        $this->displayStats();

        return;

        // TODO: refresh cache translations from db to disk

        // Recache results
        app(SaveTranslationsToDisk::class)->clear()->handle();
        $this->info('Translation cache refreshed.');

        $this->output->writeln('');
    }

    private function handleInserts()
    {
        // if no inserts in pipeline we skip the inserts early on
        if (($totalInserts = count($this->importer->getStats()->getInserts())) < 1) {
            $this->warn('No new translations found');
            return true;
        }

        $answer = $this->ask(($this->option('dry') ? 'Simulate insertion of ' : 'Insert ') . $totalInserts . ' new translations? (y,n,details,?)', 'n');

        if (!$answer || in_array(strtolower($answer), ['n','no'])) {
            return true;
        }

        if ($answer == 'details') {
        // Show details and continue
            $this->displayDetails('inserts');
            return false;
        }

        if ($answer == '?') {
        // Show details and continue
            $this->line('Your options on inserting new translations:');
            $this->line('- y (yes)  Insert the new translations into the database.');
            $this->line('- n (no)   Cancel the insert.');
            $this->line('- details  Preview the new translations.');
            $this->line('- ?        View this help section.');
            return false;
        }

        if (in_array(strtolower($answer), ['y','yes'])) {
            foreach ($this->locales as $locale) {
                // With overwrite protection enabled, we ensure only new inserts will be handled
                $this->importer->dry($this->option('dry'))->enableOverwriteProtection()->import($locale);
            }
        }

        return true;
    }


    private function handleUpdatesOnHold()
    {
        // if no inserts in pipeline we skip the inserts early on
        if (($totalUpdatesOnHold = count($this->importer->getStats()->getUpdatesOnHold())) < 1) {
            $this->warn('No changed translations found');
            return true;
        }

        $answer = $this->ask(($this->option('dry') ? '(Simulation)' : null) . $totalUpdatesOnHold . ' translations differ from database. Process each one? (y,n,details,?)', 'n');

        if (!$answer || in_array(strtolower($answer), ['n','no'])) {
            return true;
        }

        if ($answer == 'details') {
        // Show details and continue
            $this->displayDetails('updates_on_hold');
            return false;
        }

        if ($answer == '?') {
        // Show details and continue
            $this->line('Your options on processing the changed translations:');
            $this->line('- y (yes)  Update the database translations with the new version from the language file.');
            $this->line('- n (no)   Cancel the update.');
            $this->line('- details  Preview the translations that have been changed.');
            $this->line('- ?        View this help section.');
            return false;
        }

        if (in_array(strtolower($answer), ['y','yes'])) {
            $i = 1;
            foreach ($this->importer->getStats()->getUpdatesOnHold() as $key => $entry) {
                $this->info($entry->key." [".strtoupper($entry->locale)."] has changed: (".$i++ ."/".$totalUpdatesOnHold.")");
                $this->comment("Original:");
                $this->line($entry->original_value);
                $this->comment("New:");
                $this->line($entry->value);

                $confirm = $this->ask('Overwrite? (y,n)', 'n');

                if (in_array(strtolower($confirm), ['y','yes'])) {
                    $this->importer->dry($this->option('dry'))->importOnHoldValue($entry->locale, $entry->key, $entry->value);
                }
            }
        }

        return true;
    }


    private function displayDetails($action)
    {
        $stats = $this->importer->getStats();

        $table = new Table($this->output);
        $table->setHeaders(['key', 'locale', 'original', 'new']);

        $rows = [];
        $currentpage = null;
        foreach ($stats->{'get'.ucfirst($action)}() as $entry) {
            $original_value = wordwrap($entry->original_value, 100);
            $value = wordwrap($entry->value, 100);

            // Separate each page in the table with a row separator
            if (!is_null($currentpage) && $currentpage !== $entry->pagekey) {
                $rows[] = new TableSeparator();
            }
            $currentpage = $entry->pagekey;

            $rows[] = [$entry->key,$entry->locale,$original_value,$value];
        }

        $table->setRows($rows);

        $table->render();
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
