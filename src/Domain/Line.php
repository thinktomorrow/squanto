<?php

namespace Thinktomorrow\Squanto\Domain;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable as BaseTranslatable;

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
}
