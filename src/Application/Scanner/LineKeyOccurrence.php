<?php

namespace Thinktomorrow\Squanto\Application\Scanner;

use Thinktomorrow\Squanto\Domain\LineKey;

class LineKeyOccurrence
{
    /** @var LineKey */
    private $lineKey;

    /** @var string */
    private $filepath;

    /** @var string */
    private $line;

    /** @var string */
    private $excerpt;

    public function __construct(LineKey $lineKey, string $filepath, string $line, string $excerpt)
    {
        $this->lineKey = $lineKey;
        $this->filepath = $filepath;
        $this->line = $line;
        $this->excerpt = $excerpt;
    }

    public static function findAllIn(LineKey $lineKey, array $paths)
    {
        $occurrences = [];
        foreach($paths as $path){
            $occurrences[] = (new FindLineKeys($path))
                                ->only($lineKey->get())
                                ->get();
        }



        dd($occurrences);

        // Scan filepaths ...

        // paths could be folder or file, ...

        // Path -> file_get_contents -> preg_match


    }




}