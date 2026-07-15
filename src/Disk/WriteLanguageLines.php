<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Services\ConvertToTree;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;

final class WriteLanguageLines
{
    public function __construct(private SquantoRegistry $registry) {}

    public function handle(Lines $lines): array
    {
        $written = [];

        $lines->each(function (Line $line) use (&$written) {
            foreach ($line->values() as $locale => $value) {
                if ($value === null) {
                    continue;
                }

                $filepath = $this->writeValue($line->keyAsString(), $locale, $value);
                $written[$filepath][] = $line->keyAsString();
            }
        });

        return $written;
    }

    private function writeValue(string $key, string $locale, string $value): string
    {
        [$namespace, $lineKey] = $this->splitNamespace($key);
        $group = Str::before($lineKey, '.');
        $nestedKey = Str::after($lineKey, '.');
        $source = $namespace === null
            ? $this->registry->defaultSource()
            : $this->registry->findByNamespace($namespace);

        if (! $source) {
            throw new \InvalidArgumentException('No Squanto source registered for namespace ['.$namespace.'].');
        }
        $filepath = $source->path().'/'.$locale.'/'.$group.'.php';

        if (! is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $translations = [];
        if (is_file($filepath)) {
            $translations = include $filepath;
        }

        Arr::set($translations, $nestedKey, $value);

        file_put_contents($filepath, "<?php\n\n return ".var_export(ConvertToTree::fromFlattened(Arr::dot($translations)), true).";\n");

        return $filepath;
    }

    private function splitNamespace(string $key): array
    {
        if (! str_contains($key, '::')) {
            return [null, $key];
        }

        return explode('::', $key, 2);
    }
}
