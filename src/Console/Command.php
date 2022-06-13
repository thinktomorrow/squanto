<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableSeparator;

abstract class Command extends \Illuminate\Console\Command
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function displayTable(array $headers, array $rows)
    {
        $table = new Table($this->output);

        $table->setHeaders($headers);

        $rowsWithSeparators = [];

        // last pagekey
        $pageKey = '';

        foreach ($rows as $row) {
            if ($pageKey && 0 !== strpos($row[0], $pageKey)) {
                $rowsWithSeparators[] = new TableSeparator();
            }

            $rowsWithSeparators[] = $row;

            $pageKey = substr($row[0], 0, strpos($row[0], '.'));
        }

        $table->setRows($rowsWithSeparators);

        // Allow the last column to expand multiple rows (because it contains the translations)
        $table->setColumnMaxWidth(count($headers) - 1, 120);

        $table->render();
    }
}
