<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Domain\Lines;

final class ReadLanguageFolder
{
    /**
     * @var ReadLanguageFile
     */
    private ReadLanguageFile $readLanguageFile;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(ReadLanguageFile $readLanguageFile, Filesystem $filesystem)
    {
        $this->readLanguageFile = $readLanguageFile;
        $this->filesystem = $filesystem;
    }

    public function read(string $locale): Lines
    {
        $lines = new Lines([]);

        $files = $this->files($locale);


        foreach ($files as $file) {
            $lines = $lines->merge($this->readLanguageFile->read($locale, Paths::getSquantoLangPath() .'/'. $file['path']));
        }

        return $lines;
    }

    public function files(string $folder): array
    {
        $files = $this->filesystem->listContents($folder)->toArray();

        return $this->excludeFiles($files);
    }

    private function excludeFiles(array $files): array
    {
        $excludedFilenames = config('squanto.excluded_files', []);

        foreach ($files as $k => $file) {
            $filename = str_replace('.php', '', basename($file['path']));

            if (in_array($filename, $excludedFilenames)) {
                unset($files[$k]);
            }
        }

        return $files;
    }
}
