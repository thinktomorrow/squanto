<?php

namespace Thinktomorrow\Squanto\Tests;

use Thinktomorrow\Squanto\Application\Rename\RenameKey;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\Page;

class RenameTest extends TestCase
{
    private $changeKey;

    public function setUp()
    {
        parent::setUp();
        $this->changeKey = app(RenameKey::class);
    }

    /** @test */
    public function it_can_change_a_key_and_place_under_proper_page()
    {
        $line = Line::make('foz.bar');
        $page = Page::make('fer');

        $this->changeKey->handle($line, 'fer.ber');

        $this->assertNull(Line::findByKey('foz.bar'));
        $this->assertEquals($line->id, Line::findByKey('fer.ber')->id);
        $this->assertEquals($page->id, Line::findByKey('fer.ber')->page_id);
    }

    /** @test */
    public function it_can_change_a_key_and_creates_new_page()
    {
        $line = Line::make('foz.bar');

        $this->changeKey->handle($line, 'fez.ber');

        $this->assertNull(Line::findByKey('foz.bar'));
        $this->assertEquals($line->id, Line::findByKey('fez.ber')->id);
        $this->assertEquals(Page::findByKey('fez')->id, Line::findByKey('fez.ber')->page_id);
    }

    /** @test */
    public function empty_page_will_be_removed()
    {
        $line = Line::make('foz.bar');
        $this->changeKey->handle($line, 'fer.ber');

        $this->assertNull(Page::findByKey('foz'));
    }

}