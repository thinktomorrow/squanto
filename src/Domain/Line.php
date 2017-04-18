<?php

namespace Thinktomorrow\Squanto\Domain;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable as BaseTranslatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Line
 *
 * @package Thinktomorrow\Squanto\Domain
 */
class Line extends Model
{
    use BaseTranslatable, Translatable;

    public $table = 'squanto_lines';
    public $translatedAttributes = ['value'];

    /**
     * @param $key
     * @return Line
     */
    public static function make($key)
    {
        $linekey = new LineKey($key);

        $line = new self;
        $line->key = $linekey->get();
        $line->label = $linekey->getAsLabel();
        $line->page_id = Page::findOrCreateByKey($linekey->getPageKey())->id;

        $line->save();

        return $line;
    }

    public function changeKey($key)
    {
        $linekey = new LineKey($key);

        $this->key = $linekey->get();
        $this->label = $linekey->getAsLabel();
        $this->page_id = Page::findOrCreateByKey($linekey->getPageKey())->id;

        $this->save();
    }

    /**
     * @param $key
     * @return Line
     */
    public static function findOrCreateByKey($key)
    {
        if ($line = self::findByKey($key)) {
            return $line;
        }

        return self::make($key);
    }

    /**
     * Save a translated value
     *
     * @param $locale
     * @param $value
     * @return $this
     */
    public function saveValue($locale, $value)
    {
        $this->saveTranslation($locale, 'value', $value);

        return $this;
    }

    /**
     * Save a translated value
     *
     * @param $locale
     * @return $this
     */
    public function removeValue($locale)
    {
        $this->removeTranslation($locale);

        return $this;
    }

    /**
     * @param null $locale
     * @param bool $fallback
     * @return string
     */
    public function getValue($locale = null, $fallback = true)
    {
        return $this->getTranslationFor('value', $locale, $fallback);
    }

    public static function findValue($key, $locale)
    {
        if (!$line = self::findByKey($key)) {
            return null;
        }

        return $line->getValue($locale, false);
    }

    /**
     * @param $key
     * @return mixed
     */
    public static function findByKey($key)
    {
        return self::where('key', $key)->first();
    }

    public function saveSuggestedType()
    {
        // Based on first value found we will suggest a type
        if (!$value = $this->getValue()) {
            return;
        }

        $this->type = (new LineType($value))->suggest();
        $this->save();
    }

    public function editInEditor()
    {
        return $this->type == LineType::EDITOR;
    }

    public function editInTextarea()
    {
        return $this->type == LineType::TEXTAREA;
    }

    public function editInTextinput()
    {
        return (!$this->type || $this->type == LineType::TEXT);
    }

    public function areParagraphsAllowed()
    {
        return (false !== strpos($this->allowed_html,'<p>'));
    }

    public function page()
    {
        return $this->belongsTo(Page::class,'page_id');
    }

    /**
     * Get key - value pairs for all lines per locale
     *
     * @param $locale
     * @return mixed
     */
    public static function getValuesByLocale($locale)
    {
        return self::getValuesByLocaleAndPage($locale);
    }

    public static function getValuesByLocaleAndPage($locale, $pagekey = null)
    {
        $locale = $locale?: app()->getLocale();

        // Since the dimsav translatable model trait injects its behaviour and overwrites our results
        // with the current locale, we will need to fetch results straight from the db instead.
        $lines = DB::table('squanto_lines')
            ->join('squanto_line_translations', 'squanto_lines.id', '=', 'squanto_line_translations.line_id')
            ->select(['squanto_lines.*','squanto_line_translations.locale','squanto_line_translations.value'])
            ->where('squanto_line_translations.locale', $locale);

        if($pagekey)
        {
            $lines = $lines
                ->join('squanto_pages', 'squanto_lines.page_id', '=', 'squanto_pages.id')
                ->where('squanto_pages.key', $pagekey);
        }

        $lines = $lines->get();

        // Assert we have a collection
        $lines = $lines instanceof Collection ? $lines : collect($lines);

        return $lines->pluck('value', 'key')->toArray();
    }
}
