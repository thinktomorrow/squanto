<?php

namespace Thinktomorrow\Squanto\Tests\Application\Scanner;

use Thinktomorrow\Squanto\Application\Scanner\LineKeyOccurrence;
use Thinktomorrow\Squanto\Domain\DatabaseLine;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Tests\TestCase;

class LineKeyOccurrenceTest extends TestCase
{
    /** @test */
    public function it_can_find_an_occurrence_of_line_key()
    {
        $path = __DIR__.'/dummy-views/stub-1.php';

        $occurrences = LineKeyOccurrence::findAllIn(LineKey::fromString('trans.first'), [$path]);
dd($occurrences);
    }


}
