<?php

namespace Thinktomorrow\Squanto\Tests\Application\Scanner;

use Thinktomorrow\Squanto\Application\Scanner\FindLineKeys;
use Thinktomorrow\Squanto\Tests\TestCase;

class FindLineKeysTest extends TestCase
{
    /** @test */
    public function it_can_find_all_line_keys_in_a_file()
    {
        $path = __DIR__.'/dummy-views/stub-1.php';

        $findings = (new FindLineKeys($path))->handle();

        $this->assertEquals([
            __DIR__.'/dummy-views/stub-1.php' => [
                ['linekey' => 'trans.first', 'pos' => mb_strpos(file_get_contents(__DIR__.'/dummy-views/stub-1.php'), 'trans.first')],
                ['linekey' => 'trans.second.title', 'pos' => mb_strpos(file_get_contents(__DIR__.'/dummy-views/stub-1.php'), 'trans.second.title')],
            ]
        ], $findings);
    }


}
