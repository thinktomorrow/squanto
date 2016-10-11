<?php

namespace Thinktomorrow\Squanto\Handlers;

use Thinktomorrow\Squanto\Domain\Line;
use Thinktomorrow\Squanto\Services\ImportStatistics;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;

class ImportLaravelTranslations
{
    private $reader;
    private $overwriteProtection = true;
    private $enable_stats = true;
    private $dry = false;
    private $stats;
    private $excluded_files;

    public function __construct(LaravelTranslationsReader $reader, ImportStatistics $stats)
    {
        $this->reader = $reader;
        $this->stats = $stats;
        $this->excluded_files = config('squanto.excluded_files',[]);
    }

    public function dry($enable = true)
    {
        $this->dry = $enable;

        return $this;
    }

    public function import($locale)
    {
        $this->pushSingleToStats('overwrite_protection',$this->overwriteProtection);

        $translations = $this->reader->read($locale,$this->excluded_files)->flatten();

        foreach($translations as $key => $value)
        {
            $this->insertOrUpdateValue($locale, $key, $value);
        }

        return $this;
    }

    public function importSingleValue($locale, $key, $new_value)
    {
        $this->insertOrUpdateValue($locale,$key,$new_value, true);
        // TODO: change stats to reflect the change

        return $this;
    }

    public function enableOverwriteProtection($enable = true)
    {
        $this->overwriteProtection = $enable;

        return $this;
    }

    public function disableOverwriteProtection()
    {
        return $this->enableOverwriteProtection(false);
    }

    private function pushToStats($key,$value)
    {
        $this->stats->pushTranslation($key,$value);
    }

    private function pushSingleToStats($key,$value)
    {
        $this->stats->pushSingle($key,$value);
    }

    public function getStats()
    {
        return $this->stats;
    }

    /**
     * @param $locale
     * @param $key
     * @param $value
     * @param bool $force ignore overwriteProtection and disable stats
     */
    private function insertOrUpdateValue($locale, $key, $value, $force = false)
    {
        $overwriteProtection = $force ? false : $this->overwriteProtection;
        $enable_stats = $force ? false : $this->enable_stats;

        if(!$line = Line::findByKey($key))
        {
            if(!$this->dry) Line::make($key)->saveValue($locale, $value);

            if($enable_stats)
            {
                $this->pushToStats('inserts', [
                    'key'            => $key,
                    'locale'         => $locale,
                    'new_value'      => $value,
                    'original_value' => null,
                ]);
            }

            return;
        }

        $translation = $line->getValue($locale, false);

        $stat = [
            'id'             => $line->id,
            'key'            => $key,
            'locale'         => $locale,
            'new_value'      => $value,
            'original_value' => $translation,
        ];

        // Ignore the translation if it has remained the same
        if($translation === $value)
        {
            if($enable_stats) $this->pushToStats('remained_same', $stat);
            return;
        }

        if (!is_null($translation) && $overwriteProtection)
        {
            if($enable_stats) $this->pushToStats('updates_on_hold', $stat);
            return;
        }

        if(!$this->dry) $line->saveValue($locale, $value);

        if($enable_stats)
        {
            ($translation) ? $this->pushToStats('updates', $stat) : $this->pushToStats('inserts', $stat);
        }
    }


}
