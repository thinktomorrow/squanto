<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk\Query;

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
            $filepath = $this->filesystem->getAdapter()->getPathPrefix() . $file['path'];

            $lines = $lines->merge($this->readLanguageFile->read($locale, $filepath));
        }

        return $lines;
    }

    public function files(string $folder): array
    {
        $files = $this->filesystem->listContents($folder);

        return $this->excludeFiles($files);
    }

    private function excludeFiles(array $files): array
    {
        $excludedFilenames = config('thinktomorrow.squanto.excluded_files', []);

        foreach($files as $k => $file) {
            if(in_array($file['filename'], $excludedFilenames)) {
                unset($files[$k]);
            }
        }

        return $files;
    }

}
