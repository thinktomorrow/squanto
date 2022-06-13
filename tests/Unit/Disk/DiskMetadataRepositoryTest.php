<?php
declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Disk;

use Thinktomorrow\Squanto\Disk\ReadMetadataFolder;
use Thinktomorrow\Squanto\Domain\Metadata\MetadataCollection;
use Thinktomorrow\SquantoTests\TestCase;

final class DiskMetadataRepositoryTest extends TestCase
{
    /** @test */
    public function it_can_retrieve_all_metadata_files_within_a_folder()
    {
        $collection = app(ReadMetadataFolder::class)->read();
        $this->assertInstanceOf(MetadataCollection::class, $collection);

        $items = $this->getPrivateProperty($collection, 'items');
        $this->assertCount(2, $items);

        $this->assertEquals('titel label', $collection->find('about.title')->values()['label']);
    }
}
