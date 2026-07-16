<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console\Prompts;

use Laravel\Prompts\Output\BufferedConsoleOutput;
use Laravel\Prompts\Table;
use Laravel\Prompts\Themes\Default\Renderer;
use Symfony\Component\Console\Helper\Table as SymfonyTable;
use Symfony\Component\Console\Helper\TableStyle;

class SquantoTableRenderer extends Renderer
{
    /**
     * Render the table.
     */
    public function __invoke(Table $table): string
    {
        $tableStyle = (new TableStyle)
            ->setHorizontalBorderChars('─')
            ->setVerticalBorderChars('│', '│')
            ->setCellHeaderFormat($this->dim('<fg=default>%s</>'))
            ->setCellRowFormat('<fg=default>%s</>');

        if (empty($table->headers)) {
            $tableStyle->setCrossingChars('┼', '', '', '', '┤', '┘</>', '┴', '└', '├', '<fg=white>┌', '┬', '┐');
        } else {
            $tableStyle->setCrossingChars('┼', '<fg=white>┌', '┬', '┐', '┤', '┘</>', '┴', '└', '├');
        }

        $buffered = new BufferedConsoleOutput;

        (new SymfonyTable($buffered))
            ->setHeaders($table->headers)
            ->setRows($table->rows)
            ->setStyle($tableStyle)
            ->render();

        foreach (explode(PHP_EOL, trim($buffered->content(), PHP_EOL)) as $line) {
            $this->line(' '.$line);
        }

        return (string) $this;
    }
}
