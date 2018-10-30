<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Domain\Line;

class TranslationControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_can_store_a_translation()
    {
        $this->markTestIncomplete();
        
        $line = Line::make('foo.bar');
        $line->saveValue('nl','bazz');

        $response = $this->actingAs()->put('admin.squanto.update', [
            'trans' => [
                'nl' => [
                    $line->id => 'bazz & foo'
                ]
            ]
        ]);

        dd($response);

        $this->assertEquals('bazz & foo', Line::findByKey('foo.bar')->value);           
    }

}
