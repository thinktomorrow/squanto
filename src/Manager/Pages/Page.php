<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use Illuminate\Support\Str;

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
        return new static($filename, Str::slug($filename));
    }

    public function label(): string
    {
        return $this->label;
    }

    public function slug(): string
    {
        return $this->slug;
    }
}
