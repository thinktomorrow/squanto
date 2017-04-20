<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Services\LineUsage;

class LineUsageTest extends TestCase
{
    private $lineUsage;

    public function setUp()
    {
        parent::setUp();

        $this->lineUsage = app(LineUsage::class);
    }

    /** @test */
    public function it_can_check_usage_of_translation_key()
    {
        $usages = $this->lineUsage->getByKey('foo.bar');

        $this->assertCount(3,$usages);
    }
}