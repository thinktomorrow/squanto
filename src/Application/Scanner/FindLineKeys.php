<?php

namespace Thinktomorrow\Squanto\Application\Scanner;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FindLineKeys
{
    /** @var string */
    private $path;

    /** @var array */
    private $linekeys;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function get()
    {
        $iterator = $this->iterator();
        $linePattern = $this->pattern();
        $lineKeys = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach($iterator as $file)
        {
            $lineKeysForPath = [];

            if ( preg_match_all( "/$linePattern/siU", $file->getContents(), $matches, PREG_OFFSET_CAPTURE ) ) {
                foreach ( $matches[2] as $info ) {
                    $lineKeysForPath[] = ['linekey' => $info[0], 'pos' => $info[1]];
                }
            }

            if(count($lineKeysForPath) > 0){
                $lineKeys[$file->getRelativePath()] = $lineKeysForPath;
            }
        }

        return $lineKeys;
    }

    public function filter($linekeys)
    {
        if(!is_array($linekeys)) $linekeys = (array) $linekeys;

        $this->linekeys = $linekeys;

        return $this;
    }

    private function iterator(): \IteratorAggregate
    {
        if(is_file($this->path) && file_exists($this->path)){
            $finder = Finder::create()->append([ new SplFileInfo($this->path, $this->path, $this->path)]);
        } else {
            $finder = Finder::create()->files()
                ->ignoreDotFiles()
                ->in($this->path)
                ->exclude( 'storage' )
                ->exclude( 'vendor' )
                ->name( '*.php' )
                ->name( '*.vue' );
        }

        return $finder;
    }

    private function pattern()
    {
        $transFunctions = ['trans', 'trans_choice', 'Lang::get',
                           'Lang::choice', 'Lang::trans', 'Lang::transChoice',
                           '@lang', '@choice', '__', '$trans.get' ];

        $linekeys = $this->linekeys
            ? '('.implode('|', $this->linekeys).')' // specific set of linekeys
            :  '('.'[a-zA-Z0-9_-]+'."([.][^\1)$]+)+".')'; // All possible linekeys

        return
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]'. // Must not start with any alphanum or _
            '('.implode('|', $transFunctions).')'.// Must start with one of the functions
            "\(".// Match opening parentheses
            "[\'\"]".// Match " or '
            $linekeys.
            "[\'\"]".// Closing quote
            "[\),]"  // Close parentheses or new parameter
        ;
    }
}