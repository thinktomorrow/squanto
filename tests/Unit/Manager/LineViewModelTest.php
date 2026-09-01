<?php

declare(strict_types=1);

namespace Thinktomorrow\SquantoTests\Unit\Manager;

use PHPUnit\Framework\TestCase;
use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Manager\Pages\LineViewModel;

final class LineViewModelTest extends TestCase
{
    public function test_it_exposes_the_section_label_from_metadata(): void
    {
        $lineViewModel = new LineViewModel(new DatabaseLine([
            'key' => 'mails.registration-rejected.subject',
            'metadata' => ['section_label' => 'E-mail afwijzing inschrijving'],
        ]));

        $this->assertSame('E-mail afwijzing inschrijving', $lineViewModel->sectionLabel());
    }

    public function test_section_label_is_optional(): void
    {
        $lineViewModel = new LineViewModel(new DatabaseLine([
            'key' => 'mails.registration-rejected.subject',
        ]));

        $this->assertNull($lineViewModel->sectionLabel());
    }
}
