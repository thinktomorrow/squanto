<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Services;

final class PhpArrayExporter
{
    public static function export(array $array, int $indent = 0): string
    {
        if ($array === []) {
            return '[]';
        }

        $childIndent = str_repeat('  ', $indent + 1);
        $indentation = str_repeat('  ', $indent);

        $lines = ['['];

        foreach ($array as $key => $value) {
            $lines[] = $childIndent.self::exportKey($key).' => '.self::exportValue($value, $indent + 1).',';
        }

        $lines[] = $indentation.']';

        return implode("\n", $lines);
    }

    private static function exportKey(int|string $key): string
    {
        return is_int($key) ? (string) $key : var_export($key, true);
    }

    private static function exportValue(mixed $value, int $indent): string
    {
        if (is_array($value)) {
            return self::export($value, $indent);
        }

        return var_export($value, true);
    }
}
