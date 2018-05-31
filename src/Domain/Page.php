<?php

namespace Thinktomorrow\Squanto\Domain;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public $table = 'squanto_pages';

    public static function make($key)
    {
        // TODO: assert unique key
        // TODO: sanitize and validate key

        $page = new self;
        $page->key = $key; // TODO: assert unique slug
        $page->label = ucfirst($key);
        $page->save();

        return $page;
    }

    public static function findOrCreateByKey($key)
    {
        if ($page = self::findByKey($key)) {
            return $page;
        }

        return self::make($key);
    }

    public static function findByKey($key)
    {
        return self::where('key', $key)->first();
    }

    public function lines()
    {
        return $this->hasMany(Line::class, 'page_id');
    }

    public static function getAll()
    {
        return self::sequence()->get();
    }

    public function scopeSequence($query)
    {
        return $query->orderBy('sequence', 'ASC');
    }

    public function isCompleted()
    {
        return Completion::check($this);
    }
    public function completionPercentage($locale)
    {
        return Completion::asPercentage($this, $locale);
    }
}
