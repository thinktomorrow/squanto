<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use Illuminate\Support\Str;
use Thinktomorrow\Squanto\Database\DatabaseLine;

final class LineViewModel
{
    const FIELDTYPE_TEXT = 'text';
    const FIELDTYPE_TEXTAREA = 'textarea';
    const FIELDTYPE_EDITOR = 'editor';

    /**
     * @var DatabaseLine
     */
    private DatabaseLine $line;

    public function __construct(DatabaseLine $line)
    {
        $this->line = $line;
    }

    public function value(string $locale)
    {
        return $this->line->dynamic('value', $locale);
    }

    public function key(): string
    {
        return $this->line->key;
    }

    public function id(): string
    {
        return Str::slug($this->line->key);
    }

    public function label(): string
    {
        if ($this->line->metadata['label']) {
            return $this->line->metadata['label'];
        }

        $parts = explode('.', $this->line->key);

        if (count($parts) <= 2) {
            return last($parts);
        }

        array_splice($parts, 0, 2);

        return implode(' ', $parts);
    }

    public function description(): ?string
    {
        return $this->line->metadata['description'] ?? null;
    }

    public function sectionKey(): string
    {
        $parts = explode('.', $this->line->key);

        if (count($parts) <= 2) {
            return last($parts);
        }

        return $parts[1];
    }

    public function isFieldTypeEditor(): bool
    {
        return $this->fieldType() == self::FIELDTYPE_EDITOR;
    }

    public function isFieldTypeTextarea(): bool
    {
        return $this->fieldType() == self::FIELDTYPE_TEXTAREA;
    }

    private function fieldType(): string
    {
        return $this->line->metadata['fieldtype'] ?? self::FIELDTYPE_TEXT;
    }
}
