<?php

namespace Thinktomorrow\Squanto\Import;

use Thinktomorrow\Squanto\Domain\Line;

class ImportSingleTranslation
{
    private $overwriteProtection = true;
    private $dry = false;

    /**
     * key: actiontype, value: item array
     * e.g. ['inserts' => ['key' => 'foo.bar', 'new_value' => 'baz']]
     *
     * @var array
     */
    private $stats = [];

    /**
     * @param $locale
     * @param $key
     * @param $value
     * @return $this
     */
    public function import($locale, $key, $value)
    {
        if($line = Line::findByKey($key))
        {
            return $this->updateTranslation($line,$locale,$key,$value);
        }

        return $this->insertTranslation($locale,$key,$value);
    }

    /**
     * @param $locale
     * @param $key
     * @param $value
     * @return $this
     */
    private function insertTranslation($locale, $key, $value)
    {
        if (!$this->dry) {
            Line::findOrCreateByKey($key)->saveValue($locale, $value);
        }

        $this->setStats('inserts', $locale, $key, $value);

        return $this;
    }

    /**
     * @param Line $line
     * @param $locale
     * @param $key
     * @param $value
     * @return $this
     */
    private function updateTranslation(Line $line, $locale, $key, $value)
    {
        $translation = $line->getValue($locale, false);

        // Ignore the translation if it has remained the same
        if ($translation === $value) {
            $this->setStats('remained_same', $locale, $key, $value, $translation);
            return $this;
        }

        if (!is_null($translation) && $this->overwriteProtection) {
            $this->setStats('updates_on_hold', $locale, $key, $value, $translation);
            return $this;
        }

        if ($translation)
        {
            if (!$this->dry) $line->saveValue($locale, $value);
            $this->setStats('updates', $locale, $key, $value, $translation);

            return $this;
        }

        return $this->insertTranslation($locale,$key,$value);
    }

    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param $action
     * @param $locale
     * @param $key
     * @param $new_value
     * @param null $original_value
     */
    private function setStats($action, $locale, $key, $new_value, $original_value = null)
    {
        $this->stats = [$action => new Entry($locale,$key,$new_value,$original_value)];
    }

    /**
     * @param bool $enable
     * @return $this
     */
    public function enableOverwriteProtection($enable = true)
    {
        $this->overwriteProtection = !!$enable;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableOverwriteProtection()
    {
        return $this->enableOverwriteProtection(false);
    }

    /**
     * @param bool $enable
     * @return $this
     */
    public function dry($enable = true)
    {
        $this->dry = !!$enable;
        return $this;
    }


}