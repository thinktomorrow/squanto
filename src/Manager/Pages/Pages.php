<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Manager\Pages;

final class Pages
{
    private array $pages;

    public function __construct(array $pages)
    {
        $this->validatePages($pages);

        $this->pages = $pages;
    }

    public static function fromFilenames(array $filenames): self
    {
        // Extract all files (excluding non-managed)
        return new static(
            array_map(
                function ($filename) {
                    return Page::fromFilename($filename); 
                }, $filenames
            )
        );
    }

    public function all(): array
    {
        return $this->pages;
    }

    private function validatePages(array $pages)
    {
        foreach($pages as $page){
            if(! $page instanceof Page) {
                throw new \InvalidArgumentException('A pages parameter should consist of Page instances.');
            }
        }
    }
}
