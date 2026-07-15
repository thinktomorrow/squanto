<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

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

    protected function displayTable(array $headers, array $rows)
    {
        table($headers, $rows);
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
            $output[] = $locale.': '.$value;
        }

        return implode("\n", $output);
    }
}
