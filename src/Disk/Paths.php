<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Disk;

class Paths
{
    public static function getSquantoCachePath(): string
    {
        $path = config('squanto.cache_path');

        return is_null($path) ? storage_path('app/trans') : $path;
    }

    public static function getSquantoLangPath(): string
    {
        $path = config('squanto.lang_path');

        return is_null($path) ? app('path.lang') : $path;
    }

    public static function getSquantoMetadataPath(): string
    {
        return config('squanto.metadata_path', resource_path('squanto_metadata'));
    }
}
