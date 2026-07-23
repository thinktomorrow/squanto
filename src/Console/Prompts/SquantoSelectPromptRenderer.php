<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console\Prompts;

use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\Themes\Default\SelectPromptRenderer;

class SquantoSelectPromptRenderer extends SelectPromptRenderer
{
    public function __invoke(SelectPrompt $prompt): string
    {
        $maxWidth = $prompt->terminal()->cols() - 6;

        return (string) match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->truncate($prompt->label(), $maxWidth),
                    color: 'cyan',
                ),

            'cancel' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'red',
                )
                ->error($prompt->cancelMessage),

            'error' => $this
                ->box(
                    $this->truncate($prompt->label, $prompt->terminal()->cols() - 6),
                    $this->renderOptions($prompt),
                    color: 'yellow',
                )
                ->warning($this->truncate($prompt->error, $prompt->terminal()->cols() - 5)),

            default => $this
                ->box(
                    $this->cyan($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderOptions($prompt),
                    footer: $this->renderInfo($prompt),
                    color: 'cyan',
                )
                ->when(
                    $prompt->hint,
                    fn () => $this->hint($prompt->hint),
                    fn () => $this->newLine()
                ),
        };
    }

    private function renderInfo(SelectPrompt $prompt): string
    {
        if (! $prompt->infoText()) {
            return '';
        }

        return implode(PHP_EOL, array_map(
            fn (string $line) => $this->dim($line),
            $this->ansiWordwrap($prompt->infoText(), max(20, $prompt->terminal()->cols() - 10))
        ));
    }
}
