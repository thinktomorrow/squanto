<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Import\Entry;
use Thinktomorrow\Squanto\Services\ImportStatistics;

class ImportStatisticsTest extends TestCase
{
    /** @test */
    public function it_has_by_default_no_items()
    {
        $stats = new ImportStatistics();

        $this->assertIsArray($stats->get());
        $this->assertCount(0,$stats->get());
    }

    /** @test */
    public function items_can_be_passed_as_parameter()
    {
        $stats = new ImportStatistics(['first','second']);

        $this->assertIsArray($stats->get());
        $this->assertCount(2,$stats->get());
    }

    /** @test */
    public function items_can_be_fetched_by_dynamic_method_matching_the_key()
    {
        $stats = new ImportStatistics(['first' => 'foobar','second' => 'turtlepower']);

        $this->assertEquals('foobar',$stats->getFirst());
        $this->assertEquals('turtlepower',$stats->getSecond());
    }

    /** @test */
    public function camelcased_dynamic_methods_correspond_to_snakecase()
    {
        $stats = new ImportStatistics(['things_to_do' => 'eat & sleep']);

        $this->assertEquals('eat & sleep',$stats->getThingsToDo());
    }

    /** @test */
    public function nonfound_keys_are_silently_ignored_and_return_empty_array()
    {
        $stats = new ImportStatistics();

        $this->assertEquals([],$stats->getFakeStuff());
    }

    /** @test */
    public function it_can_push_a_item_to_the_statistics()
    {
        $stats = new ImportStatistics();

        $stats->pushTranslation('inserts',new Entry('nl','foo.bar','example','original'));
        $inserts = $stats->getInserts();

        $this->assertEquals('nl.foo.bar',key($inserts));
        $this->assertInstanceOf(Entry::class,reset($inserts));
    }

    /** @test */
    public function it_can_remove_an_item_from_the_statistics()
    {
        $stats = new ImportStatistics();

        $stats->pushTranslation('inserts',new Entry('nl','foo.bar','example','original'));
        $stats->pushTranslation('inserts',new Entry('nl','foo.baz','example','original'));

        $stats->removeTranslation('inserts','nl','foo.baz');

        $this->assertCount(1,$stats->getInserts());
    }
}