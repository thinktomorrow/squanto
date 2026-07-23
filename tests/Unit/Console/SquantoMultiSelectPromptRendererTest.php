<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Console;

use Laravel\Prompts\MultiSelectPrompt;
use Thinktomorrow\Squanto\Console\Prompts\SquantoMultiSelectPromptRenderer;
use Thinktomorrow\SquantoTests\TestCase;

class SquantoMultiSelectPromptRendererTest extends TestCase
{
    public function test_it_renders_the_checklist_with_the_default_selection(): void
    {
        $prompt = new MultiSelectPrompt(
            label: 'Which targets?',
            options: [
                'all' => 'All translations',
                'app' => 'Application translations',
                'seo' => 'seo',
            ],
            default: ['all'],
            hint: 'Use space to toggle targets.',
        );

        $output = (new SquantoMultiSelectPromptRenderer($prompt))($prompt);

        $this->assertStringContainsString('Which targets?', $output);
        $this->assertStringContainsString('All translations', $output);
        $this->assertStringContainsString('Application translations', $output);
        $this->assertStringContainsString('seo', $output);
    }
}
