<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Console;

use Laravel\Prompts\SelectPrompt;
use Thinktomorrow\Squanto\Console\Prompts\SquantoSelectPromptRenderer;
use Thinktomorrow\SquantoTests\TestCase;

class SquantoSelectPromptRendererTest extends TestCase
{
    public function test_it_renders_the_selected_option_description_without_truncating_it(): void
    {
        $description = 'Remove database records for a target so files become source of truth.';

        $prompt = new SelectPrompt(
            label: 'What would you like to do?',
            options: [
                'reset' => 'Reset database translations',
            ],
            default: 'reset',
            info: fn () => $description,
        );

        $output = (new SquantoSelectPromptRenderer($prompt))($prompt);

        $this->assertStringContainsString('Reset database translations', $output);
        $this->assertStringContainsString($description, $output);
        $this->assertStringNotContainsString('source of truth…', $output);
    }
}
