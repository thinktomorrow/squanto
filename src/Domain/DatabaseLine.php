<?php

namespace Thinktomorrow\Squanto\Domain;

use Illuminate\Database\Eloquent\Model;
use Dimsav\Translatable\Translatable as BaseTranslatable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DatabaseLine extends Model
{
    use BaseTranslatable, Translatable;

    public $table = 'squanto_lines';
    public $translatedAttributes = ['value'];
    public $translationForeignKey = 'line_id';

    /**
     * @return DatabaseLine
     */
    public static function createFromKey(LineKey $lineKey)
    {
        $line = new static;
        $line->key = $lineKey->get();
        $line->label = $lineKey->getAsLabel();
        $line->page_id = Page::findOrCreateByKey($lineKey->getPageKey())->id;

        $line->save();

        return $line;
    }

    /**
     * @param $lineKey
     * @return DatabaseLine
     */
    public static function findOrCreateFromKey(LineKey $lineKey)
    {
        if ($line = static::findByKey($lineKey)) {
            return $line;
        }

        return static::createFromKey($lineKey);
    }

    public static function findByKey(LineKey $lineKey)
    {
        return self::where('key', $lineKey->get())->first();
    }

    public function saveValues(array $pairs)
    {
        // Dimsav expects inserting mutliple translations into the database with using the locale as array key.
        $this->fill(array_map(function($value){ return ['value' => $value]; }, $pairs));
        $this->save();

        return $this;
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
        return (false !== strpos($this->allowed_html, '<p>'));
    }

    /**
     * Get key - value pairs for all lines per locale
     *
     * @param $locale
     * @return mixed
     */
    public static function getValuesByLocale(string $locale): array
    {
        return self::getValuesByLocaleAndPage($locale);
    }

    public static function getValuesByLocaleAndPage(string $locale, ?PageKey $pagekey): array
    {
        // Since the dimsav translatable model trait injects its behaviour and overwrites our results
        // with the current locale, we will need to fetch results straight from the db instead.
        $lines = DB::table('squanto_lines')
            ->join('squanto_line_translations', 'squanto_lines.id', '=', 'squanto_line_translations.line_id')
            ->select(['squanto_lines.*','squanto_line_translations.locale','squanto_line_translations.value'])
            ->where('squanto_line_translations.locale', $locale);

        if ($pagekey) {
            $lines = $lines
                ->join('squanto_pages', 'squanto_lines.page_id', '=', 'squanto_pages.id')
                ->where('squanto_pages.key', $pagekey->get());
        }

        $lines = $lines->get();

        return $lines->pluck('value', 'key')->toArray();
    }
}
