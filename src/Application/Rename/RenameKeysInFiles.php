<?php

namespace Thinktomorrow\Squanto\Application\Rename;

use League\Flysystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Thinktomorrow\Squanto\Services\LineUsage;

final class RenameKeysInFiles
{
    private $usage;

    function __construct(LineUsage $usage)
    {
        $this->usage = $usage;
    }

    public function handle($oldKey, $newKey)
    {
        foreach($this->usage->getByKey($oldKey) as $usage)
        {
            $this->renameKeysInFile($usage['path'], $usage['function'], $oldKey, $newKey);
        }
    }

    /**
     * @param $path
     * @param $function
     * @param $oldKey
     * @param $newKey
     */
    private function renameKeysInFile($path, $function, $oldKey, $newKey)
    {
        $replace = str_replace($oldKey,$newKey,$function);
        $function = preg_quote($function);
        $content = file_get_contents($path);

        $replacedContent = preg_replace("/$function/s", $replace, $content);

        file_put_contents($path,$replacedContent);
    }
}