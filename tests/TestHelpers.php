<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Application\Commands\AddDatabaseLine;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;

trait TestHelpers{

    public $databaseLine;

    protected function addDatabaseLine(string $key, array $values)
    {
        $lineKey = LineKey::fromString($key);
        app(AddDatabaseLine::class)->handle($lineKey, $values);

        $this->databaseLine = DatabaseLine::findByKey($lineKey);
    }

    private function recurse_copy($src, $dst) { 
        $dir = opendir($src); 
        @mkdir($dst); 
        while(false !== ( $file = readdir($dir)) ) { 
            if (( $file != '.' ) && ( $file != '..' )) { 
                if ( is_dir($src . '/' . $file) ) { 
                    $this->recurse_copy($src . '/' . $file,$dst . '/' . $file); 
                } 
                else { 
                    copy($src . '/' . $file,$dst . '/' . $file); 
                } 
            } 
        } 
        closedir($dir); 
    } 
}
