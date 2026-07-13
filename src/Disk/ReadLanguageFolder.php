<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use League\Flysystem\Filesystem;
use League\Flysystem\Local\LocalFilesystemAdapter;
use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;
use Thinktomorrow\Squanto\Sources\SquantoSource;

final class ReadLanguageFolder
{
    private ReadLanguageFile $readLanguageFile;

    /**
     * @var Filesystem
     */
    private SquantoRegistry $registry;

    public function __construct(ReadLanguageFile $readLanguageFile, SquantoRegistry $registry)
    {
        $this->readLanguageFile = $readLanguageFile;
        $this->registry = $registry;
    }

    public function read(string $locale, ?SquantoSource $source = null): Lines
    {
        $lines = new Lines([]);
        $source = $source ?: $this->registry->defaultSource();

        $files = $this->files($locale, $source);

        foreach ($files as $file) {
            $lines = $lines->merge($this->readLanguageFile->read($locale, $source->path().'/'.$file['path'], $source));
        }

        return $lines;
    }

    public function files(string $folder, ?SquantoSource $source = null): array
    {
        $source = $source ?: $this->registry->defaultSource();

        if (! is_dir($source->path().'/'.$folder)) {
            return [];
        }

        $files = $this->filesystem($source)->listContents($folder)->toArray();

        return $this->excludeFiles($files, $source);
    }

    private function excludeFiles(array $files, SquantoSource $source): array
    {
        if ($source->isNamespaced()) {
            return $files;
        }

        $excludedFilenames = config('squanto.excluded_files', []);

        foreach ($files as $k => $file) {
            $filename = str_replace('.php', '', basename($file['path']));

            if (in_array($filename, $excludedFilenames)) {
                unset($files[$k]);
            }
        }

        return $files;
    }

    private function filesystem(SquantoSource $source): Filesystem
    {
        return new Filesystem(new LocalFilesystemAdapter($source->path()));
    }
}
