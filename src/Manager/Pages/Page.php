<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Sources\SquantoSource;

final class Page
{
    private string $label;

    private string $slug;

    private ?string $namespace;

    private string $group;

    private string $sourceLabel;

    public function __construct(string $label, string $slug, ?string $namespace = null, ?string $group = null, ?string $sourceLabel = null)
    {
        $this->label = $label;
        $this->slug = $slug;
        $this->namespace = $namespace;
        $this->group = $group ?: $slug;
        $this->sourceLabel = $sourceLabel ?: 'App';
    }

    public static function fromFilename(string $filename): self
    {
        $label = str_replace(['-', '_'], ' ', $filename);

        return new self($label, $filename);
    }

    public static function fromSourceFile(SquantoSource $source, string $filename): self
    {
        $label = str_replace(['-', '_'], ' ', $filename);

        return new self(
            $label,
            $source->groupKey($filename),
            $source->namespace(),
            $filename,
            $source->label()
        );
    }

    public function label(): string
    {
        return $this->label;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function namespace(): ?string
    {
        return $this->namespace;
    }

    public function group(): string
    {
        return $this->group;
    }

    public function sourceLabel(): string
    {
        return $this->sourceLabel;
    }

    public function pageKey(): string
    {
        return $this->slug;
    }

    public function completionPercentage(): float
    {
        $lines = app(DatabaseLinesRepository::class)->allForPage($this->pageKey());
        $locales = config('squanto.locales');

        return app(Completion::class)->calculate($lines, $locales);
    }
}
