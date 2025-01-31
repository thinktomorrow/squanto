<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use League\Flysystem\FileAttributes;
use Thinktomorrow\Squanto\Disk\ReadLanguageFolder;

final class PagesRepository
{
    /**
     * @var ReadLanguageFolder
     */
    private ReadLanguageFolder $readLanguageFolder;

    public function __construct(ReadLanguageFolder $readLanguageFolder)
    {
        $this->readLanguageFolder = $readLanguageFolder;
    }

    public function all(): Pages
    {
        $filenames = [];

        foreach (config('squanto.locales') as $locale) {
            $files = $this->readLanguageFolder->files($locale);

            /** @var FileAttributes $file */
            foreach ($files as $file) {
                $filenames[] = str_replace('.php', '', basename($file->path()));
            }
        }

        return Pages::fromFilenames(array_unique($filenames));
    }

    public function findBySlug(string $slug): Page
    {
        return Page::fromFilename($slug);
    }
}
