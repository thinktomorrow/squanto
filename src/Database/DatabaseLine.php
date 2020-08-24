<?php

namespace Thinktomorrow\Squanto\Database;

use Thinktomorrow\Squanto\Domain\Line;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Thinktomorrow\Squanto\Domain\LineKey;
use Thinktomorrow\Squanto\Domain\Metadata\FieldType;
use Illuminate\Database\Eloquent\SoftDeletes;
use Thinktomorrow\DynamicAttributes\HasDynamicAttributes;

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

    protected function dynamicLocales(): array
    {
        trap(config('thinktomorrow.squanto.locales', []));
        return config('thinktomorrow.squanto.locales', []);
    }

    /**
     * Save a translated value
     *
     * @param $locale
     * @param $value
     * @return $this
     */
//    public function saveValue($locale, $value)
//    {
//        $this->setDynamic('value', $value, $locale);
//        $this->save();
//
//        return $this;
//    }

    /**
     * @param null $locale
     * @param bool $fallback
     * @return string
     */
//    public function getValue($locale = null, $fallback = true)
//    {
//        $result = $this->dynamic('value', $locale);
//
//        if(null === $result && $fallback) {
//            $result = $this->dynamic('value', config('app.fallback_locale'));
//        }
//
//        return $result;
//    }
}
