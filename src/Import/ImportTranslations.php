<?php

namespace Thinktomorrow\Squanto\Import;

use Thinktomorrow\Squanto\Services\ImportStatistics;
use Thinktomorrow\Squanto\Services\LaravelTranslationsReader;

class ImportTranslations
{
    private $reader;
    private $overwriteProtection = true;
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

    /**
     * Import an existing entry that was put on hold due to overwrite protection
     *
     * @param $locale
     * @param $key
     * @param $value
     * @return $this
     */
    public function importOnHoldValue($locale, $key, $value)
    {
        $stats = app(ImportSingleTranslation::class)
            ->disableOverwriteProtection()
            ->import($locale,$key,$value)
            ->getStats();

        // TODO: need to test thisg

        $this->pushToStats(key($stats),reset($stats));
        $this->removeFromStats('updated_on_hold',$locale,$key);

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
     */
    private function insertOrUpdateValue($locale, $key, $value)
    {
        $stats = app(ImportSingleTranslation::class)
                    ->enableOverwriteProtection($this->overwriteProtection)
                    ->import($locale,$key,$value)
                    ->getStats();

        $this->pushToStats(key($stats),reset($stats));
    }


}
