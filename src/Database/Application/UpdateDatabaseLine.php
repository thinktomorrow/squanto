<?php
declare(strict_types=1);

namespace Thinktomorrow\Squanto\Database\Application;

use Thinktomorrow\Squanto\Database\DatabaseLine;
use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Domain\LineKey;

final class UpdateDatabaseLine
{
    public function handle(Line $line): void
    {
        DatabaseLine::findByKey(LineKey::fromString($line->keyAsString()))->update(
            [
            'values' => ['value' => array_filter(
                $line->values(),
                function ($value) {
                    return null !== $value;
                }
            )],
            ]
        );
    }
}
