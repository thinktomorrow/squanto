<?php

namespace Thinktomorrow\Squanto\Database;

use Thinktomorrow\Squanto\Domain\Line;
use Illuminate\Database\Eloquent\Model;
use Thinktomorrow\Squanto\Domain\LineKey;
use Illuminate\Database\Eloquent\SoftDeletes;
use Thinktomorrow\DynamicAttributes\HasDynamicAttributes;

/**
 * @property mixed values
 * @property string key
 */
class DatabaseLine extends Model
{
    use HasDynamicAttributes;
    use SoftDeletes;

    public $table = 'squanto_lines';
    public $guarded = [];
    public $dynamicKeys = ['value'];
    public $casts = [
        'metadata' => 'array',
    ];

    public function toLine(): Line
    {
        $values = (array) json_decode($this->values, true);

        return Line::fromRaw($this->key, $values['value'] ?? []);
    }

    public static function findByKey(LineKey $lineKey)
    {
        return self::where('key', $lineKey->get())->first();
    }

    public static function findSoftDeletedByKey(LineKey $lineKey)
    {
        return self::onlyTrashed()->where('key', $lineKey->get())->first();
    }

    protected function dynamicLocales(): array
    {
        return config('squanto.locales', []);
    }
}
