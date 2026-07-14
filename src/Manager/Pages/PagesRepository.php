<?php

declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

use League\Flysystem\FileAttributes;
use Thinktomorrow\Squanto\Disk\ReadLanguageFolder;
use Thinktomorrow\Squanto\Sources\SquantoRegistry;

final class PagesRepository
{
    private ReadLanguageFolder $readLanguageFolder;

    private SquantoRegistry $registry;

    public function __construct(ReadLanguageFolder $readLanguageFolder, SquantoRegistry $registry)
    {
        $this->readLanguageFolder = $readLanguageFolder;
        $this->registry = $registry;
    }

    public function all(): Pages
    {
        $pages = [];

        foreach ($this->registry->all() as $source) {
            foreach (config('squanto.locales') as $locale) {
                $files = $this->readLanguageFolder->files($locale, $source);

                /** @var FileAttributes $file */
                foreach ($files as $file) {
                    $filename = str_replace('.php', '', basename($file->path()));
                    $page = Page::fromSourceFile($source, $filename);

                    $pages[$page->slug()] = $page;
                }
            }
        }

        return new Pages(array_values($pages));
    }

    public function findBySlug(string $slug): Page
    {
        [$namespace, $group] = str_contains($slug, '::')
            ? explode('::', $slug, 2)
            : [null, $slug];

        foreach ($this->registry->all() as $source) {
            if ($source->namespace() === $namespace) {
                return Page::fromSourceFile($source, $group);
            }
        }

        return Page::fromFilename($slug);
    }
}
