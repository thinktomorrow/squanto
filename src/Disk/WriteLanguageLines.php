<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Services\ConvertToTree;
use Thinktomorrow\Squanto\Services\PhpArrayExporter;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;

final class WriteLanguageLines
{
    public function __construct(private SquantoRegistry $registry)
    {
    }

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

    public function pathsForLines(Lines $lines): array
    {
        $paths = [];

        $lines->each(function (Line $line) use (&$paths) {
            foreach ($line->values() as $locale => $value) {
                if ($value === null) {
                    continue;
                }

                $paths[] = $this->filepathForKey($line->keyAsString(), $locale);
            }
        });

        $paths = array_values(array_unique($paths));
        sort($paths);

        return $paths;
    }

    private function writeValue(string $key, string $locale, string $value): string
    {
        [$namespace, $lineKey] = $this->splitNamespace($key);
        $group = Str::before($lineKey, '.');
        $nestedKey = Str::after($lineKey, '.');
        $filepath = $this->filepathForKey($key, $locale);

        if (! is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0777, true);
        }

        $translations = [];
        if (is_file($filepath)) {
            $translations = include $filepath;
        }

        Arr::set($translations, $nestedKey, $value);

        file_put_contents($filepath, "<?php\n\n return ".PhpArrayExporter::export(ConvertToTree::fromFlattened(Arr::dot($translations))).";\n");

        return $filepath;
    }

    private function filepathForKey(string $key, string $locale): string
    {
        [$namespace, $lineKey] = $this->splitNamespace($key);
        $group = Str::before($lineKey, '.');
        $source = $namespace === null
            ? $this->registry->defaultSource()
            : $this->registry->findByNamespace($namespace);

        if (! $source) {
            throw new \InvalidArgumentException('No Squanto source registered for namespace ['.$namespace.'].');
        }

        return $source->path().'/'.$locale.'/'.$group.'.php';
    }

    private function splitNamespace(string $key): array
    {
        if (! str_contains($key, '::')) {
            return [null, $key];
        }

        return explode('::', $key, 2);
    }
}
