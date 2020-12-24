<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Domain\Metadata\MetadataCollection;

final class ReadMetadataFolder
{
    /**
     * @var ReadMetadataFile
     */
    private ReadMetadataFile $readMetadataFile;

    /**
     * @var Filesystem
     */
    private Filesystem $filesystem;

    public function __construct(ReadMetadataFile $readMetadataFile, Filesystem $filesystem)
    {
        $this->readMetadataFile = $readMetadataFile;
        $this->filesystem = $filesystem;
    }

    public function read(): MetadataCollection
    {
        $collection = new MetadataCollection([]);
        $files = $this->filesystem->listContents();

        foreach ($files as $file) {

            $filepath = $this->filesystem->getAdapter()->getPathPrefix() . $file['path']; // preprend with basepath: $this->path . DIRECTORY_SEPARATOR . $file['path']

            $collection = $collection->merge($this->readMetadataFile->read($filepath));
        }

        return $collection;
    }

}
