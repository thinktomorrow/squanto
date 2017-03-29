<?php

namespace Thinktomorrow\Squanto\Services;

use League\Flysystem\Filesystem;
use Thinktomorrow\Squanto\Domain\Line;

class CachedTranslationFile
{
    /**
     * Local filesystem. Already contains the path to our translation files
     * e.g. storage/app/trans
     *
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Write all translations to cache
     */
    public function write()
    {
        foreach (config('squanto.locales', []) as $locale) {
            $this->writeLocale($locale, Line::getValuesByLocale($locale));
        }

        return $this;
    }

    /**
     * Create new cached translation files based on database entries
     *
     * @param $locale
     * @param array $lines - flat array of key-value pairs e.g. foo.bar => 'translation of foo'
     */
    protected function writeLocale($locale, array $lines = [])
    {
        $translations = ConvertToTree::fromFlattened($lines);

        foreach ($translations as $section => $trans) {
            $this->filesystem->put(
                $locale.'/'.$section.'.php',
                "<?php\n\n return ".var_export($trans, true).";\n"
            );
        }
    }

    public function delete($locale = null)
    {
        if ($locale) {
            $this->filesystem->deleteDir($locale);
        }

        foreach ($this->filesystem->listContents() as $content) {
            if ($content['type'] == 'dir') {
                $this->filesystem->deleteDir($content['path']);
            } else {
                $this->filesystem->delete($content['path']);
            }
        }

        return $this;
    }
}
