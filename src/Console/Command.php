<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

use Laravel\Prompts\Prompt;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\note;
use function Laravel\Prompts\table;
use function Laravel\Prompts\warning;

abstract class Command extends \Illuminate\Console\Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $previousTheme = Prompt::theme();

        Prompt::theme('squanto');

        try {
            return parent::execute($input, $output);
        } finally {
            Prompt::theme($previousTheme);
        }
    }

    protected function displayTable(array $headers, array $rows)
    {
        if ($this->isTranslationImpactTable($headers)) {
            $this->displayTranslationImpact($rows);

            return;
        }

        table($headers, $rows);
    }

    private function isTranslationImpactTable(array $headers): bool
    {
        return in_array('translations', $headers, true);
    }

    protected function displaySummary(string $title, array $rows): void
    {
        note($title);
        table(['Metric', 'Value'], $this->expandSummaryRows($rows));
    }

    private function expandSummaryRows(array $rows): array
    {
        $expandedRows = [];

        foreach ($rows as $row) {
            if (! is_array($row[1])) {
                $expandedRows[] = $row;

                continue;
            }

            foreach ($row[1] as $index => $value) {
                $expandedRows[] = [$index === 0 ? $row[0] : '', $value];
            }
        }

        return $expandedRows;
    }

    protected function summarizeFiles(array $files): array
    {
        if (count($files) === 0) {
            return ['0'];
        }

        return array_merge([count($files).' matching files'], $files);
    }

    private function displayTranslationImpact(array $rows): void
    {
        if ($this->output->isVerbose()) {
            $this->displayKeysWithTranslations($rows, truncate: ! $this->output->isVeryVerbose());

            return;
        }

        $keys = array_map(fn (array $row) => $row[0], $rows);

        if (! $this->output->isVerbose()) {
            $keys = array_slice($keys, 0, 20);
        }

        foreach ($keys as $key) {
            $this->line('  - '.$key);
        }

        if (! $this->output->isVerbose() && count($rows) > count($keys)) {
            note('Showing '.count($keys).' of '.count($rows).' affected keys. Use -v to show all keys with translation teasers, or -vv for full translations.');
        } else {
            note('Use -v to include translation teasers, or -vv for full translations.');
        }
    }

    private function displayKeysWithTranslations(array $rows, bool $truncate): void
    {
        foreach ($rows as $row) {
            $this->newLine();
            $this->line('<fg=cyan;options=bold>'.$row[0].'</>');

            foreach (explode("\n", $row[1]) as $translation) {
                $translation = $truncate ? $this->truncateTranslation($translation) : $translation;

                foreach (explode("\n", wordwrap($translation, 120)) as $wrappedLine) {
                    $this->line('  '.$wrappedLine);
                }
            }
        }
    }

    private function truncateTranslation(string $translation): string
    {
        $translation = preg_replace('/\s+/', ' ', $translation) ?: $translation;

        if (mb_strlen($translation) <= 60) {
            return $translation;
        }

        return rtrim(mb_substr($translation, 0, 60)).'...';
    }

    protected function confirmChanges(string $summary): bool
    {
        if ($this->option('dry') || $this->option('yes')) {
            return true;
        }

        warning($summary);
        note('Use --dry to view the complete summary.');

        return confirm('Continue?', default: false);
    }

    protected function outputTranslations(array $values): string
    {
        $output = [];
        foreach ($values as $locale => $value) {
            $value = preg_replace('/\s+/', ' ', (string) $value) ?: $value;

            $output[] = $locale.': '.$value;
        }

        return implode("\n", $output);
    }
}
