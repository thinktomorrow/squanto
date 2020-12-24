<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use Thinktomorrow\Squanto\Domain\Lines;
use Thinktomorrow\Squanto\Domain\Metadata\MetadataCollection;

final class DiskMetadataRepository
{
    /**
     * @var ReadMetadataFromLines
     */
    private ReadMetadataFromLines $readMetadataFromLines;

    /**
     * @var ReadMetadataFolder
     */
    private ReadMetadataFolder $readMetadataFolder;

    public function __construct(ReadMetadataFromLines $readMetadataFromLines, ReadMetadataFolder $readMetadataFolder)
    {
        $this->readMetadataFromLines = $readMetadataFromLines;
        $this->readMetadataFolder = $readMetadataFolder;
    }

    public function all(Lines $lines): MetadataCollection
    {
        // read metadata from lang files
        $collection = $this->readMetadataFromLines->read($lines);

        // read metadata from metadata file
        if(($metadataFolderpath = config('squanto.metadata_path')) && file_exists($metadataFolderpath)) {
            $collection = $collection->merge($this->readMetadataFolder->read($metadataFolderpath));
        }

        return $collection;
    }
}
