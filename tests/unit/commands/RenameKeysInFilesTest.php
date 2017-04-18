<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Application\Rename\RenameKeysInFiles;

class RenameKeysInFilesTest extends TestCase
{
    private $renameKeysInFiles;

    public function setUp()
    {
        parent::setUp();
        $this->renameKeysInFiles = app(RenameKeysInFiles::class);
    }
    
    /** @test */
    function it_can_rename_keys_in_viewfiles()
    {
        $this->renameKeysInFiles->handle('foo.bar','foo.baz');
    }

}