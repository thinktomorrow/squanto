<?php

namespace Thinktomorrow\Squanto\Database\Application;

use Illuminate\Support\Str;
use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;
use Thinktomorrow\Squanto\Services\ConvertToTree;
use Thinktomorrow\Squanto\Services\PhpArrayExporter;

class CacheDatabaseLines
{
    /**
     * Local filesystem. Already contains the path to our translation files
     * e.g. storage/app/trans
     *
     * @var Filesystem
     */
    private $filesystem;

    private DatabaseLinesRepository $databaseLinesRepository;

    public function __construct(DatabaseLinesRepository $databaseLinesRepository, Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
        $this->databaseLinesRepository = $databaseLinesRepository;
    }

    /**
     * Delete existing cache en recache all database translations
     */
    public function handle(): void
    {
        $this->deleteAllCacheFiles();

        $lines = $this->databaseLinesRepository->all();

        foreach (config('squanto.locales', []) as $locale) {
            $this->writeFile(
                $locale,
                array_filter(
                    $lines->values($locale),
                    function ($value) {
                        return $value !== null;
                    }
                )
            );
        }
    }

    /**
     * Create new cached translation files based on database entries
     *
     * @param  array  $lines  - flat array of key-value pairs e.g. foo.bar => 'translation of foo'
     */
    private function writeFile(string $locale, array $lines = [])
    {
        $translations = [];

        foreach ($lines as $key => $value) {
            [$namespace, $lineKey] = $this->splitNamespace($key);
            $group = Str::before($lineKey, '.');
            $nestedKey = Str::after($lineKey, '.');
            $path = $namespace ? 'vendor/'.$namespace.'/'.$locale : $locale;

            $translations[$path][$group][$nestedKey] = $value;
        }

        foreach ($translations as $path => $groups) {
            foreach ($groups as $section => $trans) {
                $this->filesystem->createDirectory($path);
                $this->filesystem->write(
                    $path.'/'.$section.'.php',
                    "<?php\n\n return ".PhpArrayExporter::export(ConvertToTree::fromFlattened($trans)).";\n"
                );
            }
        }
    }

    private function splitNamespace(string $key): array
    {
        if (! Str::contains($key, '::')) {
            return [null, $key];
        }

        return explode('::', $key, 2);
    }

    private function deleteAllCacheFiles(): void
    {
        foreach ($this->filesystem->listContents('/') as $content) {
            if ($content['type'] == 'dir') {
                $this->filesystem->deleteDirectory($content['path']);
            } else {
                $this->filesystem->delete($content['path']);
            }
        }
    }
}
