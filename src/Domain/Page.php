<?php

namespace Thinktomorrow\Squanto\Domain;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    public $table = 'squanto_pages';

    public static function createFromKey(PageKey $pageKey)
    {
        // TODO: assert unique key
        // TODO: sanitize and validate key

        $page = new static;
        $page->key = $pageKey->get(); // TODO: assert unique slug
        $page->label = $pageKey->getAsLabel();
        $page->save();

        return $page;
    }

    public static function findOrCreateByKey(PageKey $pageKey)
    {
        if ($page = static::findByKey($pageKey)) {
            return $page;
        }

        return static::createFromKey($pageKey);
    }

    public static function findByKey(PageKey $pageKey)
    {
        return static::where('key', $pageKey->get())->first();
    }

    public function lines()
    {
        return $this->hasMany(DatabaseLine::class, 'page_id')->orderBy('sequence', 'ASC');
    }

    public static function getAll()
    {
        return static::sequence()->get();
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
