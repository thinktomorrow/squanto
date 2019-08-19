<?php

namespace Thinktomorrow\Squanto\Domain;

use Thinktomorrow\Squanto\Exceptions\InvalidFileReference;

class LangFile
{
    /** @var string */
    private $filepath;

    /** @var array */
    private $values;

    private function __construct(string $filepath, LangValues $values = null)
    {
        if(!is_file($filepath) || !file_exists($filepath)){
            throw new InvalidFileReference('Filepath ['.$filepath . '] does not point to an existing or valid file.');
        }

        $this->filepath = $filepath;

        $this->values = $values ?? new LangValues(require $filepath);
    }

    public static function fromFilepath(string $filepath): self
    {
        return new static($filepath);
    }

    public function values(): LangValues
    {
        return $this->values;
    }

    public function replace(LineKey $lineKey, $value): self
    {
        return new static($this->filepath, $this->values->replace($lineKey->getWithoutPageKey(), $value));
    }

    public function save()
    {
        file_put_contents($this->filepath, "<?php\n\n return ".var_export($this->values->all(), true).";\n");
    }
}