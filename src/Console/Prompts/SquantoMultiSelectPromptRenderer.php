<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Console\Prompts;

use Laravel\Prompts\MultiSelectPrompt;
use Laravel\Prompts\Themes\Default\MultiSelectPromptRenderer;

class SquantoMultiSelectPromptRenderer extends MultiSelectPromptRenderer
{
    public function __invoke(MultiSelectPrompt $prompt): string
    {
        return (string) match ($prompt->state) {
            'submit' => $this
                ->box(
                    $this->dim($this->truncate($prompt->label, $prompt->terminal()->cols() - 6)),
                    $this->renderSelectedOptions($prompt),
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
                    info: count($prompt->options) > $prompt->scroll ? (count($prompt->value()).' selected') : '',
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

    private function renderInfo(MultiSelectPrompt $prompt): string
    {
        $parts = array_filter([
            $prompt->infoText(),
            count($prompt->options) > $prompt->scroll ? (count($prompt->value()).' selected') : '',
        ]);

        if ($parts === []) {
            return '';
        }

        return implode(PHP_EOL, array_map(
            fn (string $line) => $this->dim($line),
            $this->ansiWordwrap(implode(' · ', $parts), max(20, $prompt->terminal()->cols() - 10))
        ));
    }
}
