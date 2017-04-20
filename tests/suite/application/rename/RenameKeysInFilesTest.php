<?php

namespace Thinktomorrow\Squanto\Tests;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Thinktomorrow\Squanto\Application\Rename\RenameKeysInFiles;
use Thinktomorrow\Squanto\Services\LineUsage;

class RenameKeysInFilesTest extends TestCase
{
    private $renameKeysInFiles;

    public function setUp()
    {
        parent::setUp();

        $this->setupFixtureFiles();
        $this->renameKeysInFiles = $this->getRenameKeysInFilesInstance();
    }
    
    /** @test */
    function it_can_rename_keys_in_viewfiles()
    {
        $this->renameKeysInFiles->handle('foo.bar','foo.baz');

        $this->assertDirectoryContains($this->getFixtureDirectory('renamefileschanged'),'(\'foo.bar\')',0);
        $this->assertDirectoryContains($this->getFixtureDirectory('renamefileschanged'),'foo.baz',3);
    }

    /** @test */
    function it_does_not_change_files_if_key_not_found()
    {
        $this->renameKeysInFiles->handle('non.existing','never.gonna.happen');

        $this->assertDirectoryContains($this->getFixtureDirectory('renamefileschanged'),'never.gonna.happen',0);
    }

    private function setupFixtureFiles()
    {
        $dir = opendir($this->getFixtureDirectory('renamefiles'));
        while (false !== ($file = readdir($dir))) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            copy(
                $this->getFixtureDirectory('renamefiles') . '/' . $file,
                $this->getFixtureDirectory('renamefileschanged') . '/' . $file
            );
        }
    }

    private function getRenameKeysInFilesInstance()
    {
        return new RenameKeysInFiles(
            new LineUsage(new Finder(),[$this->getFixtureDirectory('renamefileschanged')])
        );
    }

    private function assertDirectoryContains($directory, $string, $times = 1)
    {
        $count = 0;

        $dir = opendir($directory);
        $string = preg_quote($string);

        while (false !== ($file = readdir($dir))) {
            if ($file == '.' || $file == '..') {
                continue;
            }

            if(preg_match_all("/$string/",file_get_contents($directory.'/'.$file),$matches))
            {
                if(is_array($matches[0]))
                {
                    $count += count($matches[0]);
                }
            }
        }

        $this->assertEquals($times, $count);
    }

}