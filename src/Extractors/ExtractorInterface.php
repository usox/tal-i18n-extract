<?php

namespace Usox\TalI18nExtract\Extractors;

use DOMXPath;
use Traversable;

interface ExtractorInterface
{
    /**
     * @return Traversable<string>
     */
    public function extract(DOMXPath $xpath): Traversable;
}
