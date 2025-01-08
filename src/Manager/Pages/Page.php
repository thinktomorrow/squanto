<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;

final class Page
{
    private string $label;
    private string $slug;

    public function __construct(string $label, string $slug)
    {
        $this->label = $label;
        $this->slug = $slug;
    }

    public static function fromFilename(string $filename): self
    {
        $label = str_replace(['-','_'], ' ', $filename);

        return new static($label, $filename);
    }

    public function label(): string
    {
        return $this->label;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function completionPercentage(): float
    {
        $lines = app(DatabaseLinesRepository::class)->allStartingWith($this->slug);
        $locales = config('squanto.locales');

        return app(Completion::class)->calculate($lines, $locales);
    }
}
