<?php

namespace Thinktomorrow\Squanto\Database\Application;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Services\ConvertToTree;
use Thinktomorrow\Squanto\Database\DatabaseLinesRepository;

class CacheDatabaseLines
{
    /**
     * Local filesystem. Already contains the path to our translation files
     * e.g. storage/app/trans
     *
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var DatabaseLinesRepository
     */
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

        foreach (config('thinktomorrow.squanto.locales', []) as $locale) {
            $this->writeFile(
                $locale, array_filter(
                    $lines->values($locale), function ($value) {
                        return null !== $value;
                    }
                )
            );
        }
    }

    /**
     * Create new cached translation files based on database entries
     *
     * @param $locale
     * @param array $lines  - flat array of key-value pairs e.g. foo.bar => 'translation of foo'
     */
    private function writeFile($locale, array $lines = [])
    {
        $translations = ConvertToTree::fromFlattened($lines);

        foreach ($translations as $section => $trans) {
            $this->filesystem->put(
                $locale.'/'.$section.'.php',
                "<?php\n\n return ".var_export($trans, true).";\n"
            );
        }
    }

    private function deleteAllCacheFiles(): void
    {
        foreach ($this->filesystem->listContents() as $content) {
            if ($content['type'] == 'dir') {
                $this->filesystem->deleteDir($content['path']);
            } else {
                $this->filesystem->delete($content['path']);
            }
        }
    }
}
