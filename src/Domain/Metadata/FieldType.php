<?php

namespace Thinktomorrow\Squanto\Domain\Metadata;

use Thinktomorrow\Squanto\Domain\Exceptions\SquantoException;

final class FieldType
{
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const EDITOR = 'editor';

    private string $type;

    public function __construct($type)
    {
        $this->validate($type);

        $this->type = $type;
    }

    public function ofType(string $type): bool
    {
        return $this->type === $type;
    }

    public function get(): string
    {
        return $this->type;
    }

    private function validate($type)
    {
        if(!in_array($type, [
            self::TEXT,
            self::TEXTAREA,
            self::EDITOR
        ])) {
            throw new SquantoException('Invalid fieldtype ['.$type.']');
        }
    }

    public static function guess(string $value): self
    {
        if (strip_tags($value) != $value) {
            return new static(self::EDITOR);
        }

        if (strlen($value) < 50) {
            return new static(self::TEXT);
        }

        return new static(self::TEXTAREA);
    }
}
