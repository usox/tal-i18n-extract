<?php

namespace Usox\TalI18nExtract\Io;

use DOMXPath;
use Generator;

interface FileLoaderInterface
{
    /**
     * @param iterable<string> $filePaths
     *
     * @return Generator<DOMXPath>
     */
    public function read(iterable $filePaths): Generator;
}
